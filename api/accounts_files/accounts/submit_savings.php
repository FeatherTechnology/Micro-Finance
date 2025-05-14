<?php
require "../../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$aadhar_number = $_POST['aadhar_number'];
$cus_id = $_POST['cus_id'];
$savings_amnt = $_POST['savings_amnt'];
$cat_type = $_POST['cat_type'];
$current_date_time = date('Y-m-d');

$qry = $pdo->query("INSERT INTO `customer_savings`( `cus_id`, `aadhar_num`, `savings_amount`, `credit_debit`, `insert_login_id`,`paid_date`) VALUES ('$cus_id','$aadhar_number','$savings_amnt','$cat_type','$user_id','$current_date_time')");
if ($qry) {
    $result = 1;
} else {
    $result = 2;
}

echo json_encode($result);
