<?php
require "../../ajaxconfig.php";

$loan_id = $_POST['loan_id']; // Get the loan ID from the AJAX request
$response = array();

// SQL query to fetch issue details
$query = "SELECT
lcm.id,
              cc.cus_id,
              cc.first_name,
              cc.mobile1, anc.areaname 
          FROM loan_cus_mapping lcm
          JOIN customer_creation cc ON lcm.cus_id = cc.id
        LEFT JOIN area_name_creation anc ON cc.area = anc.id
          WHERE lcm.loan_id = '$loan_id' GROUP BY cc.cus_id";

// Execute the query
$result = $pdo->query($query);

if ($result->rowCount() > 0) {
    // Fetch results and build response
    $response = $result->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($response);  // Return the response as JSON
