<?php
require "../../../ajaxconfig.php";

$centre_id = $_POST['centre_id']; 
$response = array();


 $query = "SELECT 
    c.centre_id,
    c.centre_no,
    c.centre_name,
    c.mobile1,
    c.mobile2,
    c.latlong,
    bc.branch_name,anc.areaname,
    cc.cus_id,
    cc.first_name,
    cc.aadhar_number,
    cc.mobile1,
    IFNULL(sums.total_credit, 0) AS total_credit,
    IFNULL(sums.total_debit, 0) AS total_debit,
    (IFNULL(sums.total_credit, 0) - IFNULL(sums.total_debit, 0)) AS balance

FROM  loan_cus_mapping lcm
LEFT JOIN centre_creation c ON c.centre_id = lcm.centre_id
LEFT JOIN customer_creation cc ON cc.id = lcm.cus_id
LEFT JOIN area_name_creation anc ON anc.id= c.area
LEFT JOIN  branch_creation bc ON bc.id = c.branch
LEFT JOIN (
    SELECT 
        cus_id,
        centre_id,
        SUM(CASE WHEN credit_debit = 1 THEN savings_amount ELSE 0 END) AS total_credit,
        SUM(CASE WHEN credit_debit = 2 THEN savings_amount ELSE 0 END) AS total_debit
    FROM customer_savings GROUP BY cus_id, centre_id
        ) sums ON sums.cus_id = cc.cus_id AND sums.centre_id = lcm.centre_id
WHERE lcm.centre_id = '$centre_id' GROUP BY  cc.id ";

// Execute the query
$result = $pdo->query($query);
$response = $result->fetchAll(PDO::FETCH_ASSOC);

$pdo = null; //Close Connection.
echo json_encode($response);
?>