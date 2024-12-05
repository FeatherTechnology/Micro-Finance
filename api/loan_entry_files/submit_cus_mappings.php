<?php
require "../../ajaxconfig.php";
@session_start();

$response = ['result' => 2]; // Default to failure

// Check if required POST parameters are set
if (isset($_POST['add_customer'], $_POST['loan_id_calc'], $_POST['customer_mapping']) && !empty($_POST['add_customer']) && !empty($_POST['loan_id_calc'])) {
    
    $user_id = $_SESSION['user_id'];
    $add_customer = intval($_POST['add_customer']); // Ensuring add_customer is an integer
    $loan_id_calc = $pdo->quote($_POST['loan_id_calc']); // Properly quoting for SQL injection protection
    $customer_mapping = intval($_POST['customer_mapping']); // Assuming customer_mapping is also an integer
    $total_members = intval($_POST['total_members']);
// Check the current count of customer mappings for the group
$stmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping WHERE loan_id = $loan_id_calc");
$current_count = $stmt->fetchColumn();

// Add the new group to the mapping
if ($current_count < $total_members) {
    // Insert query
    $qry = $pdo->query("INSERT INTO loan_cus_mapping (loan_id, cus_id, customer_mapping, inserted_login_id, created_on) VALUES ($loan_id_calc, $add_customer, $customer_mapping, '$user_id', NOW())");

    // Check if the query was successful
    if ($qry) {
        $response['result'] = 1; // Success
    } else {
        $response['result'] = 2; // Failure
    }
}
} else {
    // Customer mapping limit exceeded
    $response = ['result' => 3, 'message' => 'Customer Mapping Limit is Exceeded'];
} 

// Close connection
$pdo = null;

echo json_encode($response);
