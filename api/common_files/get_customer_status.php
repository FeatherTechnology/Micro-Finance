<?php
require '../../ajaxconfig.php';
@session_start();

class CustomerStatus
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function custStatus($cus_mapping_id,$loan_id)
    {
        // Escape loan_id to prevent SQL injection
        $cus_mapping_id = $this->pdo->quote($cus_mapping_id);
        $loan_id = $this->pdo->quote($loan_id);

        // Fetch collection data
        $collection_query = $this->pdo->query("SELECT * FROM `collection` WHERE cus_mapping_id = $cus_mapping_id");
        $total_paid = 0;
        while ($coll_row = $collection_query->fetch()) {
            $total_paid += intval($coll_row['due_amt_track']);
        }

        $query = "SELECT
        c.cus_mapping_id,
        SUM(c.total_paid_track) AS total_paid_track,
        SUM(c.fine_charge_track) AS total_fine_charge_track,
        SUM(c.penalty_track) AS total_penalty_track,
        (SUM(c.total_paid_track) + SUM(c.fine_charge_track) + SUM(c.penalty_track)) AS total_paid,
        IFNULL(f.total_fine_charge, 0) AS total_fine_charge,
        IFNULL(p.total_penalty, 0) AS total_penalty,
        (SUM(c.total_paid_track) + IFNULL(f.total_fine_charge, 0) + IFNULL(p.total_penalty, 0)) AS payable_charge,
        lelc.due_amount_calc,lelc.total_customer,lelc.due_month,lelc.due_start,lelc.due_end
        FROM
        collection c
        LEFT JOIN
        (SELECT cus_mapping_id, SUM(fine_charge) AS total_fine_charge FROM fine_charges GROUP BY cus_mapping_id) f
        ON f.cus_mapping_id = c.cus_mapping_id
        LEFT JOIN
        (SELECT cus_mapping_id, SUM(penalty) AS total_penalty FROM penalty_charges GROUP BY cus_mapping_id) p
        ON p.cus_mapping_id = c.cus_mapping_id
        LEFT JOIN
        loan_entry_loan_calculation lelc
        ON lelc.loan_id = c.loan_id
        WHERE
        c.cus_mapping_id = $cus_mapping_id and c.loan_id =  $loan_id 
        GROUP BY
        c.cus_mapping_id ";

        $result = $this->pdo->query($query);

        $overall_status = 'Payable'; // Default overall status


        if ($result->rowCount() > 0) {
            $customers = $result->fetchAll(PDO::FETCH_ASSOC);
            $all_paid = true; // Initialize $all_paid variable
            foreach ($customers as &$row) {
                $row['pending'] = 0;
                $row['payable'] = 0;

                if (!empty($row['due_amount_calc']) && $row['total_customer'] > 0) {
                    $row['individual_amount'] = $row['due_amount_calc'] / $row['total_customer'];
                } else {
                    $row['individual_amount'] = 0;
                }

               
                $current_date = date('Y-m-d');
                if ($row['due_month'] == 1) {
                    $checkcollection = $this->pdo->query("
                        SELECT SUM(due_amt_track) as totalPaidAmt,SUM(fine_charge_track) as fine_charge,SUM(penalty_track) as penalty_track
                        FROM collection
                        WHERE cus_mapping_id = $cus_mapping_id
                        AND ((YEAR(coll_date) = YEAR('$current_date') AND MONTH(coll_date) <= MONTH('$current_date'))
                        OR (YEAR(coll_date) < YEAR('$current_date')))
                    ");
                } else {
                    
                    $checkcollection = $this->pdo->query("
                        SELECT SUM(due_amt_track) as totalPaidAmt,SUM(fine_charge_track) as fine_charge,SUM(penalty_track) as penalty_track
                        FROM collection
                        WHERE cus_mapping_id = $cus_mapping_id
                        AND ((YEAR(coll_date) = YEAR('$current_date') AND WEEK(coll_date) <= WEEK('$current_date'))
                        OR (YEAR(coll_date) < YEAR('$current_date')))
                    ");
                }
                $checkrow = $checkcollection->fetch();
                $totalPaidAmt = $checkrow['totalPaidAmt'] ?? 0;
                $fine = $checkrow['fine_charge'] ?? 0;
                $penalty_track = $checkrow['penalty_track'] ?? 0;
                // Fine and penalty calculations
                $fine_charge = $this->getFineCharge($cus_mapping_id, $fine, $penalty_track);
                $penalty = $this->getPenalty($cus_mapping_id, $fine, $penalty_track);
                // Calculate and update status based on the due period (monthly/weekly)
                $status = $this->calculateStatus($row, $totalPaidAmt, $fine_charge, $penalty);

            }
        }

        return $status;
    }

    private function getFineCharge($cus_mapping_id, $fine)
    {
        // Escape loan_id for safety
        $fine_query = $this->pdo->query("SELECT SUM(fine_charge) as fine_charge FROM fine_charges WHERE cus_mapping_id = $cus_mapping_id AND  fine_date <= CURDATE() ");
        $fine_row = $fine_query->fetch();
        $fine_cal = $fine_row['fine_charge'] ?? 0;
        $final_fine = $fine_cal - $fine;
        return $final_fine;
    }

    private function getPenalty($cus_mapping_id, $penalty_track)
    {
        $penalty_query = $this->pdo->query("SELECT SUM(penalty) as penalty FROM penalty_charges WHERE cus_mapping_id = $cus_mapping_id AND penalty_date <= CURDATE()");
        $penalty_row = $penalty_query->fetch();
        $pen_cal = $penalty_row['penalty'] ?? 0;
        $final_penalty = $pen_cal - $penalty_track;
        return $final_penalty;
    }

    private function calculateStatus($row, $totalPaidAmt, $fine_charge, $penalty)
    {
        $currentDate = new DateTime();
        $status = 'Payable'; // Default status
        // Monthly calculation
        if ($row['due_month'] == 1) {
            $due_start_from = date('Y-m', strtotime($row['due_start']));
            $due_end_from = date('Y-m-d', strtotime($row['due_end']));
            $current_month = date('Y-m');
            $start_date_obj = DateTime::createFromFormat('Y-m', $due_start_from);
            $end_date_obj = DateTime::createFromFormat('Y-m-d', $due_end_from);
            $current_date_obj = DateTime::createFromFormat('Y-m', $current_month);
            $monthsElapsed = $start_date_obj->diff($current_date_obj)->m + ($start_date_obj->diff($current_date_obj)->y * 12) + 1;
            if ($monthsElapsed > 1) {
                $toPayTillNow = $monthsElapsed * $row['individual_amount'];
                $toPayTillPrev = ($monthsElapsed - 1) * $row['individual_amount'];
                $pending = $toPayTillPrev - $totalPaidAmt;
                if ($totalPaidAmt >= $toPayTillNow) {
                    $status = 'Paid';
                } else if ($toPayTillPrev == $totalPaidAmt) {
                    $status = 'Payable';
                } else if ($current_date_obj > $end_date_obj) {
                    $status = 'OD';
                } else {
                    // Check for outstanding or pending status
                    if ($pending > 0 || $fine_charge > 0 || $penalty > 0) {
                        $status = 'Pending';
                    } else {
                        $status = 'Payable';
                    }
                }
            } else {
                $toPayTillNow = $monthsElapsed * $row['individual_amount'];
                if ($totalPaidAmt >= $toPayTillNow) {
                    $status = 'Paid';
                } else {
                    $status = 'Payable';
                }
            }
        }
        // Weekly calculation
        elseif ($row['due_month'] == 2) {
            $due_start_from = date('Y-m-d', strtotime($row['due_start']));
            $due_end_from = date('Y-m-d', strtotime($row['due_end']));
            $current_date = date('Y-m-d');
            $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
            $end_date_obj = DateTime::createFromFormat('Y-m-d', $due_end_from);
            $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);
            $weeksElapsed = floor($start_date_obj->diff($current_date_obj)->days / 7) + 1;
            
    
            if ($weeksElapsed > 1) {
                // Calculate amount to be paid till now and till the previous week
                $toPayTillNow = $weeksElapsed * $row['individual_amount'];
             
                $toPayTillPrev = ($weeksElapsed - 1) * $row['individual_amount'];
                $pending = $toPayTillPrev - $totalPaidAmt;
                if ($totalPaidAmt >= $toPayTillNow) {
                    $status = 'Paid';
                } else if ($toPayTillPrev == $totalPaidAmt) {
                    $status = 'Payable';
                } else if ($current_date_obj > $end_date_obj) {
                    $status = 'OD';
                } else {
                    // Check for outstanding or pending status
                    if ($pending > 0 || $fine_charge > 0 || $penalty > 0) {
                        
                        $status = 'Pending';
                    } else {
                        $status = 'Payable';
                    }
                }
            } else {
                // Handle the case where only 1 week has elapsed or less
                $toPayTillNow = $weeksElapsed * $row['individual_amount'];
                if ($totalPaidAmt >= $toPayTillNow) {
                    $status = 'Paid';
                } else {
                    $status = 'Payable';
                }
            }
        }
        return $status;
    }
}
