<?php
require('../../ajaxconfig.php');
$id=$_POST['id'];
$qry = $pdo->query("SELECT id, centre_no,centre_name,mobile1 FROM `centre_creation` Where centre_id ='$id' ");
$result = $qry->fetchAll(PDO::FETCH_ASSOC);
$pdo = null; //Close connection.

echo json_encode($result);