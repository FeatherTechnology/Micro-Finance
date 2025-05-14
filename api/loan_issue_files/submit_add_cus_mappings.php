<?php
require '../../ajaxconfig.php';
@session_start();

$response = ['result' => 2]; // Default to failure

$customer_mapping_array = [1 => 'New', 2 => 'Renewal', 3 => 'Additional'];

$user_id = $_SESSION['user_id'];
$add_customer = intval($_POST['add_customer']); // Ensuring add_customer is an integer
$centre_id = $_POST['centre_id']; // Direct use of the variable (no quoting needed)
$loan_id_calc = $_POST['loan_id_calc']; // Direct use of the variable (no quoting needed)
$customer_mapping = $customer_mapping_array[$_POST['customer_mapping']]; // Assuming customer_mapping is also an integer
$total_cus = intval($_POST['total_cus']);
$designation = $_POST['designation']; // No need to quote for simple string use
$intrest_amount = $_POST['intrest_amount'];
$principle = $_POST['principle'];
$net_cash = $_POST['net_cash'];
$processingfees = $_POST['processingfees'];
$documentcharge = $_POST['documentcharge'];
$due_amount = $_POST['due_amount'];
$customer_amount = $_POST['customer_amount'];

// Check if the customer is already mapped to the same loan_id
$stmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping lcm WHERE lcm.cus_id = '$add_customer' AND lcm.centre_id = '$centre_id' and loan_id = '$loan_id_calc'");
$existing_mapping = $stmt->fetchColumn();

if ($existing_mapping > 0) {
    // Customer is already mapped to the same loan_id, send warning
    $response = ['result' => 4, 'message' => 'The customer is already mapped to this loan.'];
} else {
    // Insert the new customer mapping
    $qry = $pdo->query("INSERT INTO loan_cus_mapping (loan_id, centre_id, cus_id, net_cash , customer_mapping, loan_amount , `intrest_amount`, `principle_amount`, `due_amount`,  designation, inserted_login_id, created_on) VALUES ('$loan_id_calc', '$centre_id', '$add_customer','$net_cash','$customer_mapping','$customer_amount','$intrest_amount','$principle ',' $due_amount',  '$designation', '$user_id', NOW())");
}

if ($customer_mapping == 'Renewal') {
    // Check if any loan has a status between 1 and 7 (inclusive)
    $stmt = $pdo->query("SELECT lcm.id FROM loan_cus_mapping lcm LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id WHERE lelc.loan_status >= 1 AND lelc.loan_status < 8 AND lcm.cus_id = '$add_customer' AND lcm.loan_id != '$loan_id_calc'");

    $rowCount = $stmt->rowCount();

    if ($rowCount == 0) {
        $pdo->query("UPDATE customer_creation SET cus_data = 'Existing', cus_status = 'Renewal' WHERE id = '$add_customer'");
    } else {
        $pdo->query("UPDATE customer_creation SET cus_data = 'Existing', cus_status = 'Additional' WHERE id = '$add_customer'");
    }
}

$pdo = null;

echo json_encode($response);
