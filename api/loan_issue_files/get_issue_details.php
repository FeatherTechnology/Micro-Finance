<?php
require "../../ajaxconfig.php";

$loan_id = $_POST['loan_id']; // Get the loan ID from the AJAX request
$response = array();

// SQL query to fetch issue details
$query = "SELECT
              lcm.id,
              lcm.loan_id,
              cc.cus_id,
              lcm.issue_status,
              cc.first_name,
              lelc.net_cash_calc,
              lelc.total_customer,
              lelc.loan_date,
               COALESCE(SUM(ls.issue_amount), 0) as issued_amount
          FROM loan_cus_mapping lcm
          JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
          JOIN customer_creation cc ON lcm.cus_id = cc.id
          LEFT JOIN loan_issue ls ON lcm.id = ls.cus_mapping_id
          WHERE lcm.loan_id = '$loan_id' GROUP BY cc.cus_id";

// Execute the query
$result = $pdo->query($query);

if ($result->rowCount() > 0) {
    // Fetch results and build response
    $response = $result->fetchAll(PDO::FETCH_ASSOC);
    
    // Loop through each row and calculate individual_amount
    foreach ($response as &$row) {
        // Calculate individual_amount if total_customer is not zero
        if (!empty($row['net_cash_calc']) && !empty($row['total_customer']) && $row['total_customer'] > 0) {
            $row['individual_amount'] = $row['net_cash_calc'] / $row['total_customer'];
        } else {
            $row['individual_amount'] = 0; // If total_customer is 0 or net_cash_calc is empty, set to 0
        }
    }
}

echo json_encode($response);  // Return the response as JSON
