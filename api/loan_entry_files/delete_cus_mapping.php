<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
$loan_id = $_POST['loan_id'];

$checkIdStmt = $pdo->query("SELECT COUNT(*) FROM `loan_cus_mapping` WHERE `id` = '$id' AND loan_id = '$loan_id'");
$idExists = $checkIdStmt->fetchColumn();
// Delete customer mapping
if ($idExists > 0) {
    $qry = $pdo->query("DELETE FROM `loan_cus_mapping` WHERE `id` = '$id'");

    if ($qry) {
    
            // Get the count of customer mappings for the group
            $mappingCountStmt = $pdo->query("SELECT COUNT(*) FROM `loan_cus_mapping` WHERE loan_id = '$loan_id'");
            $current_mapping_count = $mappingCountStmt->fetchColumn();

            // Get the total number of members for the group
            $totalMembersStmt = $pdo->query("SELECT total_customer FROM `loan_entry_loan_calculation` WHERE loan_id = '$loan_id'");
            $total_members = $totalMembersStmt->fetchColumn();

            // Update status based on the count
            if ($current_mapping_count >= $total_members) {
                // Update status to 2 if count matches or exceeds
                $statusUpdateStmt = $pdo->query("UPDATE `loan_entry_loan_calculation` SET loan_status = '2' WHERE loan_id = '$loan_id'");
            } else {
                // Update status to 1 if count does not match
                $statusUpdateStmt = $pdo->query("UPDATE `loan_entry_loan_calculation` SET loan_status = '1' WHERE loan_id = '$loan_id'");
            }

            // Check if status update was successful
            if ($statusUpdateStmt) {
                $result = 1; // Success
            } else {
                $result = 2; // Failure to update status
            }
    }
} else {
    $result = 2; // Failure to delete mapping
}

echo json_encode($result);
?>
