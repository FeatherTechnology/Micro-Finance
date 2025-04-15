<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$add_scheme_name = $_POST['addSchemeName'];
$scheme_due_method = $_POST['schemeDueMethod'];
$schemeBenefitMethod = $_POST['schemeBenefitMethod'];
$scheme_interest_rate_min = $_POST['schemeMinInterestRate'];
$scheme_due_period_min = $_POST['schemeMinDuePeriod'];
$scheme_overdue_penalty = $_POST['schemeOverduePenalty'];
$scheme_penalty_type = $_POST['schemePenaltyType'];
$scheme_doc_charge_min = $_POST['schemeDocChargeMin'];
$scheme_doc_charge_max = $_POST['schemeDocChargeMax'];
$scheme_processing_fee_min = $_POST['schemeProcessingFeeMin'];
$scheme_processing_fee_max = $_POST['schemeProcessingFeeMax'];
$id = $_POST['id'];

$result = '0'; //initial
if ($id != '0' && $id != '') {
    $qry = $pdo->query("UPDATE `scheme` SET `scheme_name`='$add_scheme_name',`due_method`='$scheme_due_method',`benefit_method`='$schemeBenefitMethod',`interest_rate_percent_min`='$scheme_interest_rate_min',`due_period_percent_min`='$scheme_due_period_min',`overdue_penalty_percent`='$scheme_overdue_penalty',`scheme_penalty_type`='$scheme_penalty_type',`doc_charge_min`='$scheme_doc_charge_min',`doc_charge_max`='$scheme_doc_charge_max',`processing_fee_min`='$scheme_processing_fee_min',`processing_fee_max`='$scheme_processing_fee_max',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$id'");
    if ($qry) {
        $result = '1'; //Update
    }
} else {
    $qry = $pdo->query("SELECT * FROM `scheme` WHERE REPLACE(TRIM(scheme_name), ' ', '') = REPLACE(TRIM('$add_scheme_name'), ' ', '') ");
    if ($qry->rowCount() > 0) {
        $result = [
            'status' => '3', // Error
            'scheme_id' => null
        ];
    } else {
        $qry = $pdo->query("INSERT INTO `scheme`(`scheme_name`, `due_method`, `benefit_method`, `interest_rate_percent_min`,`due_period_percent_min`, `overdue_penalty_percent`, `scheme_penalty_type`, `doc_charge_min`, `doc_charge_max`, `processing_fee_min`, `processing_fee_max`,`scheme_status`, `insert_login_id`, `created_on`) VALUES ('$add_scheme_name','$scheme_due_method','$schemeBenefitMethod','$scheme_interest_rate_min','$scheme_due_period_min','$scheme_overdue_penalty','$scheme_penalty_type','$scheme_doc_charge_min','$scheme_doc_charge_max','$scheme_processing_fee_min','$scheme_processing_fee_max','1','$user_id',now())");
        if ($qry) {
            // Get the inserted ID
            $lastInsertId = $pdo->lastInsertId();
            $result = [
                'status' => '2', // Inserted successfully
                'scheme_id' => $lastInsertId // Return the inserted scheme ID
            ];
        } else {
            $result = [
                'status' => '0', // Error
                'scheme_id' => null
            ];
        }
    }
}
echo json_encode($result);
