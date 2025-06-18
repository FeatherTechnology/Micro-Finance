<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];
$qry = $pdo->query("DELETE FROM `company_document` WHERE S_no = '$id' ");
if($qry){
    $result = 0; // Deleted.
}else{
    $result = 1; // Failed.
}
$pdo = null; //Close Connection
echo json_encode($result);