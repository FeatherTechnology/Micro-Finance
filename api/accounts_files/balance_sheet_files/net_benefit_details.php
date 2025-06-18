<?php
require "../../../ajaxconfig.php";

$type = $_POST['type'];
// $user_id = ($_POST['user_id'] != '') ? $userwhere = " AND insert_login_id = '" . $_POST['user_id'] . "' " : $userwhere = ''; //for user based
if ($_POST['user_id'] != '') {
    $userwhere = " AND insert_login_id = '" . $_POST['user_id'] . "' "; //for user based    
    $lelcuserswhere = " AND li.insert_login_id = '" . $_POST['user_id'] . "' "; //for user based    
} else {
    $userwhere = '';
    $lelcuserswhere = '';
}

if ($type == 'today') {
    $where = " DATE(created_on) = CURRENT_DATE $userwhere";
    $collwhere = " DATE(created_on) = CURRENT_DATE $userwhere";
    $lelcwhere = " DATE(li.issue_date) = CURRENT_DATE $lelcuserswhere";
} else if ($type == 'day') {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $where = " (DATE(created_on) >= '$from_date' && DATE(created_on) <= '$to_date' ) $userwhere ";
    $collwhere = " (DATE(created_on) >= '$from_date' && DATE(created_on) <= '$to_date' ) $userwhere ";
    $lelcwhere = " (DATE(li.issue_date) >= '$from_date' && DATE(li.issue_date) <= '$to_date' ) $lelcuserswhere ";
} else if ($type == 'month') {
    $month = date('m', strtotime($_POST['month']));
    $year = date('Y', strtotime($_POST['month']));
    $where = " (MONTH(created_on) = '$month' AND YEAR(created_on) = $year) $userwhere";
    $collwhere = " (MONTH(created_on) = '$month' AND YEAR(created_on) = $year) $userwhere";
    $lelcwhere = " (MONTH(li.issue_date) = '$month' AND YEAR(li.issue_date) = $year) $lelcuserswhere";
}

$result = array();
$qry = $pdo->query("
    SELECT
        lcm.loan_id,
        lcm.id,
        lelc.total_customer,
        (lcm.intrest_amount) AS benefit,
        ROUND((lcm.loan_amount / lelc.loan_amount_calc) * lelc.document_charge_cal) AS doc_charges,
ROUND((lcm.loan_amount / lelc.loan_amount_calc) * lelc.processing_fees_cal) AS proc_charges
    FROM
        `loan_issue` li
    JOIN
        loan_cus_mapping lcm ON li.cus_mapping_id = lcm.id
    JOIN
        loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
    WHERE
        lcm.issue_status = 1 AND $lelcwhere
    GROUP BY
        lcm.loan_id, lcm.id;
");

// Initialize variables to store the total sums
$total_benefit = 0;
$total_doc_charges = 0;
$total_proc_charges = 0;

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        $benefit = floor($row['benefit']);
        $doc_charges = $row['doc_charges'];
        $proc_charges = $row['proc_charges'];
        // Accumulate the sums
        $total_benefit += $benefit;
        $total_doc_charges += $doc_charges;
        $total_proc_charges += $proc_charges;
    }
}

$qry2 = $pdo->query("SELECT COALESCE(SUM(due_amt_track),0) AS due,  COALESCE(SUM(fine_charge_track),0) AS fine FROM `collection` WHERE $collwhere "); //Collection 
if ($qry2->rowCount() > 0) {
    $row = $qry2->fetch(PDO::FETCH_ASSOC);
    $fine = $row['fine'];
}

$qry3 = $pdo->query("SELECT COALESCE(SUM(amount),0) AS oi_dr FROM `other_transaction` WHERE trans_cat ='8' AND type = '1' AND $where "); //Other Income 
if ($qry3->rowCount() > 0) {
    $oicr = $qry3->fetch(PDO::FETCH_ASSOC)['oi_dr'];
}

$qry4 = $pdo->query("SELECT COALESCE(SUM(amount),0) AS exp_dr FROM `expenses` WHERE $where "); //Expenses 
if ($qry4->rowCount() > 0) {
    $expdr = $qry4->fetch(PDO::FETCH_ASSOC)['exp_dr'];
}

$result[0]['benefit'] = $total_benefit;
$result[0]['doc_charges'] = $total_doc_charges;
$result[0]['proc_charges']  = $total_proc_charges;
$result[0]['fine'] = $fine;
$result[0]['oicr'] = $oicr;

$result[0]['expdr']  = $expdr;

$pdo = null; //Close connection.
echo json_encode($result);
