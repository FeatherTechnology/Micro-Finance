<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$branch_id = $_POST['branch_id'];
$response = array();
$currentDate = date('Y-m-d');
$currentMonth = date('m'); 
$currentYear = date('Y'); 
//Total Loan Entry
$tot_le = "SELECT COALESCE(count(lelc.id),0) AS total_entry from loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id where lelc.loan_status >1 AND MONTH(lelc.created_on) = $currentMonth
        AND YEAR(lelc.created_on) = $currentYear ";

//Total Loan Issued
$tot_li = "SELECT COALESCE(count(lelc.id),0) AS total_issued from loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id where lelc.loan_status >=7 AND MONTH(lelc.created_on) = $currentMonth AND YEAR(lelc.created_on) = $currentYear ";

//Total Balance
$tot_bal = "SELECT COALESCE(count(lelc.id),0) AS total_balance from loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id where lelc.loan_status <7 AND lelc.loan_status != 1 AND lelc.loan_status != 5 AND lelc.loan_status != 6  AND MONTH(lelc.created_on) = $currentMonth AND YEAR(lelc.created_on) = $currentYear  ";


//Today Loan Entry
$today_le = "SELECT COALESCE(count(lelc.id),0) AS today_entry from loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id where lelc.created_on='$currentDate' AND loan_status >1  ";

//Today Loan Issued
$today_li = "SELECT COALESCE(count(lelc.id),0) AS today_issued from loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id where lelc.created_on = '$currentDate' AND loan_status >=7    ";

//Today Balance
$today_bal = "  SELECT COALESCE(count(lelc.id),0) AS today_balance from loan_entry_loan_calculation lelc JOIN centre_creation cc ON cc.centre_id = lelc.centre_id where lelc.created_on = '$currentDate'  AND lelc.loan_status <7 AND lelc.loan_status != 1 AND lelc.loan_status != 5 AND lelc.loan_status != 6   ";


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
$response['total_loan_entry'] = $qry->fetch()['total_entry'];
$qry = $pdo->query($tot_li);
$response['total_loan_issued'] = $qry->fetch()['total_issued'];
$qry = $pdo->query($tot_bal);
$response['total_loan_balance'] = $qry->fetch()['total_balance'];
$qry = $pdo->query($today_le);
$response['today_loan_entry'] = $qry->fetch()['today_entry'];
$qry = $pdo->query($today_li);
$response['today_loan_issued'] = $qry->fetch()['today_issued'];
$qry = $pdo->query($today_bal);
$response['today_loan_balance'] = $qry->fetch()['today_balance'];

echo json_encode($response);
