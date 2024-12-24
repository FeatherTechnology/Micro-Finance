<?php
//Based user Mapping Info the Loan category has to show.
require '../../ajaxconfig.php';
$loan_id=$_POST['loan_id'];
$result = array();
$qry = $pdo->query("SELECT cc.first_name,cc.cus_id,cm.loan_id FROM loan_cus_mapping cm LEFT JOIN customer_creation cc ON cm.cus_id = cc.id  WHERE cm.loan_id ='$loan_id' ");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close connection.

echo json_encode($result);
