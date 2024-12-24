<?php
require "../../ajaxconfig.php";

$loan_id = $_POST['loan_id']; // Get the loan ID from the AJAX request
$response = array();

// SQL query to fetch loan details and customer mapping

$query = "SELECT
              lcm.id,
              lcm.loan_id,
              cc.cus_id,
              lelc.due_amount_calc,
              lelc.total_customer,
              cc.first_name,
              lelc.due_month, -- due_method will tell us if it's monthly or weekly
              lelc.due_start,
              lelc.due_end,
              lelc.scheme_name,
              lelc.loan_category,
              lelc.total_amount_calc,
              lcm.issue_status
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
        if (!empty($row['due_amount_calc']) && $row['total_customer'] > 0) {
            $row['individual_amount'] = $row['due_amount_calc'] / $row['total_customer'];
        } else {
            $row['individual_amount'] = 0;
        }
        if (!empty($row['total_amount_calc']) && $row['total_amount_calc'] > 0) {
            $row['overall_amount'] = $row['total_amount_calc'] / $row['total_customer'];
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

            $toPayTillNow = $monthsElapsed * $row['individual_amount'];

            $penalty = 0;
            $count = 0;
            while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) {
                $penalty_checking_date = $start_date_obj->format('Y-m-d');
                $penalty_date = $start_date_obj->format('Y-m');

                $checkcollection = $pdo->query("SELECT * FROM `collection` WHERE `cus_mapping_id` = '$cus_mapping_id' AND MONTH(coll_date) = MONTH('$penalty_checking_date') AND YEAR(coll_date) = YEAR('$penalty_checking_date')");
                $collectioncount = $checkcollection->rowCount();

                // Fetch penalty percentage
                if (empty($row['scheme_name'])) {
                    $penalty_result = $pdo->query("SELECT overdue_penalty AS overdue, penalty_type  AS penal_type  FROM loan_category_creation WHERE id = '" . $row['loan_category'] . "'");
                } else {
                    $penalty_result = $pdo->query("SELECT overdue_penalty_percent AS overdue, scheme_penalty_type AS penal_type FROM scheme WHERE id = '" . $row['scheme_name'] . "'");
                }
                $penalty_row = $penalty_result->fetch();
                $penalty_per = $penalty_row['overdue'];
                $penalty_type = $penalty_row['penal_type'];
                $count++;
                if ($totalPaidAmt < $toPayTillNow && $collectioncount == 0) {
                    $checkPenalty = $pdo->query("SELECT * FROM penalty_charges WHERE penalty_date = '$penalty_date' AND cus_mapping_id = '$cus_mapping_id'");
                    if ($checkPenalty->rowCount() == 0) {
                        if ($penalty_type == 'percent') {
                            $penalty += round(($row['individual_amount'] * $penalty_per) / 100);
                        } else {
                            $penalty += $penalty_per;
                        }
                        $qry = $pdo->query("INSERT INTO penalty_charges (cus_mapping_id, loan_id,penalty_date, penalty, created_date) VALUES ('$cus_mapping_id','$loan_id', '$penalty_date', '$penalty', CURRENT_TIMESTAMP)");
                    }
                }
                $start_date_obj->add($interval); // Move to the next month
            }
            if ($count > 0) {

                // If Due month exceeded, calculate pending amount with how many months are exceeded and subtract pre closure amount if available
                $row['pending'] = max(0, $toPayTillNow - $totalPaidAmt);

                // Fetch overall penalty paid till now
                $result = $pdo->query("SELECT SUM(penalty_track) as penalty FROM `collection` WHERE cus_mapping_id ='$cus_mapping_id'");
                $row1 = $result->fetch();
                $penaltyPaid = $row1['penalty'] ?? 0;

                // Fetch overall penalty raised till now for this customer
                $result1 = $pdo->query("SELECT SUM(penalty) as penalty FROM `penalty_charges` WHERE cus_mapping_id ='$cus_mapping_id' ");
                $row2 = $result1->fetch();

                $penaltyRaised = $row2['penalty'] ?? 0;

                // Calculate total penalty after adjusting paid and waiver amounts
                $row['penalty'] = $penaltyRaised - $penaltyPaid;

                // Calculate payable amount (due + pending amount)

                $row['payable'] = $row['pending'] + $row['individual_amount'];
                $row['total_cus_amnt'] = $row['overall_amount'] - $totalPaidAmt;
                // If payable exceeds balance, adjust to balance amount
                $response[$cus_mapping_id]['penalty'] = $penaltyRaised - $penaltyPaid;
            } else {
                // If due date hasn't been exceeded, no pending or penalty
                $row['pending'] = 0;
                $row['penalty'] = 0;

                // Payable will be the due amount minus any paid and pre-closure amounts
                $row['payable'] = $row['pending'] + $row['individual_amount'] - $totalPaidAmt;

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
            $toPayTillNow = $weeksElapsed * $row['individual_amount'];

            // Debugging logs

            $penalty = 0;
            $count = 0;

            while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) {
                $penalty_checking_date = $start_date_obj->format('Y-m-d');
                $penalty_date = $start_date_obj->format('Y-m-d');
                $checkcollection = $pdo->query("SELECT * FROM `collection` WHERE `cus_mapping_id` = '$cus_mapping_id' AND WEEK(coll_date) = WEEK('$penalty_checking_date') AND YEAR(coll_date) = YEAR('$penalty_checking_date')");
                $collectioncount = $checkcollection->rowCount();

                if (empty($row['scheme_name'])) {
                    $penalty_result = $pdo->query("SELECT overdue_penalty AS overdue, penalty_type AS penal_type FROM loan_category_creation WHERE id = '" . $row['loan_category'] . "'");
                } else {
                    $penalty_result = $pdo->query("SELECT overdue_penalty_percent AS overdue, scheme_penalty_type AS penal_type FROM scheme WHERE id = '" . $row['scheme_name'] . "'");
                }
                $penalty_row = $penalty_result->fetch();
                $penalty_per = $penalty_row['overdue'];
                $penalty_type = $penalty_row['penal_type'];
                $count++;
                if ($totalPaidAmt < $toPayTillNow && $collectioncount == 0) {
                    $checkPenalty = $pdo->query("SELECT * FROM penalty_charges WHERE penalty_date = '$penalty_date' AND cus_mapping_id = '$cus_mapping_id'");
                    if ($checkPenalty->rowCount() == 0) {
                        if ($penalty_type == 'percent') {
                            $penalty += round(($row['individual_amount'] * $penalty_per) / 100);
                        } else {
                            $penalty += $penalty_per;
                        }
                        $qry = $pdo->query("INSERT INTO penalty_charges (cus_mapping_id,loan_id, penalty_date, penalty, created_date) VALUES ('$cus_mapping_id','$loan_id', '$penalty_date', '$penalty', CURRENT_TIMESTAMP)");
                    }
                }
                $start_date_obj->add($interval); // Move to the next week
            }
      
            if ($count > 0) {
                // If Due month exceeded, calculate pending amount with how many months are exceeded and subtract pre closure amount if available
                $row['pending'] = max(0, $toPayTillNow - $totalPaidAmt);
                // Fetch the overdue penalty based on scheme or loan category
                //   echo "Pending: " . $row['pending'] . "<br>";
                // Fetch overall penalty paid till now
                $result = $pdo->query("SELECT SUM(penalty_track) as penalty FROM `collection` WHERE cus_mapping_id ='$cus_mapping_id'");
                $row1 = $result->fetch();
                $penaltyPaid = $row1['penalty'] ?? 0;

                // Fetch overall penalty raised till now for this customer
                $result1 = $pdo->query("SELECT SUM(penalty) as penalty FROM `penalty_charges` WHERE cus_mapping_id ='$cus_mapping_id' ");
                $row2 = $result1->fetch();

                $penaltyRaised = $row2['penalty'] ?? 0;

                // Calculate total penalty after adjusting paid and waiver amounts
                $row['penalty'] = $penaltyRaised - $penaltyPaid;

                // Calculate payable amount (due + pending amount)
                // $row['pending'] = $row['pending'] ?? 0;
                if ($row['pending'] > 0) {
                    $row['payable'] = $row['pending'] + $row['individual_amount'];
                }else{
                    $row['payable'] = $row['pending'] + $row['individual_amount']- $totalPaidAmt;  
                }
                $row['total_cus_amnt'] = $row['overall_amount'] - $totalPaidAmt;
            } else {
                // If due date hasn't been exceeded, no pending or penalty
                $row['pending'] = 0;
                $row['penalty'] = 0;

                // Payable will be the due amount minus any paid and pre-closure amounts
                $row['payable'] = $row['pending'] + $row['individual_amount'] - $totalPaidAmt;
                $row['total_cus_amnt'] = $row['overall_amount'] - $totalPaidAmt;
            }
        }

        // Calculate payable amount
        // $row['payable'] = $row['pending'] + $row['individual_amount'];

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
