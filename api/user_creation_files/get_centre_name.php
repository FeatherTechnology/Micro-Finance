<?php
require "../../ajaxconfig.php";
$branch_id = $_POST['branchId'];
$response =array();
$qry = $pdo->query("SELECT id, centre_name FROM `centre_creation` WHERE 1");
if ($qry->rowCount() > 0) {
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null;
echo json_encode($response);