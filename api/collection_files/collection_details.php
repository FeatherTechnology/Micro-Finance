<?php
require '../../ajaxconfig.php';

$loan_id = $_POST['loan_id'];
$response = array(); // Final array to return
$response['success'] = false; // Default to false

// Fetch total and due amounts from the loan_entry_loan_calculation table
$loan_query = $pdo->query("SELECT * FROM `loan_entry_loan_calculation` WHERE loan_id = '$loan_id'");
if ($loan_query->rowCount() > 0) {
    $loan_row = $loan_query->fetch();

    // Total Amount Calculation (If total_amount_calc is not available, use principal_amount_calc)
    $response['total_amount_calc'] = !empty($loan_row['total_amount_calc']) ? $loan_row['total_amount_calc'] : $loan_row['principal_amount_calc'];

    // Due Amount Calculation (If due_amount_calc is not available, set it as 0)
    $response['due_amount_calc'] = !empty($loan_row['due_amount_calc']) ? $loan_row['due_amount_calc'] : 0;
} else {
    // If no loan found, return an error response
    $response['message'] = "Loan not found";
    echo json_encode($response);
    exit;
}

// Fetch paid amounts from the collection table
$collection_query = $pdo->query("SELECT * FROM `collection` WHERE loan_id = '$loan_id'");
$total_paid = 0; // Total paid amount initialization

if ($collection_query->rowCount() > 0) {
    while ($coll_row = $collection_query->fetch()) {
        // Sum the paid amounts from the collection table
        $total_paid += intval($coll_row['due_amt_track']); // Sum due amounts paid for this loan
    }

    $response['total_paid'] = $total_paid;

    // Calculate the balance as (total amount - total paid)
    $response['balance'] = $response['total_amount_calc'] - $response['total_paid'];
} else {
    // If no payments found, set total paid as 0 and balance as the total amount
    $response['total_paid'] = 0;
    $response['balance'] = $response['total_amount_calc'];
}

// Fine charge calculation
$coll_charge_query = $pdo->query("SELECT SUM(fine_charge) as fine_charge FROM fine_charges WHERE loan_id = '$loan_id'");
$coll_charge_row = $coll_charge_query->fetch();

if ($coll_charge_row['fine_charge'] !== null) {
    $fine_charge = $coll_charge_row['fine_charge'];

    // Fetch fine charge already tracked in the collection table
    $paid_coll_charge_query = $pdo->query("SELECT SUM(fine_charge_track) as fine_charge_track FROM collection WHERE loan_id = '$loan_id'");
    $paid_coll_charge_row = $paid_coll_charge_query->fetch();
    $fine_charge_track = $paid_coll_charge_row['fine_charge_track'] ?? 0;

    // Calculate remaining fine charge
    $response['fine_charge'] = $fine_charge - $fine_charge_track;
} else {
    $response['fine_charge'] = 0;
}

// Penalty calculation
$result = $pdo->query("SELECT SUM(penalty_track) as penalty FROM `collection` WHERE loan_id = '$loan_id'");
$row = $result->fetch();
$penaltyPaid = $row['penalty'] ?? 0;

// Fetch overall penalty raised till now for this customer
$result1 = $pdo->query("SELECT SUM(penalty) as penalty FROM `penalty_charges` WHERE loan_id = '$loan_id'");
$row1 = $result1->fetch();
$penaltyRaised = $row1['penalty'] ?? 0;

$response['penalty'] = $penaltyRaised - $penaltyPaid;

// Query to fetch customer details mapped to the loan
 $query = "SELECT
              lcm.id AS cus_mapping_id,
              lcm.loan_id,
              cc.cus_id,
              lelc.due_amount_calc,
              lelc.total_customer,
              cc.first_name,
              lelc.due_month, -- due_month determines monthly or weekly due
              lelc.due_start,
              lelc.scheme_name,
              lelc.loan_category,
              lcm.issue_status
          FROM loan_cus_mapping lcm
          JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
          JOIN customer_creation cc ON lcm.cus_id = cc.id
          WHERE lcm.loan_id = '$loan_id'
          GROUP BY cc.cus_id";

// Execute the query
$result = $pdo->query($query);

// Variables to hold total pending and total payable amounts
$total_pending = 0;
$total_payable = 0;

if ($result->rowCount() > 0) {
    $customers = $result->fetchAll(PDO::FETCH_ASSOC);

    // Loop through each customer and calculate pending and individual amount
    foreach ($customers as &$row) {
        $row['pending'] = 0;
        $row['payable'] = 0;  // Initialize 'payable' here as well
        
        // Calculate individual_amount for each customer
        if (!empty($row['due_amount_calc']) && $row['total_customer'] > 0) {
            $row['individual_amount'] = round($row['due_amount_calc'] / $row['total_customer']);
        } else {
            $row['individual_amount'] = 0;
        }
    
        // Fetch total paid amount for this customer
        $cus_mapping_id = $row['cus_mapping_id'];
        $checkcollection = $pdo->query("SELECT SUM(due_amt_track) as totalPaidAmt FROM collection WHERE cus_mapping_id = '$cus_mapping_id'");
        $checkrow = $checkcollection->fetch();
        $totalPaidAmt = $checkrow['totalPaidAmt'] ?? 0;
    
        // Penalty calculations for monthly dues
        if ($row['due_month'] == 1) { // Monthly calculation
            $due_start_from = date('Y-m', strtotime($row['due_start']));
            $current_date = date('Y-m');
            $interval = new DateInterval('P1M'); // One month interval
    
            $start_date_obj = DateTime::createFromFormat('Y-m', $due_start_from);
            $current_date_obj = DateTime::createFromFormat('Y-m', $current_date);
    
            // Calculate months elapsed since due start
            $monthsElapsed = $start_date_obj->diff($current_date_obj)->m + ($start_date_obj->diff($current_date_obj)->y * 12)+1;
            if ($start_date_obj > $current_date_obj) {
                $monthsElapsed = 1;
            }
            if ($monthsElapsed == 1) {
                // For the first month, payable = individual_amount + totalPaidAmt
                $row['payable'] = $row['individual_amount'] - $totalPaidAmt;
              //  echo "Payable: " . $row['payable'] . "<br>";
            } elseif ($monthsElapsed >1) {
                // For subsequent months, calculate pending and add to individual_amount for payable
                $toPayTillPrev = ($monthsElapsed -1) * $row['individual_amount'];
                $toPayTillNow = $monthsElapsed * $row['individual_amount'];
                $row['pending'] = max(0, $toPayTillPrev - $totalPaidAmt);
                if ($row['pending'] > 0) {
                    $row['payable'] = max(0, $row['pending'] + $row['individual_amount']);
                }else{
                    $row['payable'] = max(0, $toPayTillNow - $totalPaidAmt);  
                }
                // Add to total pending
                $total_pending += max(0, $row['pending']);
               
            }
            
        } elseif ($row['due_month'] == 2) { // Weekly calculation
            $due_start_from = date('Y-m-d', strtotime($row['due_start']));
            $current_date = date('Y-m-d');
            $interval = new DateInterval('P1W'); // One week interval
    
            $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
            $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);
    
            $weeksElapsed = floor($start_date_obj->diff($current_date_obj)->days / 7) +1;
            if ($start_date_obj > $current_date_obj) {
                $weeksElapsed = 1;
            }
            if ($weeksElapsed == 1) {
                // For the first week, payable = individual_amount + totalPaidAmt
                $row['payable'] = $row['individual_amount'] - $totalPaidAmt;
            } elseif ($weeksElapsed > 1) {
                // For subsequent weeks, calculate pending and add to individual_amount for payable
                $toPayTillPrev = ($weeksElapsed - 1)* $row['individual_amount'];
                $toPayTillNow = ($weeksElapsed)* $row['individual_amount'];
                $toPayTillPrev = ($weeksElapsed - 1)* $row['individual_amount'];
                $row['pending'] = max(0, $toPayTillPrev - $totalPaidAmt);
                if ($row['pending'] > 0) {
                    $row['payable'] = max(0, $row['pending'] + $row['individual_amount']);
                }else{
                    $row['payable'] = max(0, $toPayTillNow - $totalPaidAmt);  
                }
                $total_pending += max(0, $row['pending']); // Add to total pending only after 1 week
            }
        }
    
        // Add individual payable to total payable
        $total_payable += max(0, $row['payable']);
    }
    
    

    // Add total pending and total payable to the response
    $response['total_pending'] = $total_pending;
    $response['total_payable'] = $total_payable;

    // Add customer data to the response
    $response['customers'] = $customers;
}

$response['success'] = true; // Set success to true after all data is fetched

// Return the final response as JSON
echo json_encode($response);
?>
