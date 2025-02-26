<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
$response = array();
$qry = $pdo->query("SELECT lelc.*, cc.centre_no,cc.centre_name,cc.mobile1,lcc.procrssing_fees_type FROM loan_entry_loan_calculation lelc  JOIN centre_creation cc ON lelc.centre_id = cc.centre_id JOIN  loan_category_creation lcc ON lelc.loan_category = lcc.id WHERE lelc.id ='$id' ");
if($qry->rowCount()>0){
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo=null;
echo json_encode($response);
?>