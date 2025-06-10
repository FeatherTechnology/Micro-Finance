<?php
require "../../ajaxconfig.php";

$loan_id = $_POST['loan_id']; // Get the loan ID from the AJAX request
$response = array();

// SQL query to fetch loan details and customer mapping

 $query = "SELECT
              lcm.id,
              lcm.loan_id,
              lcm.cus_status,
              lcm.centre_id,
              cc.cus_id,
              cc.aadhar_number,
              lelc.due_amount_calc,
              lelc.total_customer,
              cc.first_name,
              lelc.due_month, -- due_method will tell us if it's monthly or weekly
              lelc.due_start,
              lelc.due_end,
              lelc.scheme_name,
              lelc.loan_category,
              lelc.due_period,
              lcm.issue_status,
              lcm.due_amount,
              lcm.loan_amount
          FROM loan_cus_mapping lcm
          JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
          JOIN customer_creation cc ON lcm.cus_id = cc.id
          WHERE lcm.loan_id = '$loan_id'";

// Execute the query
$result = $pdo->query($query);

if ($result->rowCount() > 0) {
    $response = $result->fetchAll(PDO::FETCH_ASSOC);

    // Loop through each customer and calculate pending and individual amount
    foreach ($response as &$row) {
        $row['pending'] = 0;
        // Calculate individual_amount
        if (!empty($row['due_amount'])) {
            $row['individual_amount'] = round($row['due_amount']);

        } else {
            $row['individual_amount'] = 0;
        }
        if (!empty($row['due_amount']) && $row['due_period'] > 0) {
            $row['overall_amount'] = round($row['due_amount'] * $row['due_period']);
        } else {
            $row['overall_amount'] = 0;
        }

        // Fetch total paid amount for this customer
        $cus_mapping_id = $row['id'] ?? '';
        $checkcollection = $pdo->query("SELECT SUM(due_amt_track) as totalPaidAmt FROM collection WHERE cus_mapping_id = '$cus_mapping_id'");
        $checkrow = $checkcollection->fetch();
        $totalPaidAmt = $checkrow['totalPaidAmt'] ?? 0;

        // Penalty calculations for monthly or weekly dues
        $due_month = $row['due_month'] ?? '';
        if ($due_month == 1) { // Monthly calculation
            $due_start_from = date('Y-m', strtotime($row['due_start']));
            $due_end_from = date('Y-m-d', strtotime($row['due_end']));
            $current_date = date('Y-m');
            $interval = new DateInterval('P1M'); // One month interval

            $start_date_obj = DateTime::createFromFormat('Y-m', $due_start_from);
            $end_date_obj = DateTime::createFromFormat('Y-m-d', $due_end_from);
            $current_date_obj = DateTime::createFromFormat('Y-m', $current_date);

            $monthsElapsed = $start_date_obj->diff($current_date_obj)->m + ($start_date_obj->diff($current_date_obj)->y * 12) + 1;
            $toPayTillPrev = ($monthsElapsed - 1) * $row['individual_amount'];
            $toPayTillNow = $monthsElapsed * $row['individual_amount'];
            $count = 0;
            if ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) {
                $count++;                
            }
            if ($count > 0) {

                $row['pending'] = max(0, $toPayTillPrev - $totalPaidAmt);

                if ($row['pending'] > 0) {
                    $row['payable'] = max(0, $row['pending'] + $row['individual_amount']);
                }else{
                    $row['payable'] = max(0, $toPayTillNow - $totalPaidAmt);  
                }
                $row['total_cus_amnt'] = $row['overall_amount'] - $totalPaidAmt;
                // If payable exceeds balance, adjust to balance amount
            } else {
                // If due date hasn't been exceeded, no pending or penalty
                $row['pending'] = 0;

                // Payable will be the due amount minus any paid and pre-closure amounts
                $row['payable'] = max(0, $row['pending'] + $row['individual_amount'] - $totalPaidAmt);

                //  echo "payable: " . $row['payable'] . "<br>";
                $row['total_cus_amnt'] = $row['overall_amount'] - $totalPaidAmt;
            }
        } elseif ($due_month == 2) { // Weekly calculation
            $due_start_from = date('Y-m-d', strtotime($row['due_start']));
            $due_end_from = date('Y-m-d', strtotime($row['due_end']));
            $current_date = date('Y-m-d');
            $interval = new DateInterval('P1W'); // One week interval

            $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
            $end_date_obj = DateTime::createFromFormat('Y-m-d', $due_end_from);
            $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);
            $weeksElapsed = floor($start_date_obj->diff($current_date_obj)->days / 7) + 1;
            $toPayTillPrev = ($weeksElapsed -1) * $row['individual_amount'];
            $toPayTillNow = ($weeksElapsed) * $row['individual_amount'];

            // Debugging logs

            $count = 0;
            if ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) {
                $count++;
            }
            
            if ($count > 0) {
                // If Due month exceeded, calculate pending amount with how many months are exceeded and subtract pre closure amount if available
                $row['pending'] = max(0, $toPayTillPrev - $totalPaidAmt);
                if ($row['pending'] > 0) {
                    $row['payable'] = max(0, $row['pending'] + $row['individual_amount']);
                }else{
                    $row['payable'] = max(0, $toPayTillNow - $totalPaidAmt);  
                }
                $row['total_cus_amnt'] = $row['overall_amount'] - $totalPaidAmt;
            } else {
                // If due date hasn't been exceeded, no pending or penalty
                $row['pending'] = 0;

                // Payable will be the due amount minus any paid and pre-closure amounts
                $row['payable'] = max(0, $row['pending'] + $row['individual_amount'] - $totalPaidAmt);
                $row['total_cus_amnt'] = $row['overall_amount'] - $totalPaidAmt;
            }
        }

        // Fetch collection charges for this customer
        $coll_charge_query = $pdo->query("SELECT SUM(fine_charge) as fine_charge FROM fine_charges WHERE cus_mapping_id = '$cus_mapping_id'");
        $coll_charge_row = $coll_charge_query->fetch();

        if ($coll_charge_row['fine_charge'] !== null) {
            $fine_charge = $coll_charge_row['fine_charge'];
            $paid_coll_charge_query = $pdo->query("SELECT SUM(fine_charge_track) as fine_charge_track FROM collection WHERE cus_mapping_id = '$cus_mapping_id'");
            $paid_coll_charge_row = $paid_coll_charge_query->fetch();
            $fine_charge_track = $paid_coll_charge_row['fine_charge_track'] ?? 0;

            $row['fine_charge'] = $fine_charge - $fine_charge_track;
        } else {
            $row['fine_charge'] = 0;
        }
    }
}

echo json_encode($response);
