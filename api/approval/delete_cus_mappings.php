<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
$loan_id = $_POST['loan_id'];

// To check already mapped customer so use loan id here
$checkIdStmt = $pdo->query("SELECT COUNT(*) FROM `loan_cus_mapping` WHERE `id` = '$id' AND loan_id = '$loan_id'");
$idExists = $checkIdStmt->fetchColumn();
// Delete customer mapping
if ($idExists > 0) {
    $qry = $pdo->query("DELETE FROM `loan_cus_mapping` WHERE `id` = '$id'");
    if ($qry) {
        $result = 1;
    }else {
        $result = 0; // Deletion failed due to query error
    }
} else {
    $result = 2; // Failure to delete mapping
}

echo json_encode($result);
