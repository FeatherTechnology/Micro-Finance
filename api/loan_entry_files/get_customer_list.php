<?php
require "../../ajaxconfig.php";
$result = array();

// Query to get customers who are either not in loan_cus_mapping or have multiple_loan = 1
$qry = $pdo->query("
    SELECT cc.id, cc.cus_id, cc.first_name, cc.last_name, cc.mobile1, anc.areaname 
    FROM customer_creation cc 
    LEFT JOIN area_name_creation anc ON cc.area = anc.id 
    LEFT JOIN loan_cus_mapping lcm ON cc.id = lcm.cus_id 
    WHERE lcm.cus_id IS NULL OR cc.multiple_loan = 1
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close connection

echo json_encode($result);
?>
