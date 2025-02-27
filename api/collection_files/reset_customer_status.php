<?php
require '../../ajaxconfig.php';
@session_start();

class CollectStsClass
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function updateCollectStatus($loan_id)
    {
        $currentDate = new DateTime(); // Current date for comparison

        // Escape loan_id to prevent SQL injection
        $loan_id = $this->pdo->quote($loan_id);
       
        // Fetch loan details

        $loan_query = $this->pdo->query("SELECT * FROM `loan_entry_loan_calculation` WHERE loan_id = $loan_id");
        if ($loan_query->rowCount() > 0) {
            $loan_row = $loan_query->fetch();
            $due_amount_calc = !empty($loan_row['due_amount_calc']) ? $loan_row['due_amount_calc'] : 0;
        }

        // Fetch customer mapping and scheme details
        $query = "SELECT lcm.id AS cus_mapping_id, lcm.loan_id, cc.cus_id, lelc.due_amount_calc, lelc.due_period, cc.first_name, lelc.due_month, lelc.due_start, lelc.scheme_name, lelc.loan_category, lcm.issue_status,lelc.due_end,lcm.due_amount
                  FROM loan_cus_mapping lcm 
                  JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id 
                  JOIN customer_creation cc ON lcm.cus_id = cc.id 
                  WHERE lcm.loan_id = $loan_id 
                  GROUP BY cc.cus_id";

        $result = $this->pdo->query($query);

        $overall_status = 'Payable'; // Default overall status


        if ($result->rowCount() > 0) {
            $customers = $result->fetchAll(PDO::FETCH_ASSOC);
            $all_paid = true; // Initialize $all_paid variable
            foreach ($customers as &$row) {
                $row['pending'] = 0;
                $row['payable'] = 0;

                if (!empty($row['due_amount']) > 0) {
                    $row['individual_amount'] = $row['due_amount'];
                } else {
                    $row['individual_amount'] = 0;
                }

                $cus_mapping_id = $row['cus_mapping_id'];

                // Escape cus_mapping_id for safety
                $cus_mapping_id = $this->pdo->quote($cus_mapping_id);
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
                $fine_charge = $this->getFineCharge($cus_mapping_id,$fine);
                $penalty = $this->getPenalty($cus_mapping_id,$penalty_track);
                // Calculate and update status based on the due period (monthly/weekly)
                $status = $this->calculateStatus($row, $totalPaidAmt, $fine_charge, $penalty);

                // Check if any customer has 'Pending' or 'OD' status
                if ($status == 'Pending' || $status == 'OD') {
                    $overall_status = $status; // Set overall status to the most severe state (OD or Pending)
                    $all_paid = false;         // Mark that not all customers are paid
                }
                if ($status == 'Payable') {
                    $all_paid = false;
                }
                // If any customer is not "Paid," mark $all_paid as false
                if ($status != 'Paid') {
                    $all_paid = false;
                }
            }
        

        if ($all_paid) {
            $overall_status = 'Paid';
        }
    }

        // if ($overall_status == 'Paid') {
        //     $update_query = "UPDATE loan_entry_loan_calculation SET sub_status = 2 WHERE loan_id = $loan_id";
        // } else {
        //     $update_query = "UPDATE loan_entry_loan_calculation SET sub_status = 1 WHERE loan_id = $loan_id";
        // }

        // // Execute the update query
        // $this->pdo->exec($update_query);

        return $overall_status;
    }
 
    private function getFineCharge($cus_mapping_id,$fine)
    {
        $current_date = date('Y-m-d');
        // Escape loan_id for safety
        $fine_query = $this->pdo->query("SELECT SUM(fine_charge) as fine_charge FROM fine_charges WHERE cus_mapping_id = $cus_mapping_id AND  fine_date <='$current_date' ");
        $fine_row = $fine_query->fetch();
        $fine_cal = $fine_row['fine_charge'] ?? 0;
        $final_fine = $fine_cal - $fine;
        return $final_fine;
    }

    private function getPenalty($cus_mapping_id,$penalty_track)
    {$current_date = date('Y-m-d');
        $penalty_query = $this->pdo->query("SELECT SUM(penalty) as penalty FROM penalty_charges WHERE cus_mapping_id = $cus_mapping_id AND penalty_date <= '$current_date'");
        $penalty_row = $penalty_query->fetch();
        $pen_cal = $penalty_row['penalty'] ?? 0;
        $final_penalty = $pen_cal - $penalty_track;
        return $final_penalty;
    }

    private function calculateStatus($row, $totalPaidAmt, $fine_charge, $penalty)
    {
        $currentDate = new DateTime();
        $status = 'Payable'; // Default status
    
        // Logic for calculating the status
        $status = '';
        // Monthly calculation
        if ($row['due_month'] == 1) {
            $due_start_from = date('Y-m', strtotime($row['due_start']));
            $due_end_from = date('Y-m-d', strtotime($row['due_end']));
            $current_month = date('Y-m');

            $start_date_obj = DateTime::createFromFormat('Y-m', $due_start_from);
            $end_date_obj = DateTime::createFromFormat('Y-m-d', $due_end_from);
            $current_date_obj = DateTime::createFromFormat('Y-m', $current_month);
            $monthsElapsed = $start_date_obj->diff($current_date_obj)->m + ($start_date_obj->diff($current_date_obj)->y * 12) + 1;
            if ($start_date_obj > $current_date_obj) {
                $monthsElapsed = 1;
            }
    
            if ($monthsElapsed > 1) {
                $toPayTillNow = $monthsElapsed * $row['individual_amount'];
                $toPayTillPrev = ($monthsElapsed - 1) * $row['individual_amount'];
                $pending = $toPayTillPrev - $totalPaidAmt;
                if ($totalPaidAmt >= $toPayTillNow) {
                    $status = 'Paid';
                    if ($fine_charge > 0 || $penalty > 0) {
                        $status = 'Pending';
                    }
                } else if ($toPayTillPrev == $totalPaidAmt) {
                    $status = 'Payable';
                } else if ($current_date_obj > $end_date_obj) {
                    $status = 'OD';
                } else {
                    // Check for outstanding or pending status
                    if ($pending > 0) {
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
            if ($start_date_obj > $current_date_obj) {
                $weeksElapsed =1;
            }
            if ($weeksElapsed > 1) {
                // Calculate amount to be paid till now and till the previous week
                $toPayTillNow = $weeksElapsed * $row['individual_amount'];
                $toPayTillPrev = ($weeksElapsed - 1) * $row['individual_amount'];
                $pending = $toPayTillPrev - $totalPaidAmt;

                if ($totalPaidAmt >= $toPayTillNow) {
                    $status = 'Paid';
                    // If there are fines or penalties, the status should be 'Pending' even if the amount is fully paid
                    if ($fine_charge > 0 || $penalty > 0) {
                        $status = 'Pending';
                    }
                } elseif ($toPayTillPrev == $totalPaidAmt) {
                    // If the total paid amount equals the amount due till the previous period, set status to 'Payable'
                    $status = 'Payable';
                } elseif ($current_date_obj > $end_date_obj) {
                    // If the current date is past the end date, set status to 'OD' (Overdue)
                    $status = 'OD';
                } else {
                    // Check if there is any pending amount
                    if ($pending > 0) {
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
