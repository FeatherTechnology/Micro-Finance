<?php
require "../../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$userid = $_POST['id'];
$branch = $_POST['branch'];
$no_of_bills = $_POST['no_of_bills'];
$collected_amnt = str_replace(',', '', $_POST['collected_amnt']);
$cash_type = $_POST['cash_type'];
$bank_id = $_POST['bank_id'];
$current_date_time = date('Y-m-d H:i:s');
$qry = $pdo->query("INSERT INTO `accounts_collect_entry`( `user_id`, `branch`, `coll_mode`, `bank_id`, `no_of_bills`, `collection_amnt`, `insert_login_id`, `created_on`) VALUES ('$userid','$branch','$cash_type','$bank_id','$no_of_bills','$collected_amnt','$user_id','$current_date_time')");
if ($qry) {
    $result = 1;
} else {
    $result = 2;
}
$pdo = null; //Close connection.
echo json_encode($result);
