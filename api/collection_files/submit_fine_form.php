<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$fine_loan_id = $_POST['fine_loan_id'];
$cus_id = $_POST['cus_id'];
$fine_date = date('Y-m-d', strtotime($_POST['fine_date']));
$fine_purpose = $_POST['fine_purpose'];
$fine_Amnt = $_POST['fine_Amnt'];

$qry = $pdo->query("INSERT INTO `fine_charges`( `cus_mapping_id`, `loan_id`, `fine_date`, `fine_purpose`, `fine_charge`, `status`, `insert_login_id`, `created_date`) VALUES ('$cus_id','$fine_loan_id','$fine_date','$fine_purpose','$fine_Amnt','0','$user_id', now()) ");
if ($qry) {
    $result = 1;
} else {
    $result = 2;
}

$pdo = null; //Close connection.
echo json_encode($result);
