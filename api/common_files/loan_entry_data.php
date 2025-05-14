<?php
require "../../ajaxconfig.php";

$current_loan_id = $_POST['current_loan_id'];
$current_centre_id = $_POST['current_centre_id'];

$response = array();

$qry = $pdo->query("SELECT  lelc.*, lc.loan_category , cc.centre_no, s.scheme_name,  s.due_method AS scheme_due_method, lcc.due_method AS category_due_method, cc.centre_name, cc.mobile1, lcc.procrssing_fees_type FROM loan_entry_loan_calculation lelc  JOIN centre_creation cc ON lelc.centre_id = cc.centre_id 
LEFT JOIN scheme s ON s.id = lelc.scheme_name JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id LEFT JOIN loan_category lc ON lc.id = lelc.loan_category
WHERE lelc.loan_id ='$current_loan_id' AND lelc.centre_id = '$current_centre_id'");

if($qry->rowCount()>0){
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo=null;
echo json_encode($response);
?>