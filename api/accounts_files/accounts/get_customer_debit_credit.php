<?php
require '../../../ajaxconfig.php';

$cus_id = $_POST['cus_id'];
$result = array();
$qry = $pdo->query("SELECT cus_id,SUM(CASE WHEN credit_debit = 1 THEN savings_amount ELSE 0 END) AS total_credit,SUM(CASE WHEN credit_debit = 2 THEN savings_amount ELSE 0 END) AS total_debit FROM customer_savings WHERE cus_id = '$cus_id'");
if ($qry) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}else{
    $result = 0;
}
$pdo = null; //Close connection.

echo json_encode($result);