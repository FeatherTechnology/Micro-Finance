<?php
require '../../ajaxconfig.php';

$cus_mapping_id = $_POST['cus_mapping_id'];

$qry = $pdo->query("SELECT lelc.loan_id, cuc.centre_id, cuc.centre_name, lelc.profit_type, lelc.due_month, cc.cus_id, cc.first_name 
    FROM loan_entry_loan_calculation lelc
    JOIN loan_cus_mapping lcm ON lelc.loan_id = lcm.loan_id
    JOIN customer_creation cc ON lcm.cus_id = cc.id
    JOIN centre_creation cuc ON lelc.centre_id = cuc.centre_id
    WHERE lcm.id = '$cus_mapping_id'");

$row = $qry->fetch();
$profit_type = $row['profit_type'];
$due_month = $row['due_month'];
$cus_id = $row['cus_id'];
$cus_name = $row['first_name'];
$loan_id = $row['loan_id'];
$centre_id = $row['centre_id'];
$centre_name = $row['centre_name'];

// Determine the due_month and loan_type based on profit_type and due_month
if ($profit_type == 1) {
    if ($due_month == 1) {
        $response['due_month'] = 'Monthly';
    } else if ($due_month == 2) {
        $response['due_month'] = 'Weekly';
    }
    $response['loan_type'] = 'EMI';
} else if ($profit_type == 2) {
    if ($due_month == 1) {
        $response['due_month'] = 'Monthly';
    } else if ($due_month == 2) {
        $response['due_month'] = 'Weekly';
    }
    $response['loan_type'] = 'Scheme';
}

$response['cus_id'] = $cus_id;
$response['cus_name'] = $cus_name;
$response['loan_id'] = $loan_id;
$response['centre_id'] = $centre_id;
$response['centre_name'] = $centre_name;

echo json_encode($response);
?>
