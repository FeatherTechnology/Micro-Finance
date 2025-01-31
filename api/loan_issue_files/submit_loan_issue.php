<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];
$currentDate = date('Y-m-d');

// Get the loan details and loan issue data from the POST request
$loan_id = isset($_POST['loan_id']) ? $_POST['loan_id'] : '';
$loan_amnt_calc = isset($_POST['loan_amnt_calc']) ? $_POST['loan_amnt_calc'] : '';
$loan_date = DateTime::createFromFormat('d/m/Y', $_POST['loan_date'])->format('Y-m-d');
$due_startdate_calc = isset($_POST['due_startdate_calc']) ? $_POST['due_startdate_calc'] : '';
$maturity_date_calc = isset($_POST['maturity_date_calc']) ? $_POST['maturity_date_calc'] : '';
$total_amnt_calc = isset($_POST['total_amnt_calc']) ? (int) $_POST['total_amnt_calc'] : '';
$loan_issue_data = isset($_POST['loan_issue_data']) ? $_POST['loan_issue_data'] : [];

$query= $pdo->query("SELECT `total_customer`,`due_amount_calc` FROM `loan_entry_loan_calculation` WHERE `loan_id`='$loan_id'");
$result = $query->fetch(PDO::FETCH_ASSOC);
$total_cus = isset($result['total_customer']) ? (int) $result['total_customer'] : 0;
$due_amount_calc = isset($result['due_amount_calc']) ? (float) $result['due_amount_calc']:0;


$payable=round($due_amount_calc / $total_cus);
$indudual_amount=round($total_amnt_calc/$total_cus);
$result = 0;  // Default result

// Loop through each loan issue data
foreach ($loan_issue_data as $issue) {
    $cus_id = $issue['cus_id'];
    $cus_mapping_id = $issue['id'];
    $payment_type = $issue['payment_type'];
    $issue_type = $issue['issue_type'];
    $issue_amount = $issue['issue_amount'];
    $net_cash = $issue['net_cash'];
    $issue_date = $issue['issue_date'];
    $formatted_issue_date = DateTime::createFromFormat('d/m/Y', $issue_date)->format('Y-m-d');
    
    $qry1 = $pdo->query("INSERT INTO loan_issue 
        (cus_mapping_id, loan_id, loan_amnt, net_cash, payment_mode, issue_type, issue_amount, issue_date, insert_login_id, created_on) 
        VALUES 
        ($cus_mapping_id, '$loan_id', '$loan_amnt_calc', '$net_cash', $payment_type, $issue_type, '$issue_amount', '$formatted_issue_date', $user_id, NOW())");

    // Check and update auction_details based on total settlement
    $qry4 = $pdo->query("SELECT COALESCE(SUM(issue_amount), 0) as total_amount 
    FROM loan_issue 
    WHERE cus_mapping_id = '$cus_mapping_id'");

    $total_amount = $qry4->fetchColumn();
    if ($net_cash == $total_amount) {
        $qry2 = $pdo->query("UPDATE loan_cus_mapping SET issue_status = '1' WHERE id = $cus_mapping_id");

        $qry = $pdo->query("INSERT INTO `customer_status`( `loan_id`,`cus_id`, `cus_map_id`, `sub_status`, `payable_amount`,`balance_amount`, `insert_login_id`, `created_date`) VALUES ('$loan_id','$cus_id','$cus_mapping_id','Payable','$payable','$indudual_amount','$user_id','$currentDate')");
    }

    if ($qry1 ) {
        $result = 1;  // If both queries executed successfully
    } else {
        $result = 0;  // If any query failed
    }
}

// Update loan_entry_loan_calculation table (without prepared statement)
$qry3 = $pdo->query("UPDATE loan_entry_loan_calculation 
    SET due_start = '$due_startdate_calc', due_end = '$maturity_date_calc', loan_date = '$loan_date', update_login_id = $user_id, updated_on = NOW() 
    WHERE loan_id = '$loan_id'");

// Query to check if all customers mapped to the loan_id have issue_status as 'Issued'
$qry5 = $pdo->query("SELECT COUNT(*) as total_customers, 
                             SUM(CASE WHEN issue_status = '1' THEN 1 ELSE 0 END) as issued_customers
                      FROM loan_cus_mapping 
                      WHERE loan_id = '$loan_id'");

$result_check = $qry5->fetch(PDO::FETCH_ASSOC);

if ($result_check['total_customers'] == $result_check['issued_customers']) {
    // If all customers have 'Issued' status, update loan_status to 7 in loan_entry_loan_calculation table
    $qry6 = $pdo->query("UPDATE loan_entry_loan_calculation 
                         SET loan_status = 7,update_login_id = $user_id, updated_on = NOW()
                         WHERE loan_id = '$loan_id'");
}

$pdo = null; // Close connection

// Return the result as JSON
echo json_encode($result);
