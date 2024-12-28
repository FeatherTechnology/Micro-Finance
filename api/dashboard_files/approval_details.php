<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$branch_id = $_POST['branch_id'];
$currentDate = date('Y-m-d');
$currentMonth = date('m'); 
$currentYear = date('Y');
$response = array();

//Total Approval
$tot_le = "SELECT COALESCE(count(lelc.id),0) AS total_approval from loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id where lelc.loan_status >=4  AND lelc.loan_status !=5  AND lelc.loan_status !=6  AND MONTH(lelc.updated_on) = $currentMonth AND YEAR(lelc.updated_on) = $currentYear";
//Total Loan Issued
$tot_li = "SELECT COALESCE(count(lelc.id),0) AS total_issued from loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id where lelc.loan_status >=7 AND MONTH(lelc.updated_on) = $currentMonth AND YEAR(lelc.updated_on) = $currentYear ";

//Total Balance
$tot_bal = "SELECT COALESCE(count(lelc.id),0) AS total_balance from loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id where lelc.loan_status <7 AND lelc.loan_status >= 4 AND lelc.loan_status != 5 AND lelc.loan_status != 6  AND MONTH(lelc.updated_on) = $currentMonth  AND YEAR(lelc.updated_on) = $currentYear  ";

//Today Approval
$today_le = "SELECT COALESCE(count(lelc.id),0) AS today_approval from loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id where lelc.updated_on='$currentDate' AND loan_status >=4 ";

//Today Loan Issued
$today_li = "SELECT COALESCE(count(lelc.id),0) AS today_issued from loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id where lelc.updated_on = '$currentDate' AND loan_status >=7 ";

//Today Balance
$today_bal = "SELECT COALESCE(count(lelc.id),0) AS today_balance from loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id where lelc.updated_on = '$currentDate'  AND lelc.loan_status <7 AND lelc.loan_status >= 4 AND lelc.loan_status != 1 AND lelc.loan_status != 5 AND lelc.loan_status != 6   ";


if ($branch_id != '' && $branch_id != '0') {
    $tot_le .= " AND cc.branch = '$branch_id' ";
    $tot_li .= " AND cc.branch = '$branch_id' ";
    $tot_bal .= " AND cc.branch = '$branch_id' ";
    $today_le .= " AND cc.branch = '$branch_id' ";
    $today_li .= " AND cc.branch = '$branch_id' ";
    $today_bal .= " AND cc.branch = '$branch_id' ";
} else {
    $tot_le .= " AND lelc.insert_login_id = '$user_id'";
    $tot_li .= " AND lelc.insert_login_id = '$user_id'";
    $tot_bal .= " AND lelc.insert_login_id = '$user_id'";
    $today_le .= " AND lelc.insert_login_id = '$user_id'";
    $today_li .= " AND lelc.insert_login_id = '$user_id'";
    $today_bal .= " AND lelc.insert_login_id = '$user_id'";
}

$qry = $pdo->query($tot_le);
$response['total_approved'] = $qry->fetch()['total_approval'];
$qry = $pdo->query($tot_li);
$response['total_loan_issued'] = $qry->fetch()['total_issued'];
$qry = $pdo->query($tot_bal);
$response['total_approve_balance'] = $qry->fetch()['total_balance'];
$qry = $pdo->query($today_le);
$response['today_approved'] = $qry->fetch()['today_approval'];
$qry = $pdo->query($today_li);
$response['today_loan_issued'] = $qry->fetch()['today_issued'];
$qry = $pdo->query($today_bal);
$response['today_approve_balance'] = $qry->fetch()['today_balance'];

echo json_encode($response);
