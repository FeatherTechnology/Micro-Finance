<?php
require '../../ajaxconfig.php';

$search_list_arr = array();

$aadhar_number = isset($_POST['aadhar_no']) ? $_POST['aadhar_no'] : '';
$cus_name = isset($_POST['cus_name']) ? $_POST['cus_name'] : '';
$centre_no = isset($_POST['centre_no']) ? $_POST['centre_no'] : '';
$centre_name = isset($_POST['centre_name']) ? $_POST['centre_name'] : '';
$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
$area = isset($_POST['area']) ? $_POST['area'] : '';

// Initialize the query with the common part
// $sql = "SELECT cc.aadhar_number, cc.first_name,cc.mobile1,cc.cus_id,cc.id, anc.areaname AS area, bc.branch_name,crc.centre_name,crc.centre_id,centre_no,lcm.loan_id FROM loan_entry_loan_calculation lelc LEFT JOIN loan_cus_mapping lcm ON lcm.loan_id = lelc.loan_id LEFT JOIN centre_creation crc ON lelc.centre_id = crc.centre_id LEFT JOIN customer_creation cc ON lcm.cus_id =cc.id LEFT JOIN area_name_creation anc ON cc.area = anc.id LEFT JOIN area_creation ac ON anc.id = ac.id LEFT JOIN branch_creation bc ON ac.branch_id = bc.id INNER JOIN (SELECT MAX(centre_id) as max_id FROM centre_creation GROUP BY centre_id) latest ON crc.centre_id = latest.max_id  WHERE 1=1";
$sql = "SELECT cc.aadhar_number, cc.first_name,cc.mobile1,cc.cus_id,cc.id, anc.areaname AS area, bc.branch_name,crc.centre_name,crc.centre_id,centre_no,lcm.loan_id
        FROM  loan_entry_loan_calculation lelc 
        LEFT JOIN loan_cus_mapping lcm ON lcm.loan_id = lelc.loan_id
         LEFT JOIN centre_creation crc ON lelc.centre_id = crc.centre_id
      LEFT JOIN customer_creation cc ON lcm.cus_id =cc.id
        LEFT JOIN area_name_creation anc ON cc.area = anc.id
        LEFT JOIN area_creation ac ON anc.id = ac.id
        LEFT JOIN branch_creation bc ON ac.branch_id = bc.id 
        WHERE 1=1";

// Create an array to hold the conditions
$conditions = [];
$parameters = [];

// Add conditions based on priority
if (!empty($aadhar_number)) {
    $conditions[] = "cc.aadhar_number LIKE :aadhar_no";
    $parameters[':aadhar_no'] = '%' . $aadhar_number . '%';
}
if (!empty($cus_name)) {
    $conditions[] = "cc.first_name LIKE :cus_name";
    $parameters[':cus_name'] = '%' . $cus_name . '%';
}
if (!empty($centre_name)) {
    $conditions[] = "crc.centre_name LIKE :centre_name";
    $parameters[':centre_name'] = '%' . $centre_name . '%';
}
if (!empty($centre_no)) {
    $conditions[] = "crc.centre_no LIKE :centre_no";
    $parameters[':centre_no'] = '%' . $centre_no . '%';
}
if (!empty($mobile)) {
    $conditions[] = "cc.mobile1 LIKE :mobile";
    $parameters[':mobile'] = '%' . $mobile . '%';
}
if (!empty($area)) {
    $conditions[] = "anc.areaname LIKE :area";
    $parameters[':area'] = '%' . $area . '%';
}

// Apply the conditions based on priority
if (count($conditions) > 0) {
    $sql .= " AND (" . implode(" OR ", $conditions) . ")GROUP by loan_id";
}

// $sql .= " ORDER BY cc.cus_id DESC";
$stmt = $pdo->prepare($sql);


foreach ($parameters as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}


$stmt->execute();


if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['action'] = "<button class='btn btn-primary view_centre_customer' centre_id='" . $row['centre_id'] . "'>View</button>";

        $search_list_arr[] = $row; 
    }
}

$pdo = null; // Close Connection

echo json_encode($search_list_arr);
?>
