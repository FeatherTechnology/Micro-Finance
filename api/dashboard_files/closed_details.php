<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$branch_id = $_POST['branch_id'];
$currentDate = date('Y-m-d');
$currentMonth = date('m'); 
$currentYear = date('Y'); 
$response = array();

//Total In Closed
$tot_le = "SELECT COALESCE(count(lelc.id),0) AS total_closed FROM loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id  WHERE lelc.loan_status >= 8  AND MONTH(lelc.updated_on) = $currentMonth
        AND YEAR(lelc.updated_on) = $currentYear ";

//Total Consider
$tot_li = "SELECT COALESCE(count(cl.id),0) AS total_consider FROM `closed_loan` cl JOIN loan_entry_loan_calculation lelc ON cl.loan_id = lelc.loan_id  JOIN centre_creation cc ON cc.centre_id = lelc.centre_id WHERE cl.closed_sub_status = 1  AND MONTH(cl.closed_date) = $currentMonth AND YEAR(cl.closed_date) = $currentYear  ";


//Total Rejected
$tot_bal = "SELECT COALESCE(count(cl.id),0) AS total_rejected FROM `closed_loan` cl JOIN loan_entry_loan_calculation lelc ON cl.loan_id = lelc.loan_id  JOIN centre_creation cc ON cc.centre_id = lelc.centre_id WHERE cl.closed_sub_status = 2  AND MONTH(cl.closed_date) = $currentMonth AND YEAR(cl.closed_date) = $currentYear   ";

//Today In Closed
$today_le = "SELECT COALESCE(count(lelc.id),0) AS today_closed FROM loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id  WHERE lelc.loan_status >= 8 AND DATE(lelc.updated_on) = $currentDate ";

//Today Consider
$today_li = "SELECT COALESCE(count(cl.id),0) AS today_consider FROM `closed_loan` cl JOIN loan_entry_loan_calculation lelc ON cl.loan_id = lelc.loan_id  JOIN centre_creation cc ON cc.centre_id = lelc.centre_id WHERE cl.closed_sub_status = 1  AND DATE(cl.closed_date) = $currentDate ";

//Today Rejected
$today_bal = "SELECT COALESCE(count(cl.id),0) AS today_rejected FROM `closed_loan` cl JOIN loan_entry_loan_calculation lelc ON cl.loan_id = lelc.loan_id  JOIN centre_creation cc ON cc.centre_id = lelc.centre_id WHERE cl.closed_sub_status = 2  AND DATE(cl.closed_date) = $currentDate ";


if ($branch_id != ''  && $branch_id != '0') {
    $tot_le .= " AND cc.branch = '$branch_id' ";
    $tot_li .= " AND cc.branch = '$branch_id' ";
    $tot_bal .= " AND cc.branch = '$branch_id' ";
    $today_le .= " AND cc.branch = '$branch_id' ";
    $today_li .= " AND cc.branch = '$branch_id' ";
    $today_bal .= " AND cc.branch = '$branch_id' ";
} else {
    $tot_le .= " AND lelc.insert_login_id = '$user_id'";
    $tot_li .= " AND cl.insert_login_id = '$user_id'";
    $tot_bal .= " AND cl.insert_login_id = '$user_id'";
    $today_le .= " AND lelc.insert_login_id = '$user_id'";
    $today_li .= " AND cl.insert_login_id = '$user_id'";
    $today_bal .= " AND cl.insert_login_id = '$user_id'";
}

$qry = $pdo->query($tot_le);
$response['total_in_closed'] = $qry->fetch()['total_closed'];
$qry = $pdo->query($tot_li);
$response['total_consider'] = $qry->fetch()['total_consider'];
$qry = $pdo->query($tot_bal);
$response['total_rejected'] = $qry->fetch()['total_rejected'];
$qry = $pdo->query($today_le);
$response['today_in_closed'] = $qry->fetch()['today_closed'];
$qry = $pdo->query($today_li);
$response['today_consider'] = $qry->fetch()['today_consider'];
$qry = $pdo->query($today_bal);
$response['today_rejected'] = $qry->fetch()['today_rejected'];

$pdo = null; //Close Connection
echo json_encode($response);
