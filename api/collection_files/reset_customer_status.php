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

        // Fetch collection data
        $collection_query = $this->pdo->query("SELECT * FROM `collection` WHERE loan_id = $loan_id");
        $total_paid = 0;
        while ($coll_row = $collection_query->fetch()) {
            $total_paid += intval($coll_row['due_amt_track']);
        }

        // Fine and penalty calculations
        $fine_charge = $this->getFineCharge($loan_id);
        $penalty = $this->getPenalty($loan_id);

        // Fetch customer mapping and scheme details
        $query = "SELECT lcm.id AS cus_mapping_id, lcm.loan_id, cc.cus_id, lelc.due_amount_calc, lelc.total_customer, cc.first_name, lelc.due_month, lelc.due_start, lelc.scheme_name, lelc.loan_category, lcm.issue_status 
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

                if (!empty($row['due_amount_calc']) && $row['total_customer'] > 0) {
                    $row['individual_amount'] = $row['due_amount_calc'] / $row['total_customer'];
                } else {
                    $row['individual_amount'] = 0;
                }

                $cus_mapping_id = $row['cus_mapping_id'];

                // Escape cus_mapping_id for safety
                $cus_mapping_id = $this->pdo->quote($cus_mapping_id);
                $checkcollection = $this->pdo->query("SELECT SUM(due_amt_track) as totalPaidAmt FROM collection WHERE cus_mapping_id = $cus_mapping_id;");
                $checkrow = $checkcollection->fetch();
                $totalPaidAmt = $checkrow['totalPaidAmt'] ?? 0;

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
        }

        if ($all_paid) {
            $overall_status = 'Paid';
        }

        if ($overall_status == 'Paid') {
            $update_query = "UPDATE loan_entry_loan_calculation SET sub_status = 2 WHERE loan_id = $loan_id";
        } else {
            $update_query = "UPDATE loan_entry_loan_calculation SET sub_status = 1 WHERE loan_id = $loan_id";
        }

        // Execute the update query
        $this->pdo->exec($update_query);

        return $overall_status;
    }

    private function getFineCharge($loan_id)
    {
        // Escape loan_id for safety
        $loan_id = $this->pdo->quote($loan_id);
        $fine_query = $this->pdo->query("SELECT SUM(fine_charge) as fine_charge FROM fine_charges WHERE loan_id = $loan_id");
        $fine_row = $fine_query->fetch();
        return $fine_row['fine_charge'] ?? 0;
    }

    private function getPenalty($loan_id)
    {
        // Escape loan_id for safety
        $loan_id = $this->pdo->quote($loan_id);
        $penalty_query = $this->pdo->query("SELECT SUM(penalty) as penalty FROM penalty_charges WHERE loan_id = $loan_id");
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
                $toPayTillPrev = ($monthsElapsed - 1) * $row['individual_amount'];
                $pending = $toPayTillNow - $totalPaidAmt;
                if ($toPayTillNow == $totalPaidAmt) {
                    $status = 'Paid';
                } else if ($toPayTillPrev == $totalPaidAmt) {
                    $status = 'Payable';
                } else {
                    // Check for outstanding or pending status
                    if ($pending > 0) {
                        $status = ($fine_charge > 0 || $penalty > 0) ? 'OD' : 'Pending';
                    } else {
                        $status = 'Pending'; // If no pending, mark as pending regardless of fine or penalty
                    }
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
                // Calculate amount to be paid till now and till the previous week
                $toPayTillNow = $weeksElapsed * $row['individual_amount'];
                $toPayTillPrev = ($weeksElapsed - 1) * $row['individual_amount'];
                $pending = $toPayTillNow - $totalPaidAmt;

                if ($toPayTillNow == $totalPaidAmt) {
                    $status = 'Paid';
                } else if ($toPayTillPrev == $totalPaidAmt) {
                    $status = 'Payable';
                } else {
                    // Check for outstanding or pending status
                    if ($pending > 0) {
                        $status = ($fine_charge > 0 || $penalty > 0) ? 'OD' : 'Pending';
                    } else {
                        $status = 'Pending'; // If no pending, mark as pending regardless of fine or penalty
                    }
                }
            } else {
                // Handle the case where only 1 week has elapsed or less
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
