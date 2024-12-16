<?php
require "../../ajaxconfig.php";

$loan_id = $_POST['loan_id'];

$result = array();
$qry = $pdo->query("SELECT coc.*,cc.first_name FROM collection_charges coc 
JOIN loan_cus_mapping lcm ON coc.cus_mapping_id = lcm.id
 JOIN customer_creation cc ON lcm.cus_id = cc.id
 WHERE coc.loan_id = '$loan_id' AND coc.coll_date != '' ");
if ($qry->rowCount() > 0) {
    while($fineInfo =  $qry->fetch(PDO::FETCH_ASSOC)){
        $fineInfo['coll_date'] = date('d-m-Y', strtotime($fineInfo['coll_date']));
        $result[] = $fineInfo;
    }
}
$pdo = null; //Close Connection.
echo json_encode($result);
