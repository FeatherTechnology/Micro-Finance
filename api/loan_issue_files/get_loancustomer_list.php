
<?php
require "../../ajaxconfig.php";
$loan_id = $_POST['loan_id'];
$result =array();
$qry=$pdo->query("SELECT
lcm.id,
cc.cus_id,
cc.first_name,
cc.mobile1,
anc.areaname
FROM
loan_cus_mapping lcm
JOIN
customer_creation cc ON lcm.cus_id = cc.id
LEFT JOIN area_name_creation anc ON cc.area = anc.id
WHERE
lcm.loan_id = '$loan_id'");
if($qry->rowCount()>0){
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo=null; //Close Connection.

echo json_encode($result);
?>