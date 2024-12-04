<?php
require '../../ajaxconfig.php';

$aadhar_number = $_POST['aadhar_number'];
$result = array();
$qry = $pdo->query("SELECT * FROM `customer_creation` WHERE aadhar_number='$aadhar_number' ORDER BY id DESC LIMIT 1");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}else{
    $result = 'New';
}
$pdo = null; //Close connection.

echo json_encode($result);