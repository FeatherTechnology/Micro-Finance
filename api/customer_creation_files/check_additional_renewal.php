<?php
require '../../ajaxconfig.php';

$cus_id = $_POST['cus_id'];
$result = '';
$qry = $pdo->query("SELECT *
              FROM customer_creation cc
              LEFT JOIN loan_cus_mapping lcm ON cc.id = lcm.cus_id
              LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id where cc.cus_id ='$cus_id' AND lelc.loan_status = 7 AND lelc.loan_status <= 8 ");
if ($qry->rowCount() >0) {
    $result = "Additional"; //Additional
}else{
    $qry = $pdo->query("SELECT *
              FROM customer_creation cc
              LEFT JOIN loan_cus_mapping lcm ON cc.id = lcm.cus_id
              LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id where cc.cus_id ='$cus_id' AND lelc.loan_status >= 8 ");
    if($qry->rowCount()>0){
        $result = "Renewal";
    }
}
$pdo = null; //Close connection.

echo json_encode($result);