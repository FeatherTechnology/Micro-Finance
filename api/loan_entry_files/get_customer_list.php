
<?php
require "../../ajaxconfig.php";

$customer_mapping = isset($_POST['customer_mapping']) ? intval($_POST['customer_mapping']) : 0;

$query = "";

if ($customer_mapping == 1) {
    // Fetch customers who are not in loan_cus_mapping table or whose multiple_loan is 1
    $query = "SELECT cc.id, cc.cus_id, cc.first_name, cc.last_name, cc.mobile1, anc.areaname 
              FROM customer_creation cc
              LEFT JOIN area_name_creation anc ON cc.area = anc.id
              LEFT JOIN loan_cus_mapping lcm ON cc.id = lcm.cus_id
              WHERE lcm.cus_id IS NULL";
} elseif ($customer_mapping == 2) {
    // Fetch customers who are in loan_cus_mapping table with loan_status = 8
    $query = "SELECT cc.id, cc.cus_id, cc.first_name, cc.last_name, cc.mobile1, anc.areaname 
              FROM customer_creation cc
              LEFT JOIN loan_cus_mapping lcm ON cc.id = lcm.cus_id
              LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
              LEFT JOIN area_name_creation anc ON cc.area = anc.id
              WHERE lelc.loan_status IN (8,9) AND (cc.customer_status IS NULL OR cc.customer_status = '' OR cc.customer_status = 1)";
} elseif ($customer_mapping == 3) {
    // Fetch customers who are in loan_cus_mapping table (additional conditions can be applied here)
    $query = "SELECT cc.id, cc.cus_id, cc.first_name, cc.last_name, cc.mobile1, anc.areaname 
FROM customer_creation cc
INNER JOIN loan_cus_mapping lcm ON cc.id = lcm.cus_id
LEFT JOIN area_name_creation anc ON cc.area = anc.id
WHERE cc.multiple_loan = 1 
AND (cc.customer_status IS NULL OR cc.customer_status = '' OR cc.customer_status = 1)
GROUP BY cc.id;";
}

try {
    // Prepare and execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $customer_list = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch data as an associative array

    echo json_encode($customer_list); // Return the JSON-encoded data

} catch (PDOException $e) {
    // Handle any errors
    echo json_encode(['error' => $e->getMessage()]);
}
// Close connection
$pdo = null;
?>
