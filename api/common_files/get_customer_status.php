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

    public function updateCollectStatus($cus_mapping_id)
    {
        // Escape loan_id to prevent SQL injection
        $cus_mapping_id = $this->pdo->quote($cus_mapping_id);

        // Fetch collection data
        $collection_query = $this->pdo->query("SELECT * FROM `collection` WHERE cus_mapping_id = $cus_mapping_id");
        $total_paid = 0;
        while ($coll_row = $collection_query->fetch()) {
            $total_paid += intval($coll_row['due_amt_track']);
        }

        // Fine and penalty calculations
        $fine_charge = $this->getFineCharge($cus_mapping_id);
        $penalty = $this->getPenalty($cus_mapping_id);
        // Fetch customer mapping and scheme details
        $query = "SELECT COALESCE(lelc.total_amount_calc, 0) AS total_amount_calc,
    COALESCE(lelc.total_customer, 0) AS total_customer,SUM(COALESCE(c.total_paid_track, 0) + COALESCE(c.fine_charge_track, 0) + COALESCE(c.penalty_track, 0)) AS total_paid,
    SUM(COALESCE(fc.fine_charge, 0) + COALESCE(pc.penalty, 0)) AS payable_charge,cm.id,lelc.due_month,lelc.due_start
                            from loan_cus_mapping cm
                            left join fine_charges fc On fc.cus_mapping_id = cm.id
                            left join collection c On c.cus_mapping_id =cm.id
                            left join loan_entry_loan_calculation lelc On lelc.loan_id =cm.loan_id
                            left join penalty_charges pc on pc.cus_mapping_id=cm.id WHERE cm.id=$cus_mapping_id";

        $result = $this->pdo->query($query);

        $overall_status = 'Payable'; // Default overall status
      

        if ($result->rowCount() > 0) {
            $customers = $result->fetchAll(PDO::FETCH_ASSOC);
            $all_paid = true; // Initialize $all_paid variable
            foreach ($customers as &$row) {
                $row['pending'] = 0;
                $row['payable'] = 0;

                if (!empty($row['total_amount_calc']) && $row['total_customer'] > 0) {
                    $row['individual_amount'] = $row['total_amount_calc'] / $row['total_customer'];
                } else {
                    $row['individual_amount'] = 0;
                }

                

                // Escape cus_mapping_id for safety
              
                $totalPaidAmt = $row['total_paid'] ?? 0;

                // Calculate and update status based on the due period (monthly/weekly)
                $status = $this->calculateStatus($row, $totalPaidAmt, $fine_charge, $penalty);

                // Check if any customer has 'Pending' or 'OD' status
                if ($status == 'Pending' || $status == 'OD' || $status =='Payable') {
                    $overall_status = $status; // Set overall status to the most severe state (OD or Pending)
                    $all_paid = false;         // Mark that not all customers are paid
                }
            
                // If any customer is not "Paid," mark $all_paid as false
                if ($status != 'Paid') {
                    $all_paid = false;
                }
            }
        }

        if ($all_paid) {
            $overall_status = 'Paid';
        }
      
        // if ($overall_status == 'Paid') {
        //     $update_query = "UPDATE loan_entry_loan_calculation SET sub_status = 2 WHERE loan_id = $loan_id";
        // } else {
        //     $update_query = "UPDATE loan_entry_loan_calculation SET sub_status = 1 WHERE loan_id = $loan_id";
        // }

        // Execute the update query
        // $this->pdo->exec($update_query);

        return $overall_status;
    }

    private function getFineCharge($cus_mapping_id)
    {
        // Escape loan_id for safety
        $cus_mapping_id = $this->pdo->quote($cus_mapping_id);
        $fine_query = $this->pdo->query("SELECT SUM(fine_charge) as fine_charge FROM fine_charges WHERE cus_mapping_id = $cus_mapping_id");
        $fine_row = $fine_query->fetch();
        return $fine_row['fine_charge'] ?? 0;
    }

    private function getPenalty($cus_mapping_id)
    {
        // Escape loan_id for safety
        $cus_mapping_id = $this->pdo->quote($cus_mapping_id);
        $penalty_query = $this->pdo->query("SELECT SUM(penalty) as penalty FROM penalty_charges WHERE cus_mapping_id = $cus_mapping_id");
        $penalty_row = $penalty_query->fetch();
        return $penalty_row['penalty'] ?? 0;
    }

    private function calculateStatus($row, $totalPaidAmt, $fine_charge, $penalty)
    {
        $currentDate = new DateTime();
        $status = 'Payable'; // Default status

        // Monthly calculation
        if ($row['due_month'] == 1) {
            $due_start_from = date('Y-m', strtotime($row['due_start']));
            $current_month = date('Y-m');

            $start_date_obj = DateTime::createFromFormat('Y-m', $due_start_from);
            $current_date_obj = DateTime::createFromFormat('Y-m', $current_month);
            $monthsElapsed = $start_date_obj->diff($current_date_obj)->m + ($start_date_obj->diff($current_date_obj)->y * 12) + 1;

            if ($monthsElapsed > 1) {
                $toPayTillNow = $monthsElapsed * $row['individual_amount'];
                $pending = $toPayTillNow - $totalPaidAmt;
                if ($toPayTillNow == $totalPaidAmt) {
                    $status = 'Paid';
                } else {
                    $status = 'Payable';
                }
                if ($pending > 0) {
                    $status = ($fine_charge > 0 || $penalty > 0) ? 'OD' : 'Pending';
                } else {
                    $status = ($fine_charge > 0 || $penalty > 0) ? 'OD' : 'Pending';
                }
            } else {
                $toPayTillNow = $monthsElapsed * $row['individual_amount'];
                if ($toPayTillNow == $totalPaidAmt) {
                    $status = 'Paid';
                } else {
                    $status = 'Payable';
                }
            }
        }

        // Weekly calculation
        elseif ($row['due_month'] == 2) {
            $due_start_from = date('Y-m-d', strtotime($row['due_start']));
            $current_date = date('Y-m-d');

            $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
    
            $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);
   
            $weeksElapsed = floor($start_date_obj->diff($current_date_obj)->days / 7) + 1;

            if ($weeksElapsed > 1) {
                $toPayTillNow = $weeksElapsed * $row['individual_amount'];
            
                $pending = $toPayTillNow - $totalPaidAmt;
               
                if ($toPayTillNow == $totalPaidAmt) {
                    $status = 'Paid';
                } else {
                    $status = 'Payable';
                }
                if ($pending > 0) {
                    $status = ($fine_charge > 0 || $penalty > 0) ? 'OD' : 'Pending';
                } else {
                    $status = ($fine_charge > 0 || $penalty > 0) ? 'OD' : 'Pending';
                }
            } else {
                $toPayTillNow = $weeksElapsed * $row['individual_amount'];
                if ($toPayTillNow == $totalPaidAmt) {
                    $status = 'Paid';
                } else {
                    $status = 'Payable';
                }
            }
        }
        return $status;
    }
}
