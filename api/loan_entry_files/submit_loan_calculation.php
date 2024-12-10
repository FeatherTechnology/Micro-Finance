<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$loan_id_calc = $_POST['loan_id_calc'];
$Centre_id = $_POST['Centre_id'];
$loan_category_calc = $_POST['loan_category_calc'];
$loan_amount_calc = $_POST['loan_amount_calc'];
$total_cus = $_POST['total_cus'];
$loan_amount_per_cus = $_POST['loan_amount_per_cus'];
$profit_type_calc = $_POST['profit_type_calc'];
$due_method_calc = $_POST['due_method_calc'];
$profit_method_calc = $_POST['profit_method_calc'];
$interest_rate_calc = $_POST['interest_rate_calc'];
$due_period_calc = $_POST['due_period_calc'];
$doc_charge_calc = $_POST['doc_charge_calc'];
$processing_fees_calc = $_POST['processing_fees_calc'];
$scheme_name_calc = $_POST['scheme_name_calc'];
$scheme_date_calc = $_POST['scheme_date_calc'];
$loan_amnt_calc = $_POST['loan_amnt_calc'];
$principal_amnt_calc = $_POST['principal_amnt_calc'];
$interest_amnt_calc = $_POST['interest_amnt_calc'];
$total_amnt_calc = $_POST['total_amnt_calc'];
$due_amnt_calc = $_POST['due_amnt_calc'];
$doc_charge_calculate = $_POST['doc_charge_calculate'];
$processing_fees_calculate = $_POST['processing_fees_calculate'];
$net_cash_calc = $_POST['net_cash_calc'];
$scheme_day_calc = $_POST['scheme_day_calc'];
$due_startdate_calc = $_POST['due_startdate_calc'];
$maturity_date_calc = $_POST['maturity_date_calc'];
$id = $_POST['id'];



// Check Customer Mapping
$mappingCountStmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping WHERE loan_id = '$loan_id_calc'");
$current_mapping_count = $mappingCountStmt->fetchColumn();

if ($current_mapping_count > $total_cus) {
    echo json_encode(['result' => 3, 'message' => 'Remove The Customer Mapping Details']);
    exit;
}

if ($id == '') {
    $qry = $pdo->query("INSERT INTO `loan_entry_loan_calculation`( `centre_id`, `loan_id`,`loan_category`, `loan_amount`, `total_customer`, `loan_amt_per_cus`, `profit_type`, `due_month`, `benefit_method`,`scheme_day_calc`, `interest_rate`, `due_period`, `doc_charge`, `processing_fees`, `scheme_name`, `scheme_date`,  `loan_amount_calc`, `principal_amount_calc`, `intrest_amount_calc`, `total_amount_calc`, `due_amount_calc`, `document_charge_cal`, `processing_fees_cal`, `net_cash_calc`, `due_start`, `due_end`, `loan_status`, `insert_login_id`,`created_on`) VALUES ('$Centre_id','$loan_id_calc','$loan_category_calc','$loan_amount_calc','$total_cus','$loan_amount_per_cus','$profit_type_calc','$due_method_calc','$profit_method_calc','$scheme_day_calc','$interest_rate_calc','$due_period_calc','$doc_charge_calc','$processing_fees_calc','$scheme_name_calc','$scheme_date_calc','$loan_amnt_calc','$principal_amnt_calc','$interest_amnt_calc','$total_amnt_calc','$due_amnt_calc','$doc_charge_calculate','$processing_fees_calculate','$net_cash_calc','$due_startdate_calc','$maturity_date_calc','1','$user_id',NOW()) ");
    $result =2;
}else{
    $qry = $pdo->query("UPDATE `loan_entry_loan_calculation` 
    SET 
        `centre_id` = '$Centre_id',
        `loan_id` = '$loan_id_calc',
        `loan_category` = '$loan_category_calc',
        `loan_amount` = '$loan_amount_calc',
        `total_customer` = '$total_cus',
        `loan_amt_per_cus` = '$loan_amount_per_cus',
        `profit_type` = '$profit_type_calc',
        `due_month` = '$due_method_calc',
        `benefit_method` = '$profit_method_calc',
        `scheme_day_calc` = '$scheme_day_calc',
        `interest_rate` = '$interest_rate_calc',
        `due_period` = '$due_period_calc',
        `doc_charge` = '$doc_charge_calc',
        `processing_fees` = '$processing_fees_calc',
        `scheme_name` = '$scheme_name_calc',
        `scheme_date` = '$scheme_date_calc',
        `loan_amount_calc` = '$loan_amnt_calc',
        `principal_amount_calc` = '$principal_amnt_calc',
        `intrest_amount_calc` = '$interest_amnt_calc',
        `total_amount_calc` = '$total_amnt_calc',
        `due_amount_calc` = '$due_amnt_calc',
        `document_charge_cal` = '$doc_charge_calculate',
        `processing_fees_cal` = '$processing_fees_calculate',
        `net_cash_calc` = '$net_cash_calc',
        `due_start` = '$due_startdate_calc',
        `due_end` = '$maturity_date_calc',
        `update_login_id` = '$user_id',
        `updated_on` = now()
    WHERE `id` = '$id'");

}
// Check if the query was successful
if ($qry) {
    // If insertion or update was successful, check if the customer mapping count is now equal to total members
    $mappingCountStmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping WHERE loan_id = '$loan_id_calc'");
    $current_mapping_count = $mappingCountStmt->fetchColumn();

    // Check if the total_month matches auction_count
    if ($current_mapping_count == $total_cus) {
        // Update status in group_creation table
        $statusUpdateStmt = $pdo->query("UPDATE loan_entry_loan_calculation SET loan_status = '2' WHERE loan_id = '$loan_id_calc'");
        if ($statusUpdateStmt) {
            $result = 1;
        } else {
            $result = 0;
        }
    } else {
        $result = 1; // Success, but mapping count not yet full
    }

    $last_id = $pdo->lastInsertId();
} else {
    $result = 0; // Failure
    $last_id = '';
}
echo json_encode(['result' => $result, 'last_id' => $last_id]);
