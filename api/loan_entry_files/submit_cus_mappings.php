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

            // Insert the new customer mapping
            $qry = $pdo->query("SELECT cc.id,cc.cus_id, cc.first_name, cc.aadhar_number, cc.mobile1, anc.areaname
                                FROM customer_creation cc
                                LEFT JOIN area_name_creation anc ON cc.area = anc.id 
                                WHERE cc.id = $add_customer");

            $customer_data = $qry->fetch(PDO::FETCH_ASSOC);

            if ($qry && $customer_data) {
                // Check if count now equals total members
              
                $response['result'] = 1; // Success, but not yet full
                $response['customer_data'] = $customer_data; // Send customer data back to the response
            } else {
                // Failure during insertion
                $response['result'] = 2;
            }
        

    if ($customer_mapping == 2) {
        // Check if any loan has a status between 1 and 7 (inclusive)
        $stmt = $pdo->query("SELECT lcm.id 
                             FROM loan_cus_mapping lcm
                             LEFT JOIN loan_entry_loan_calculation lelc 
                             ON lcm.loan_id = lelc.loan_id 
                             WHERE lelc.loan_status >= 1 AND lelc.loan_status < 8 and lcm.cus_id = '$add_customer'");

        $rowCount = $stmt->rowCount();

        if ($rowCount == 0) {
            $pdo->query("UPDATE customer_creation 
                         SET cus_data = 'Existing', cus_status = 'Renewal' 
                         WHERE id = '$add_customer'");
        } else {
            $pdo->query("UPDATE customer_creation 
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
