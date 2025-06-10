<?php
require "../../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$centre_id = $_POST['centre_id'];
$current_date_time = date('Y-m-d');

$result = 2; // default fail

if (isset($_POST['savingsData']) ) {
    foreach ($_POST['savingsData'] as $data) {
        $cus_id = $data['cus_id'];
        $cus_name = $data['cus_name'];
        $aadhar_num = $data['aadhar_num'];
        $savings_amount = $data['savings_amount'];
        $credit_debit = $data['credit_debit'];

        $sql = "INSERT INTO customer_savings (centre_id, cus_id, cus_name, aadhar_num, savings_amount, credit_debit, insert_login_id, paid_date)
                VALUES ('$centre_id', '$cus_id', '$cus_name', '$aadhar_num', '$savings_amount', '$credit_debit', '$user_id', '$current_date_time')";

        $qry = $pdo->query($sql);
        if ($qry) $result = 1;
    }
} else {
    $cus_id = $_POST['cus_id'];
    $cus_name = $_POST['cus_name'];
    $aadhar_num = $_POST['aadhar_number'];
    $savings_amount = $_POST['savings_amnt'];
    $credit_debit = $_POST['cat_type'];

    $sql = "INSERT INTO customer_savings (centre_id, cus_id, cus_name, aadhar_num, savings_amount, credit_debit, insert_login_id, paid_date)
            VALUES ('$centre_id', '$cus_id', '$cus_name', '$aadhar_num', '$savings_amount', '$credit_debit', '$user_id', '$current_date_time')";

    $qry = $pdo->query($sql);
    if ($qry) $result = 1;
}

echo json_encode($result);
