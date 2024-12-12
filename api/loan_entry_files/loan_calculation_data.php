<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
$response = array();
$qry = $pdo->query("SELECT lelc.*, cc.centre_no,cc.centre_name,cc.mobile1 FROM loan_entry_loan_calculation lelc  JOIN centre_creation cc ON lelc.centre_id = cc.centre_id WHERE lelc.id ='$id' ");
if($qry->rowCount()>0){
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo=null;
echo json_encode($response);
?>