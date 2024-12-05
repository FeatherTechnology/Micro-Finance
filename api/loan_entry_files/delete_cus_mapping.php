<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

// Get group_id for the record to be deleted
$groupIdQuery = $pdo->query("SELECT loan_id FROM `loan_cus_mapping` WHERE id = '$id'");
$groupIdResult = $groupIdQuery->fetch(PDO::FETCH_ASSOC);

if ($groupIdResult) {
    $loan_id = $groupIdResult['loan_id'];

    // Check the current status of the group
    $statusQuery = $pdo->query("SELECT status FROM `loan_entry_loan_calculation` WHERE loan_id = '$loan_id'");
    $statusResult = $statusQuery->fetch(PDO::FETCH_ASSOC);

    if ($statusResult) {
        $status = $statusResult['status'];

        if ($status == '3') {
            // Return error if status is 3
            echo json_encode(2); // Indicate that deletion is not allowed
            exit;
        }
    }
}

// Delete customer mapping
$qry = $pdo->query("DELETE FROM `loan_cus_mapping` WHERE `id` = '$id'");

if ($qry) {
    if ($groupIdResult) {
        // Get the count of customer mappings for the group
        $mappingCountStmt = $pdo->query("SELECT COUNT(*) FROM `loan_cus_mapping` WHERE loan_id = '$loan_id'");
        $current_mapping_count = $mappingCountStmt->fetchColumn();

        // Get the total number of members for the group
        $totalMembersStmt = $pdo->query("SELECT total_members FROM `loan_entry_loan_calculation` WHERE loan_id = '$loan_id'");
        $total_members = $totalMembersStmt->fetchColumn();

        // Update status based on the count
        if ($current_mapping_count >= $total_members) {
            // Update status to 2 if count matches or exceeds
            $statusUpdateStmt = $pdo->query("UPDATE `loan_entry_loan_calculation` SET status = '2' WHERE loan_id = '$loan_id'");
        } else {
            // Update status to 1 if count does not match
            $statusUpdateStmt = $pdo->query("UPDATE `loan_entry_loan_calculation` SET status = '1' WHERE loan_id = '$loan_id'");
        }

        // Check if status update was successful
        if ($statusUpdateStmt) {
            $result = 1; // Success
        } else {
            $result = 2; // Failure to update status
        }
    } else {
        $result = 1; // Success (status not found, deletion allowed)
    }
} else {
    $result = 2; // Failure to delete mapping
}

echo json_encode($result);
?>
