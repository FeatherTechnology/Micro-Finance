<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$id = $_POST['id'];
$loan_id = $_POST['loan_id'];
$centre_id = $_POST['centre_id'];

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
            $result=1;
            if (isset($_POST['customer_mapping_data']) && is_array($_POST['customer_mapping_data'])) {
                $customer_mapping_data = $_POST['customer_mapping_data'];
            
                foreach ($customer_mapping_data as $customer) {
                    // Check if 'cus_id' exists in each customer entry
                    if (isset($customer['cus_id'])) {
                        $cus_id = $customer['cus_id'];
                    } else {
                        // Handle missing cus_id
                        continue;
                    }
            
                    if (isset($customer['cus_mapping'])) {
                        $cus_mapping = $customer['cus_mapping'];
                    } else {
                        // Handle missing cus_mapping
                        continue;
                    }
            
                    if (isset($customer['designation'])) {
                        $designation = $customer['designation'];
                    } else {
                        // Handle missing designation
                        continue;
                    }
            
                    // Check if the customer is already mapped to the same loan_id
                    $stmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping lcm WHERE lcm.cus_id = '$cus_id' AND lcm.centre_id = '$centre_id'");
                    $existing_mapping = $stmt->fetchColumn();
            
                    if ($existing_mapping > 0) {
                        // Customer is already mapped to the same loan_id, send warning
                        $response = ['result' => 4, 'message' => 'The customer is already mapped to this loan.'];
                    } else {
                        // Insert the new customer mapping
                        $qry = $pdo->query("INSERT INTO loan_cus_mapping (loan_id, centre_id, cus_id, customer_mapping, designation, inserted_login_id, created_on) 
                                            VALUES ('$loan_id', '$centre_id', '$cus_id', '$cus_mapping', '$designation', '$user_id', NOW())");
                    }
                    if ($cus_mapping == 'Renewal') {
                        // Check if any loan has a status between 1 and 7 (inclusive)
                        $stmt = $pdo->query("SELECT lcm.id 
                                             FROM loan_cus_mapping lcm
                                             LEFT JOIN loan_entry_loan_calculation lelc 
                                             ON lcm.loan_id = lelc.loan_id 
                                             WHERE lelc.loan_status >= 1 AND lelc.loan_status < 8 and lcm.cus_id = '$cus_id'AND lcm.loan_id !='$loan_id'");
                    
                        $rowCount = $stmt->rowCount();
                    
                        if ($rowCount == 0) {
                            $pdo->query("UPDATE customer_creation 
                                         SET cus_data = 'Existing', cus_status = 'Renewal' 
                                         WHERE id = '$cus_id'");
                        } else {
                            $pdo->query("UPDATE customer_creation 
                                         SET cus_data = 'Existing', cus_status = 'Additional' 
                                         WHERE id = '$cus_id'");
                        }
                    }
                }
            } else {
                echo json_encode(['result' => 0, 'message' => 'Customer mapping data is missing or invalid']);
                exit;
            }
    }
} else {
    $result = 2; // Failure to delete mapping
}

echo json_encode($result);
?>
