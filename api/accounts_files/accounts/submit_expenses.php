<?php
require "../../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$coll_mode = $_POST['coll_mode'];
$invoice_id = $_POST['invoice_id'];
$branch_name = $_POST['branch_name'];
$expenses_category = $_POST['expenses_category'];
$description = $_POST['description'];
$expenses_amnt = $_POST['expenses_amnt'];
$current_date_time = date('Y-m-d H:i:s');
$qry = $pdo->query("INSERT INTO `expenses`(`coll_mode`, `invoice_id`, `branch`, `expenses_category`, `description`, `amount`, `insert_login_id`, `created_on`) VALUES ('$coll_mode','$invoice_id','$branch_name','$expenses_category','$description','$expenses_amnt','$user_id','$current_date_time' )");

if ($qry) {
    $result = 1;
} else {
    $result = 2;
}

echo json_encode($result);
