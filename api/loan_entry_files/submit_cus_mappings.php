<?php
require "../../ajaxconfig.php";
@session_start();

$response = ['result' => 2]; // Default to failure

// Check if required POST parameters are set
if (isset($_POST['add_customer'], $_POST['loan_id_calc'], $_POST['customer_mapping']) && !empty($_POST['add_customer']) && !empty($_POST['loan_id_calc'])) {
    
    $user_id = $_SESSION['user_id'];
    $add_customer = intval($_POST['add_customer']); // Ensuring add_customer is an integer
    $centre_id = $_POST['centre_id']; // Direct use of the variable (no quoting needed)
    $loan_id_calc = $_POST['loan_id_calc']; // Direct use of the variable (no quoting needed)
    $customer_mapping = intval($_POST['customer_mapping']); // Assuming customer_mapping is also an integer
    $total_cus = intval($_POST['total_cus']);
    $designation = $_POST['designation']; // No need to quote for simple string use

    // Check if the customer is already mapped to the same loan_id
    $stmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping lcm LEFT JOIN loan_entry_loan_calculation lelc 
                             ON lcm.loan_id = lelc.loan_id  WHERE lcm.cus_id = $add_customer AND lelc.centre_id = '$centre_id'" );
    $existing_mapping = $stmt->fetchColumn();

    if ($existing_mapping > 0) {
        // Customer is already mapped to the same loan_id, send warning
        $response = ['result' => 4, 'message' => 'The customer is already mapped to this loan.'];
    } else {
        // Check the current count of customer mappings for the loan
        $stmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping WHERE loan_id = '$loan_id_calc'");
        $current_count = $stmt->fetchColumn();

        // Add the new group to the mapping only if the current count is less than the total allowed
        if ($current_count < $total_cus) {
            // Insert query
            $qry = $pdo->query("INSERT INTO loan_cus_mapping (loan_id, cus_id, customer_mapping, designation, inserted_login_id, created_on) 
                                VALUES ('$loan_id_calc', $add_customer, $customer_mapping, '$designation', '$user_id', NOW())");

            // Check if the query was successful
            if ($qry) {
                // Check if count now equals total members
                $stmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping WHERE loan_id = '$loan_id_calc'");
                $current_count = $stmt->fetchColumn();

                if ($current_count == $total_cus) {
                    // Update the status in the loan_entry_loan_calculation table
                    $update_stmt = $pdo->query("UPDATE loan_entry_loan_calculation SET loan_status = '2' WHERE loan_id = '$loan_id_calc'");

                    if ($update_stmt) {
                        $response['result'] = 1; // Success
                    } else {
                        $response['result'] = 2; // Failure
                    }
                } else {
                    $response['result'] = 1; // Success, but not yet full
                }
            } else {
                // Failure during insertion
                $response['result'] = 2;
            }
        } else {
            // Customer mapping limit exceeded
            $response = ['result' => 3, 'message' => 'Customer Mapping Limit is Exceeded'];
        }
    }
    if ($customer_mapping == 2) {
        // Check if any loan has a status between 1 and 7 (inclusive)
        $stmt = $pdo->query("SELECT lcm.id 
                             FROM loan_cus_mapping lcm
                             LEFT JOIN loan_entry_loan_calculation lelc 
                             ON lcm.loan_id = lelc.loan_id 
                             WHERE lelc.loan_status >= 1 AND lelc.loan_status < 8 and lcm.cus_id ='$add_customer'");
                             
        $rowCount = $stmt->rowCount();

        if ($rowCount == 0) {
            $update_stmt = $pdo->query("UPDATE customer_creation 
            SET cus_data = 'Existing', cus_status = 'Renewal' 
            WHERE id = '$add_customer'");
        } else {
          
              $update_stmt = $pdo->query("UPDATE customer_creation 
              SET cus_data = 'Existing', cus_status = 'Additional' 
              WHERE id = '$add_customer'");                            
        }
    }
} else {
    $response = ['result' => 2, 'message' => 'Required parameters missing'];
}


// Close connection
$pdo = null;

echo json_encode($response);
?>
