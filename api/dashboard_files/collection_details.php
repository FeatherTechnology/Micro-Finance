<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$branch_id = $_POST['branch_id'];
$currentDate = date('Y-m-d');
$currentMonth = date('m'); 
$currentYear = date('Y');
$response = array();

//Total Paid
$tot_paid = "SELECT COALESCE(SUM(total_paid_track),0) AS total_paid FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON lcm.centre_id =cc.centre_id WHERE 1 AND MONTH(c.created_on) = $currentMonth AND YEAR(c.created_on) = $currentYear ";

//Total Penalty
// $tot_penalty = "SELECT COALESCE(SUM(c.penalty_track),0) AS total_penalty  FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON cc.centre_id = lcm.centre_id  WHERE 1 AND MONTH(c.created_on) = $currentMonth  AND YEAR(c.created_on) = $currentYear ";

//Total Fine
$tot_fine = "SELECT COALESCE(SUM(c.fine_charge_track),0) AS total_fine FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON cc.centre_id = lcm.centre_id WHERE 1 AND MONTH(c.created_on) = $currentMonth AND YEAR(c.created_on) = $currentYear ";

//Today Paid
$today_paid = "SELECT COALESCE(SUM(total_paid_track),0) AS today_paid FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON cc.centre_id = lcm.centre_id  WHERE DATE(c.created_on) = $currentDate";

//Today Penalty
// $today_penalty = "SELECT COALESCE(SUM(c.penalty_track),0) AS today_penalty FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON cc.centre_id = lcm.centre_id WHERE DATE(c.created_on) = $currentDate ";

//Today Fine
$today_fine = "SELECT COALESCE(SUM(c.fine_charge_track),0) AS today_fine FROM `collection` c JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id JOIN centre_creation cc ON cc.centre_id = lcm.centre_id WHERE DATE(c.created_on) = $currentDate ";


if ($branch_id != '' && $branch_id != '0') {
    $tot_paid .= " AND cc.branch = '$branch_id' ";
    $tot_fine .= " AND cc.branch = '$branch_id' ";
    $today_paid .= " AND cc.branch = '$branch_id' ";
    // $today_penalty .= " AND cc.branch = '$branch_id' ";
    $today_fine .= " AND cc.branch = '$branch_id' ";
} else {
    $tot_paid .= " AND c.insert_login_id = '$user_id'";
    $tot_fine .= " AND c.insert_login_id = '$user_id'";
    $today_paid .= " AND c.insert_login_id = '$user_id'";
    // $today_penalty .= " AND c.insert_login_id = '$user_id'";
    $today_fine .= " AND c.insert_login_id = '$user_id'";
}


$qry = $pdo->query($tot_paid);
$response['total_paid'] = $qry->fetch()['total_paid'];
// $qry = $pdo->query($tot_penalty);
// $response['total_penalty'] = $qry->fetch()['total_penalty'];
$qry = $pdo->query($tot_fine);
$response['total_fine'] = $qry->fetch()['total_fine'];
$qry = $pdo->query($today_paid);
$response['today_paid'] = $qry->fetch()['today_paid'];
// $qry = $pdo->query($today_penalty);
// $response['today_penalty'] = $qry->fetch()['today_penalty'];
$qry = $pdo->query($today_fine);
$response['today_fine'] = $qry->fetch()['today_fine'];

echo json_encode($response);
