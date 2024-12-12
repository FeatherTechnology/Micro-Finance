<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$loan_calc_id = $_POST['loan_calc_id'];
$cus_sts = $_POST['cus_sts'];
$result = array();
$qry = $pdo->query("UPDATE `loan_entry_loan_calculation` SET `loan_status`='$cus_sts', `update_login_id`='$user_id', `updated_on`=now() WHERE `id`='$loan_calc_id' ");
if($qry){
    $result = 0;
}
$pdo=null; //Close Connection.
echo json_encode($result);
?>