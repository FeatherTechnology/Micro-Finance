<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
$qry = $pdo->query("DELETE FROM `users` WHERE id = '$id' ");
if ($qry) {
    $result = '0';
} else {
    $result = '1';
}

$pdo = null; //Close Connection
echo json_encode($result);