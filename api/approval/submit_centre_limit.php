<?php
require '../../ajaxconfig.php';

$centre_id = $_POST['centre_id'];
$centre_limit = $_POST['centre_limit'];
$lable = $_POST['lable'];
$feedback = $_POST['feedback'];
$remarks = $_POST['remarks'];

$qry = $pdo->query("UPDATE `centre_creation` SET `centre_limit`='$centre_limit',`lable`='$lable',`feedback`='$feedback',`remarks`='$remarks'WHERE centre_id='$centre_id '");
if($qry){
    $result="0";
}
else{
    $result= "1";
}
$pdo = null; //Close connection.

echo json_encode($result);