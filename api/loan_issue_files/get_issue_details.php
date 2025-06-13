<?php
require "../../ajaxconfig.php";

$loan_id = $_POST['loan_id']; // Get the loan ID from the AJAX request
$response = array();

// SQL query to fetch issue details
$query = "SELECT
              lcm.id,
              lcm.loan_id,
              lcm.centre_id,
              lcm.loan_amount,
              cc.cus_id,
              lcm.issue_status,
              cc.first_name,
              lelc.net_cash_calc,
              lelc.total_customer,
              lelc.loan_date,
              lelc.loan_status,
              lcm.net_cash,
               COALESCE(SUM(ls.issue_amount), 0) as issued_amount
          FROM loan_cus_mapping lcm
          JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
          JOIN customer_creation cc ON lcm.cus_id = cc.id
          LEFT JOIN loan_issue ls ON lcm.id = ls.cus_mapping_id
          WHERE lcm.loan_id = '$loan_id' GROUP BY cc.cus_id";

// echo $query; die;
// Execute the query
$result = $pdo->query($query);

if ($result->rowCount() > 0) {
    // Fetch results and build response
    $response = $result->fetchAll(PDO::FETCH_ASSOC);
    
    // Loop through each row and calculate individual_amount
    foreach ($response as &$row) {
        // Calculate individual_amount if total_customer is not zero
        if (!empty($row['net_cash'])  > 0) {
            $row['individual_amount'] = round($row['net_cash'] );
        } else {
            $row['individual_amount'] = 0; // If total_customer is 0 or net_cash_calc is empty, set to 0
        }
    }
}

$pdo = null; //Close Connection
echo json_encode($response);  // Return the response as JSON
