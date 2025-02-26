<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
$response = array();
$qry = $pdo->query("SELECT  lcm.id,lcm.customer_mapping, lcm.loan_amount, lcm.designation,cc.first_name  FROM loan_cus_mapping lcm left join customer_creation cc on cc.id= lcm.cus_id  WHERE lcm.id = $id");
if($qry->rowCount()>0){
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo=null;
echo json_encode($response);
?>