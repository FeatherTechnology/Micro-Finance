<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$loan_id_calc = $_POST['loan_id_calc'];
$Centre_id = $_POST['Centre_id'];
$loan_category_calc = $_POST['loan_category_calc'];
$loan_amount_calc = $_POST['loan_amount_calc'];
$total_cus = $_POST['total_cus'];
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
try {
    // Begin transaction
    $pdo->beginTransaction();
    // Get the latest customer ID
    $selectIC = $pdo->query("SELECT loan_id FROM loan_entry_loan_calculation WHERE loan_id != '' ORDER BY id DESC LIMIT 1 FOR UPDATE");

if ($id == '') {
    if ($selectIC->rowCount() > 0) {
        $row = $selectIC->fetch();
        $ac2 = $row["loan_id"];
        $appno2 = ltrim(strstr($ac2, '-'), '-'); // Extract numeric part after the hyphen
        $appno2 = $appno2 + 1;
        $loan_id_calc = "L-" . $appno2;
    } else {
        $loan_id_calc = "L-101"; // If no previous customer ID exists, start with C-1
    }
    $qry = $pdo->query("INSERT INTO `loan_entry_loan_calculation`( `centre_id`, `loan_id`,`loan_category`, `loan_amount`, `total_customer`, `profit_type`, `due_month`, `benefit_method`,`scheme_day_calc`, `interest_rate`, `due_period`, `doc_charge`, `processing_fees`, `scheme_name`, `scheme_date`,  `loan_amount_calc`, `principal_amount_calc`, `intrest_amount_calc`, `total_amount_calc`, `due_amount_calc`, `document_charge_cal`, `processing_fees_cal`, `net_cash_calc`, `due_start`, `due_end`, `loan_status`, `insert_login_id`,`created_on`) 
                            VALUES ('$Centre_id','$loan_id_calc','$loan_category_calc','$loan_amount_calc','$total_cus','$profit_type_calc','$due_method_calc','$profit_method_calc','$scheme_day_calc','$interest_rate_calc','$due_period_calc','$doc_charge_calc','$processing_fees_calc','$scheme_name_calc','$scheme_date_calc','$loan_amnt_calc','$principal_amnt_calc','$interest_amnt_calc','$total_amnt_calc','$due_amnt_calc','$doc_charge_calculate','$processing_fees_calculate','$net_cash_calc','$due_startdate_calc','$maturity_date_calc','1','$user_id',NOW()) ");
    $result = 2;
} else {
    $qry = $pdo->query("UPDATE `loan_entry_loan_calculation` 
    SET 
        `centre_id` = '$Centre_id',
        `loan_id` = '$loan_id_calc',
        `loan_category` = '$loan_category_calc',
        `loan_amount` = '$loan_amount_calc',
        `total_customer` = '$total_cus',
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

// Check if customer mapping data is provided
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

        if (isset($customer['intrest_amount'])) {
            $intrest_amount = $customer['intrest_amount'];
        } else {
            // Handle missing designation
            continue;
        }

        if (isset($customer['principle'])) {
            $principle = $customer['principle'];
        } else {
            // Handle missing designation
            continue;
        }

        if (isset($customer['due_amount'])) {
            $due_amount = $customer['due_amount'];
        } else {
            // Handle missing designation
            continue;
        }

        if (isset($customer['net_cash'])) {
            $net_cash = $customer['net_cash'];
        } else {
            // Handle missing designation
            continue;
        }
        if (isset($customer['customer_loan_amount'])) {
            $customer_loan_amount = $customer['customer_loan_amount'];
        } else {
            // Handle missing designation
            continue;
        }

        // Check if the customer is already mapped to the same loan_id
        $stmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping lcm WHERE lcm.cus_id = '$cus_id' AND lcm.centre_id = '$Centre_id' and lcm.loan_id = '$loan_id_calc' ");
        $existing_mapping = $stmt->fetchColumn();

        if ($existing_mapping > 0) {
            // Customer is already mapped to the same loan_id, send warning
            $response = ['result' => 4, 'message' => 'The customer is already mapped to this loan.'];
        } else {
            // Insert the new customer mapping
            $qry = $pdo->query("INSERT INTO loan_cus_mapping (loan_id, centre_id, cus_id, net_cash , customer_mapping,loan_amount , `intrest_amount`, `principle_amount`, `due_amount`,  designation, inserted_login_id, created_on) 
                                VALUES ('$loan_id_calc', '$Centre_id', '$cus_id','$net_cash','$cus_mapping','$customer_loan_amount','$intrest_amount','$principle ',' $due_amount',  '$designation', '$user_id', NOW())");
        }
        if ($cus_mapping == 'Renewal') {
            // Check if any loan has a status between 1 and 7 (inclusive)
            $stmt = $pdo->query("SELECT lcm.id 
                                 FROM loan_cus_mapping lcm
                                 LEFT JOIN loan_entry_loan_calculation lelc 
                                 ON lcm.loan_id = lelc.loan_id 
                                 WHERE lelc.loan_status >= 1 AND lelc.loan_status < 8 and lcm.cus_id = '$cus_id'AND lcm.loan_id !='$loan_id_calc'");
        
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

// Check if the query was successful
if ($qry) {
    if ($id == '') {
        $last_id = $pdo->lastInsertId();
    } else {
        $last_id = ''; // No new ID for updates
    }

    // If insertion or update was successful, check if the customer mapping count is now equal to total members
    $mappingCountStmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping WHERE loan_id = '$loan_id_calc'");
    $current_mapping_count = $mappingCountStmt->fetchColumn();

    // Check if the total_member matches customer mapping count
    if ($current_mapping_count == $total_cus) {
        // Update status in loan_entry_loan_calculation table
        $statusUpdateStmt = $pdo->query("UPDATE loan_entry_loan_calculation SET loan_status = '2' WHERE loan_id = '$loan_id_calc'");
        if ($statusUpdateStmt) {
            $result = 1;
        } else {
            $result = 0;
        }
    } else {
        $result = 1; // Success, but mapping count not yet full
    }
} else {
    $result = 0; // Failure
    $last_id = '';
}
$pdo->commit();

} catch (Exception $e) {
    // Rollback the transaction on error
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
    exit;
}
echo json_encode(['result' => $result, 'last_id' => $last_id]);
?>
