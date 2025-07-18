<?php
require "../../../ajaxconfig.php";

$op_data = array();
$op_data[0]['hand_cash'] = 0;
$op_data[0]['bank_cash'] = 0;
$current_date = date('Y-m-d');
//Collection credit.
$c_cr_h_qry = $pdo->query("SELECT SUM(collection_amnt) AS coll_cr_amnt FROM accounts_collect_entry WHERE coll_mode = 1 AND DATE(created_on) <= '$current_date' - INTERVAL 1 DAY "); //Hand Cash
if ($c_cr_h_qry->rowCount() > 0) {
    $c_cr_h = $c_cr_h_qry->fetch()['coll_cr_amnt'];
} else {
    $c_cr_h = 0;
}

$c_cr_b_qry = $pdo->query("SELECT SUM(collection_amnt) AS coll_cr_amnt FROM accounts_collect_entry WHERE coll_mode = 2 AND DATE(created_on) <= '$current_date' - INTERVAL 1 DAY  "); //Hand Cash
if ($c_cr_b_qry->rowCount() > 0) {
    $c_cr_b = $c_cr_b_qry->fetch()['coll_cr_amnt'];
} else {
    $c_cr_b = 0;
}
// Loan Issue
$l_c_h_qry = $pdo->query("SELECT SUM(issue_amount) AS lis_cr_amnt FROM loan_issue WHERE issue_type = 1 AND DATE(created_on) <= '$current_date'- INTERVAL 1 DAY  "); //Hand Cash
if ($l_c_h_qry->rowCount() > 0) {
    $l_cr_h = $l_c_h_qry->fetch()['lis_cr_amnt'];
} else {
    $l_cr_h = 0;
}
//Expenses Debit.
$e_dr_h_qry = $pdo->query("SELECT SUM(amount) AS exp_dr_amnt FROM expenses WHERE coll_mode = 1 AND DATE(created_on) <= '$current_date' - INTERVAL 1 DAY "); //Hand Cash
if ($e_dr_h_qry->rowCount() > 0) {
    $e_dr_h = $e_dr_h_qry->fetch()['exp_dr_amnt'];
} else {
    $e_dr_h = 0;
}

$e_dr_b_qry = $pdo->query("SELECT SUM(amount) AS exp_dr_amnt FROM expenses WHERE coll_mode = 2 AND DATE(created_on) <= '$current_date' - INTERVAL 1 DAY  "); //Hand Cash
if ($e_dr_b_qry->rowCount() > 0) {
    $e_dr_b = $e_dr_b_qry->fetch()['exp_dr_amnt'];
} else {
    $e_dr_b = 0;
}

//Other Transaction Credit / Debit.
$ot_cr_h_qry = $pdo->query("SELECT SUM(amount) AS ot_amnt FROM other_transaction WHERE coll_mode = 1 AND type = 1 AND DATE(created_on) <= '$current_date' - INTERVAL 1 DAY  "); //Hand Cash //credit
if ($ot_cr_h_qry->rowCount() > 0) {
    $ot_cr_h = $ot_cr_h_qry->fetch()['ot_amnt'];
} else {
    $ot_cr_h = 0;
}

$ot_dr_h_qry = $pdo->query("SELECT SUM(amount) AS ot_amnt FROM other_transaction WHERE coll_mode = 1 AND type = 2 AND DATE(created_on) <= '$current_date' - INTERVAL 1 DAY  "); //Hand Cash //debit
if ($ot_dr_h_qry->rowCount() > 0) {
    $ot_dr_h = $ot_dr_h_qry->fetch()['ot_amnt'];
} else {
    $ot_dr_h = 0;
}

$ot_cr_b_qry = $pdo->query("SELECT SUM(amount) AS ot_amnt FROM other_transaction WHERE coll_mode = 2 AND type = 1 AND DATE(created_on) <= '$current_date' - INTERVAL 1 DAY  "); //Bank Cash //credit
if ($ot_cr_b_qry->rowCount() > 0) {
    $ot_cr_b = $ot_cr_b_qry->fetch()['ot_amnt'];
} else {
    $ot_cr_b = 0;
}

$ot_dr_b_qry = $pdo->query("SELECT SUM(amount) AS ot_amnt FROM other_transaction WHERE coll_mode = 2 AND type = 2 AND DATE(created_on) <= '$current_date' - INTERVAL 1 DAY  "); //Bank Cash //debit
if ($ot_dr_b_qry->rowCount() > 0) {
    $ot_dr_b = $ot_dr_b_qry->fetch()['ot_amnt'];
} else {
    $ot_dr_b = 0;
}


//savings Credit / Debit.
$sa_cr_h_qry  = $pdo->query("SELECT SUM(savings_amount) AS ot_amnt FROM customer_savings WHERE credit_debit = 1 AND DATE(paid_date) = '$current_date'- INTERVAL 1 DAY  "); //Hand Cash //credit
if ($sa_cr_h_qry ->rowCount() > 0) {
    $sa_cr_h = $sa_cr_h_qry->fetch()['ot_amnt'];
} else {
    $sa_cr_h = 0;
}

$sa_dr_h_qry = $pdo->query("SELECT SUM(savings_amount) AS ot_amnt FROM customer_savings WHERE  credit_debit = 2 AND DATE(paid_date) = '$current_date'- INTERVAL 1 DAY  "); //Hand Cash //debit
if ($sa_dr_h_qry ->rowCount() > 0) {
    $sa_dr_h = $sa_dr_h_qry->fetch()['ot_amnt'];
} else {
    $sa_dr_h = 0;
}

$hand_cr = intval($c_cr_h) + intval($ot_cr_h)+ intval($sa_cr_h);
$hand_dr = intval($e_dr_h) + intval($ot_dr_h) + intval($l_cr_h)+ intval($sa_dr_h);
$bank_cr = intval($c_cr_b) + intval($ot_cr_b);
$bank_dr = intval($e_dr_b) + intval($ot_dr_b);

$op_data[0]['hand_cash'] = intval($hand_cr) - intval($hand_dr);
$op_data[0]['bank_cash'] = intval($bank_cr) - intval($bank_dr);
$op_data[0]['opening_balance'] = $op_data[0]['hand_cash'] + $op_data[0]['bank_cash'];

$pdo = null; //Close connection.
echo json_encode($op_data);
