<?php
require "../../ajaxconfig.php";
$response =array();
$qry=$pdo->query("SELECT id, centre_name FROM centre_creation");

if ($qry->rowCount() > 0) {
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null;
echo json_encode($response);