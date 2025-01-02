<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$id = $_POST['loan_id'];
$loan_sts = $_POST['loan_sts'];
$current_date = date('Y-m-d ');
$result = array();
$qry = $pdo->query("UPDATE `loan_entry_loan_calculation` SET `loan_status`='$loan_sts', `update_login_id`='$user_id', `updated_on`='$current_date' WHERE `id`='$id' ");
if($qry){
    $result = 0;
}
$pdo=null; //Close Connection.
echo json_encode($result);
?>