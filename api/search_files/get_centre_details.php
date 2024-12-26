<?php
require '../../ajaxconfig.php';

$centre_id = $_POST['centre_id'];
$cus_map_arr = array();

$qry = $pdo->query("SELECT cc.cus_id , cc.first_name , cc.aadhar_number , cc.mobile1 , anc.areaname,lcm.centre_id  FROM `loan_cus_mapping` lcm 
left join customer_creation cc on cc.id = lcm.cus_id 
left join area_name_creation anc on cc.area=anc.id WHERE lcm.centre_id='$centre_id'");

if ($qry->rowCount() > 0) {
    // Fetch the results and process them
    while ($gcm_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        $cus_map_arr[] = $gcm_info;
    }
}
else {
$cus_map_arr = [];
}
$pdo = null;

echo json_encode($cus_map_arr);
?>