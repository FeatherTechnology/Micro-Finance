<?php
require '../../ajaxconfig.php';

$id = $_POST['id']; 
$qry = $pdo->query("SELECT lelc.centre_id,lelc.loan_amount,lelc.due_start,lelc.due_end, cc.centre_no,cc.centre_name, cc.mobile1,anc.areaname,bc.branch_name,lelc.loan_id
 FROM loan_entry_loan_calculation lelc 
 LEFT JOIN centre_creation cc ON lelc.centre_id = cc.centre_id
 LEFT JOIN area_name_creation anc ON anc.id = cc.area
 LEFT JOIN branch_creation bc ON bc.id = cc.branch
 LEFT JOIN closed_status cs ON cs.loan_id = lelc.loan_id
 WHERE lelc.id ='$id '");
 
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close connection.

echo json_encode($result);