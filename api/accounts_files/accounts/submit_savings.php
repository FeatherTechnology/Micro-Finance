<?php
require "../../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$coll_mode = $_POST['coll_mode'];
$invoice_id = $_POST['invoice'];
$aadhar_number = $_POST['aadhar_number'];
$cus_name = $_POST['cus_name'];
$description = $_POST['description'];
$savings_amnt = $_POST['savings_amnt'];
$cat_type = $_POST['cat_type'];
$current_date_time = date('Y-m-d H:i:s');

$qry = $pdo->query("INSERT INTO `savings`( `invoice_id`, `coll_mode`, `aadhar_number`, `cus_name`, `description`, `amount`,`cat_type`, `insert_login_id`, `created_on`) VALUES ('$invoice_id','$coll_mode','$aadhar_number','$cus_name ','$description','$savings_amnt','$cat_type','$user_id ','$current_date_time')");
if ($qry) {
    $result = 1;
} else {
    $result = 2;
}

echo json_encode($result);
