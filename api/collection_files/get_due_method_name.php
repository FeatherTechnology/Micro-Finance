<?php
require '../../ajaxconfig.php';
$loan_id = $_POST['loan_id'];

$qry = $pdo->query("SELECT profit_type, due_month FROM loan_entry_loan_calculation where loan_id = '$loan_id' ");
$row = $qry->fetch();
$profit_type = $row['profit_type'];
$due_month = $row['due_month'];

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

echo json_encode($response);
