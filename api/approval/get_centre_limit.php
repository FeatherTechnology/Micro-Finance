<?php
require '../../ajaxconfig.php';

$centre_id = $_POST['centre_id'];
$qry = $pdo->query("SELECT centre_limit,lable,feedback,remarks FROM `centre_creation` WHERE centre_id='$centre_id'");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close connection.

echo json_encode($result);