<?php
require '../../ajaxconfig.php';
@session_start();

class loanIssueUploadClass
{
    public function uploadFiletoFolder()
    {
        $excel = $_FILES['excelFile']['name'];
        $excel_temp = $_FILES['excelFile']['tmp_name'];
        $excelfolder = "../../uploads/excel_format/loan_issue_format/" . $excel;

        $fileExtension = pathinfo($excelfolder, PATHINFO_EXTENSION); //get the file extension

        $excel = uniqid() . '.' . $fileExtension;
        while (file_exists("../../uploads/excel_format/loan_issue_format/" . $excel)) {
            // this loop will continue until it generates a unique file name
            $excel = uniqid() . '.' . $fileExtension;
        }
        $excelfolder = "../../uploads/excel_format/loan_issue_format/" . $excel;
        move_uploaded_file($excel_temp, $excelfolder);
        return $excelfolder;
    }

    public function fetchAllRowData($Row)
    {
        $dataArray = array(
            'loan_id' => isset($Row[1]) ? $Row[1] : "",
            'centre_name' => isset($Row[2]) ? $Row[2] : "",
            'customer_mapping' => isset($Row[3]) ? $Row[3] : "",
            'cus_aadhar' => isset($Row[4]) ? $Row[4] : "",
            'designation' => isset($Row[5]) ? $Row[5] : "",
            'loan_date' => isset($Row[6]) ? $Row[6] : "",
            'loan_amount' => isset($Row[7]) ? $Row[7] : "",
            'net_cash' => isset($Row[8]) ? $Row[8] : "",
            'payment_mode' => isset($Row[9]) ? $Row[9] : "",
            'issue_type' => isset($Row[10]) ? $Row[10] : "",
            'issue_amount' => isset($Row[11]) ? $Row[11] : "",
            'issue_date' => isset($Row[12]) ? $Row[12] : "",
        );
        $dataArray['cus_aadhar'] = strlen(trim($dataArray['cus_aadhar'])) == 12 ? $dataArray['cus_aadhar'] : 'Invalid';
        if (!empty($dataArray['loan_date'])) {
            $dataArray['loan_date'] = $this->dateFormatChecker($dataArray['loan_date']);
        }
        if (!empty($dataArray['issue_date'])) {
            $dataArray['issue_date'] = $this->dateFormatChecker($dataArray['issue_date']);
        }
        if (!empty($dataArray['payment_mode'])) {
            $refer_typeArray = ['Single' => '1', 'Split' => '2'];
            $dataArray['payment_mode'] = $this->arrayItemChecker($refer_typeArray, $dataArray['payment_mode']);
        }
        if (!empty($dataArray['issue_type'])) {
            $referred_typeArray = ['Cash' => '1', 'Bank Transfer' => '2'];
            $dataArray['issue_type'] = $this->arrayItemChecker($referred_typeArray, $dataArray['issue_type']);
        }
        return $dataArray;
    }
    function dateFormatChecker($checkdate)
    {
        // Attempt to create a DateTime object from the provided date
        $dateTime = DateTime::createFromFormat('Y-m-d', $checkdate);

        // Check if the date is in the correct format
        if ($dateTime && $dateTime->format('Y-m-d') === $checkdate) {
            // Date is in the correct format, no need to change anything
            return $checkdate;
        }
        return 'Invalid Date';
    }
    function arrayItemChecker($arrayList, $arrayItem)
    {
        if (array_key_exists($arrayItem, $arrayList)) {
            $arrayItem = $arrayList[$arrayItem];
        } else {
            $arrayItem = 'Not Found';
        }
        return $arrayItem;
    }


    function centreName($pdo, $centre_name)
    {
        // Use a direct query (ensure $grp_name is properly sanitized before using)
        $stmt = $pdo->query("SELECT centre_id FROM centre_creation WHERE centre_name = '$centre_name'");

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $centre_id = $row['centre_id']; // Fetch the 'grp_id' column
        } else {
            $centre_id = 'Not Found'; // Return null if no result is found
        }

        return $centre_id;
    }

    function getCustomerId($pdo, $cus_aadhar)
    {
        $stmt = $pdo->query("SELECT id FROM  customer_creation WHERE aadhar_number = '$cus_aadhar'");
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cust_id = $row["id"];
        } else {
            $cust_id = 'Not Found'; // Return null if no result is found
        }


        return $cust_id;
    }
    function checkCustomerData($pdo, $cust_id)
    {

        $qry = $pdo->query("SELECT lelc.loan_status 
              FROM customer_creation cc
              LEFT JOIN loan_cus_mapping lcm ON cc.id = lcm.cus_id
              LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id where cc.id='$cust_id' AND lelc.loan_status>=1");

        if ($qry && $qry->rowCount() > 0) {
            $result = $qry->fetch(PDO::FETCH_ASSOC);
            $status = $result['loan_status'];  // Fetch the customer status

            // Determine cus_status based on status value
            if ($status >= 1 && $status <= 6) {
                $cus_status = '';  // For status between 1 and 6, cus_status is empty
            } elseif ($status == 7 || $status == 8) {
                $cus_status = 'Additional';  // For status 7 or 8, cus_status is 'Additional'
            } elseif ($status >= 9) {
                $cus_status = 'Renewal';  // For status 9 or above, cus_status is 'Renewal'
            }

            $response['cus_data'] = 'Existing';  // Customer is 'Existing'
            $response['cus_status'] = $cus_status;  // Include the determined cus_status

        } else {
            // If no result is found, it's a new customer
            $response['cus_data'] = 'New';   // Customer is 'New'
            $response['cus_status'] = '';    // cus_status is empty for new customers
        }

        return $response;
    }
    function getCustomerMappingId($pdo, $cust_id, $loan_id)

    {
        $stmt = $pdo->query("SELECT id FROM  loan_cus_mapping WHERE cus_id = '$cust_id' AND loan_id = '$loan_id'");
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cus_mapping_id = $row["id"];
        } else {
            $cus_mapping_id = 'Not Found'; // Return null if no result is found
        }
        return $cus_mapping_id;
    }
    function cusMappingTable($pdo, $data)
    {
        $user_id = $_SESSION['user_id'];

        // Fetch current customer count in the Loan
        $stmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping WHERE loan_id = '" . $data['loan_id'] . "'");
        $current_count = $stmt->fetchColumn();

        // Fetch total members allowed in the Loan
        $smt2 = $pdo->query("SELECT total_customer FROM loan_entry_loan_calculation WHERE loan_id = '" . $data['loan_id'] . "'");
        $total_members = $smt2->fetchColumn();

        // Initialize a response message variable
        $responseMessage = '';

        // Add the new customer to the Loan mapping
        if ($current_count < $total_members) {
            $che_query = "SELECT id FROM loan_cus_mapping WHERE loan_id = '" . $data['loan_id'] . "' AND cus_id ='" . strip_tags($data['cust_id']) . "'";
            $result2 = $pdo->query($che_query);
            if ($result2->rowCount() == 0) {
                $insert_query5 = "INSERT INTO loan_cus_mapping (loan_id,centre_id, cus_id, customer_mapping, designation, inserted_login_id, created_on) 
                              VALUES ('" . strip_tags($data['loan_id']) . "', '" . strip_tags($data['centre_id']) . "','" . strip_tags($data['cust_id']) . "', '" . strip_tags($data['customer_mapping']) . "','" . strip_tags($data['designation']) . "', '$user_id', NOW())";

                $pdo->query($insert_query5);

                if ($pdo->lastInsertId()) {
                    // Check if the count now equals the total members allowed
                    $stmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping WHERE loan_id = '" . $data['loan_id'] . "'");
                    $current_count = $stmt->fetchColumn();

                    if ($current_count == $total_members) {
                        // Update the status in the Loan table to indicate the Loan is full
                        $pdo->query("UPDATE loan_entry_loan_calculation SET loan_status = '4',update_login_id = '$user_id', updated_on = NOW() WHERE loan_id = '" . $data['loan_id'] . "'");
                        $responseMessage = "Customer successfully added. The group is now full.";
                    } else {
                        $responseMessage = "Customer successfully added.";
                    }
                } else {
                    $responseMessage = "Error inserting new customer to group.";
                }
            }
        } else {
            $responseMessage = "Customer Mapping Limit is Exceeded"; // Show error if the count exceeds the limit
        }
        // Return response message
        return $responseMessage;
    }

    function statusUpdate($pdo, $data)
    {
        $update_qry = "UPDATE customer_creation 
    SET cus_data = 'Existing', 
    cus_status = '" . strip_tags($data['cus_status']) . "' 
    WHERE aadhar_number = '" . strip_tags($data['cus_aadhar']) . "'";
        $pdo->query($update_qry);
    }

    function loanIssueTable($pdo, $data)
    {
        // Get user ID from session
        $user_id = $_SESSION['user_id'];
        if (!empty($data['loan_date'])) {
            $pdo->query("UPDATE  loan_entry_loan_calculation
        SET loan_date = '" . strip_tags($data['loan_date']) . "', update_login_id = '$user_id', updated_on = NOW() 
        WHERE loan_id = '" . strip_tags($data['loan_id']) . "' ");
        }
        // Get user ID from session
        $user_id = $_SESSION['user_id'];
        // Fetch current customer count in the Loan
        // Initialize a response message variable
        $responseMessage = '';

        // Add the new customer to the Loan mapping
        if (!empty($data['issue_date'])) {
            // Prepare the insert query for settlement_info table
            $qry5 = $pdo->query("SELECT issue_status 
            FROM loan_cus_mapping WHERE id = '" . strip_tags($data['cus_mapping_id']) . "'");
            $issue_status = $qry5->fetchColumn();
            if ($issue_status != 1) {
                $insert_query2 = "INSERT INTO loan_issue 
        (cus_mapping_id, loan_id, loan_amnt, net_cash, payment_mode, issue_type, issue_amount, issue_date, insert_login_id, created_on) 
            VALUES (
                '" . strip_tags($data['cus_mapping_id']) . "',
                '" . strip_tags($data['loan_id']) . "',
                '" . strip_tags($data['loan_amount']) . "',
                '" . strip_tags($data['net_cash']) . "',   
                '" . strip_tags($data['payment_mode']) . "',
                '" . strip_tags($data['issue_type']) . "',
                '" . strip_tags($data['issue_amount']) . "',
                '" . strip_tags($data['issue_date']) . "',
                '" . $user_id . "',
                NOW()
            )";

                // Execute the insert query for settlement_info
                $pdo->query($insert_query2);
            }
        }
        if (!empty($data['issue_date'])) {
            // Prepare and execute the group_cus_mapping update query
            $qry4 = $pdo->query("SELECT COALESCE(SUM(issue_amount), 0) as total_amount 
            FROM loan_issue 
            WHERE cus_mapping_id = '" . strip_tags($data['cus_mapping_id']) . "'");
            $total_amount = $qry4->fetchColumn();
            $net_cash = strip_tags($data['net_cash']);
            if ($net_cash == $total_amount) {
                $loan_update = "UPDATE loan_cus_mapping 
            SET issue_status = '1'
            WHERE loan_id = '" . strip_tags($data['loan_id']) . "' 
            AND id = '" . strip_tags($data['cus_mapping_id']) . "' ";
            $pdo->query($loan_update);
            }
    
            $stmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping WHERE loan_id = '" . $data['loan_id'] . "' AND issue_status = 1");
            $issue_count = $stmt->fetchColumn();
            $smt2 = $pdo->query("SELECT total_customer FROM loan_entry_loan_calculation WHERE loan_id = '" . $data['loan_id'] . "'");
            $total_members = $smt2->fetchColumn();
            if ($issue_count ==  $total_members) {
                // If all customers have 'Issued' status, update loan_status to 7 in loan_entry_loan_calculation table
                $qry6 = $pdo->query("UPDATE loan_entry_loan_calculation 
                         SET loan_status = 7,update_login_id = $user_id, updated_on = NOW() 
                         WHERE loan_id = '" . strip_tags($data['loan_id']) . "' ");
            }
        }
    }

    function handleError($data)
    {
        $errcolumns = array();


        if ($data['centre_name'] == '') {
            $errcolumns[] = 'Centre Name';
        }
        if ($data['customer_mapping'] == '') {
            $errcolumns[] = 'Customer Mapping';
        }
        if (!empty($data['centre_id'])) {
            if ($data['centre_id'] == 'Not Found') {
                $errcolumns[] = 'Centre ID';
            }
        }
        if ($data['loan_id'] == '') {
            $errcolumns[] = 'Loan ID';
        }
        if ($data['cus_aadhar'] == 'Invalid') {
            $errcolumns[] = 'Customer Aadhar Number';
        }
        if ($data['cust_id'] == 'Not Found') {
            $errcolumns[] = 'Customer ID';
        }
        if (!empty($dataArray['loan_amount'])) {
            if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['loan_amount'])) {
                $errcolumns[] = 'Loan Amount';
            }
        }
        if (!empty($dataArray['issue_amount'])) {
            if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['issue_amount'])) {
                $errcolumns[] = 'Issue Amount';
            }
        }
        if ($data['payment_mode'] == 'Not Found') {
            $errcolumns[] = 'Payment Mode';
        }
        if ($data['issue_type'] == 'Not Found') {
            $errcolumns[] = 'Issue Type';
        }

        if ($data['loan_date'] == 'Invalid Date') {
            $errcolumns[] = 'Loan Date';
        }

        if ($data['issue_date'] == 'Invalid Date') {
            $errcolumns[] = 'Issue Date';
        }
        return $errcolumns;
    }
}
