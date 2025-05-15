<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$branch_id = $_POST['branch_id'];
$currentMonth = date('m'); 
$currentYear = date('Y');
$response = array();

//Total Paid
$tot_paid_current = "SELECT COALESCE(COUNT(`sub_status`),0) AS total_paid_current, COALESCE(SUM(total_paid_track),0) AS current_paid FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON lcm.centre_id =cc.centre_id WHERE `sub_status` ='Payable' AND MONTH(c.created_on) = $currentMonth  AND YEAR(c.created_on) = $currentYear";

$tot_paid_pending = "SELECT COALESCE(COUNT(`sub_status`),0) AS total_paid_pending, COALESCE(SUM(total_paid_track),0) AS pending_paid FROM `collection` c  JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON lcm.centre_id =cc.centre_id WHERE `sub_status` ='Pending' AND MONTH(c.created_on) = $currentMonth  AND YEAR(c.created_on) = $currentYear";

$tot_paid_od = "SELECT COALESCE(COUNT(`sub_status`),0) AS total_paid_od, COALESCE(SUM(total_paid_track),0) AS od_paid FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON lcm.centre_id =cc.centre_id WHERE `sub_status` ='OD' AND MONTH(c.created_on) = $currentMonth AND YEAR(c.created_on) = $currentYear ";

// //Total Penalty
// $tot_penalty_current = "SELECT COALESCE(COUNT(`sub_status`),0) AS total_penalty_current, COALESCE(SUM(penalty_track),0) AS current_penalty FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON lcm.centre_id =cc.centre_id WHERE `sub_status` ='Payable' AND penalty_track != '' AND penalty_track != 0  AND MONTH(c.created_on) = $currentMonth  AND YEAR(c.created_on) = $currentYear";

// $tot_penalty_pending = "SELECT COALESCE(COUNT(`sub_status`),0) AS total_penalty_pending, COALESCE(SUM(penalty_track),0) AS pending_penalty FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON lcm.centre_id =cc.centre_id WHERE `sub_status` ='Pending' AND penalty_track != '' AND penalty_track != 0  AND MONTH(c.created_on) = $currentMonth AND YEAR(c.created_on) = $currentYear";

// $tot_penalty_od = "SELECT COALESCE(COUNT(`sub_status`),0) AS total_penalty_od, COALESCE(SUM(penalty_track),0) AS od_penalty FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON lcm.centre_id =cc.centre_id WHERE `sub_status` ='OD' AND penalty_track != '' AND penalty_track != 0  AND MONTH(c.created_on) = $currentMonth AND YEAR(c.created_on) = $currentYear";

//Total Fine
$tot_fine_current = "SELECT COALESCE(COUNT(`sub_status`),0) AS total_fine_current, COALESCE(SUM(fine_charge_track),0) AS current_fine FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON lcm.centre_id =cc.centre_id WHERE `sub_status` ='Payable' AND fine_charge_track !='' AND fine_charge_track != 0  AND MONTH(c.created_on) = $currentMonth AND YEAR(c.created_on) = $currentYear ";

$tot_fine_pending = "SELECT COALESCE(COUNT(`sub_status`),0) AS total_fine_pending, COALESCE(SUM(fine_charge_track),0) AS pending_fine FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON lcm.centre_id =cc.centre_id WHERE `sub_status` ='Pending' AND fine_charge_track !='' AND fine_charge_track != 0  AND MONTH(c.created_on) = $currentMonth AND YEAR(c.created_on) = $currentYear";

$tot_fine_od = "SELECT COALESCE(COUNT(`sub_status`),0) AS total_fine_od, COALESCE(SUM(fine_charge_track),0) AS od_fine FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON lcm.centre_id =cc.centre_id WHERE `sub_status` ='OD' AND fine_charge_track !='' AND fine_charge_track != 0 AND MONTH(c.created_on) = $currentMonth AND YEAR(c.created_on) = $currentYear ";

if ($branch_id != '' && $branch_id != '0') {
    $tot_paid_current .= " AND cc.branch = '$branch_id' ";
    $tot_paid_pending .= " AND cc.branch = '$branch_id' ";
    $tot_paid_od .= " AND cc.branch = '$branch_id' ";
    // $tot_penalty_current .= " AND cc.branch = '$branch_id' ";
    // $tot_penalty_pending .= " AND cc.branch = '$branch_id' ";
    // $tot_penalty_od .= " AND cc.branch = '$branch_id' ";
    $tot_fine_current .= " AND cc.branch = '$branch_id' ";
    $tot_fine_pending .= " AND cc.branch = '$branch_id' ";
    $tot_fine_od .= " AND cc.branch = '$branch_id' ";
} else {
    $tot_paid_current .= " AND c.insert_login_id = '$user_id'";
    $tot_paid_pending .= " AND c.insert_login_id = '$user_id'";
    $tot_paid_od .= " AND c.insert_login_id = '$user_id'";
    // $tot_penalty_current .= " AND c.insert_login_id = '$user_id'";
    // $tot_penalty_pending .= " AND c.insert_login_id = '$user_id'";
    // $tot_penalty_od .= " AND c.insert_login_id = '$user_id'";
    $tot_fine_current .= " AND c.insert_login_id = '$user_id'";
    $tot_fine_pending .= " AND c.insert_login_id = '$user_id'";
    $tot_fine_od .= " AND c.insert_login_id = '$user_id'";
}

$qry = $pdo->query($tot_paid_current);
$row = $qry->fetch();
$response['total_paid_current'] = $row['total_paid_current'];
$response['current_paid'] = $row['current_paid'];
$qry1 = $pdo->query($tot_paid_pending);
$row1 = $qry1->fetch();
$response['total_paid_pending'] = $row1['total_paid_pending'];
$response['pending_paid'] = $row1['pending_paid'];
$qry2 = $pdo->query($tot_paid_od);
$row2 = $qry2->fetch();
$response['total_paid_od'] = $row2['total_paid_od'];
$response['od_paid'] = $row2['od_paid'];

// $qry3 = $pdo->query($tot_penalty_current);
// $row3 = $qry3->fetch();
// $response['total_penalty_current'] = $row3['total_penalty_current'];
// $response['current_penalty'] = $row3['current_penalty'];
// $qry4 = $pdo->query($tot_penalty_pending);
// $row4 = $qry4->fetch();
// $response['total_penalty_pending'] = $row4['total_penalty_pending'];
// $response['pending_penalty'] = $row4['pending_penalty'];
// $qry5 = $pdo->query($tot_penalty_od);
// $row5 = $qry5->fetch();
// $response['total_penalty_od'] = $row5['total_penalty_od'];
// $response['od_penalty'] = $row5['od_penalty'];

$qry3 = $pdo->query($tot_fine_current);
$row3 = $qry3->fetch();
$response['total_fine_current'] = $row3['total_fine_current'];
$response['current_fine'] = $row3['current_fine'];
$qry4 = $pdo->query($tot_fine_pending);
$row4 = $qry4->fetch();
$response['total_fine_pending'] = $row4['total_fine_pending'];
$response['pending_fine'] = $row4['pending_fine'];
$qry5 = $pdo->query($tot_fine_od);
$row5 = $qry5->fetch();
$response['total_fine_od'] = $row5['total_fine_od'];
$response['od_fine'] = $row5['od_fine'];

echo json_encode($response);
