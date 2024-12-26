<?php
require '../../ajaxconfig.php';
@session_start();

class loanEntryUploadClass
{
    public function uploadFiletoFolder()
    {
        $excel = $_FILES['excelFile']['name'];
        $excel_temp = $_FILES['excelFile']['tmp_name'];
        $excelfolder = "../../uploads/excel_format/loan_entry_format/" . $excel;

        $fileExtension = pathinfo($excelfolder, PATHINFO_EXTENSION); //get the file extension

        $excel = uniqid() . '.' . $fileExtension;
        while (file_exists("../../uploads/excel_format/loan_entry_format/" . $excel)) {
            // this loop will continue until it generates a unique file name
            $excel = uniqid() . '.' . $fileExtension;
        }
        $excelfolder = "../../uploads/excel_format/loan_entry_format/" . $excel;
        move_uploaded_file($excel_temp, $excelfolder);
        return $excelfolder;
    }

    public function fetchAllRowData($Row)
    {
        $dataArray = array(
            'centre_name' => isset($Row[1]) ? $Row[1] : "",
            'loan_id' => isset($Row[2]) ? $Row[2] : "",
            'loan_category' => isset($Row[3]) ? $Row[3] : "",
            'loan_amount' => isset($Row[4]) ? $Row[4] : "",
            'total_customer' => isset($Row[5]) ? $Row[5] : "",
            'loan_amt_per_cus' => isset($Row[6]) ? $Row[6] : "",
            'profit_type' => isset($Row[7]) ? $Row[7] : "",
            'due_method' => isset($Row[8]) ? $Row[8] : "",
            'benefit_method' => isset($Row[9]) ? $Row[9] : "",
            'date' => isset($Row[10]) ? $Row[10] : "",
            'day' => isset($Row[11]) ? $Row[11] : "",
            'scheme_name' => isset($Row[12]) ? $Row[12] : "",
            'interest_rate' => isset($Row[13]) ? $Row[13] : "",
            'due_period' => isset($Row[14]) ? $Row[14] : "",
            'doc_charge' => isset($Row[15]) ? $Row[15] : "",
            'processing_fees' => isset($Row[16]) ? $Row[16] : "",
            'principal_amnt' => isset($Row[17]) ? $Row[17] : "",
            'intreset_amnt' => isset($Row[18]) ? $Row[18] : "",
            'total_amnt' => isset($Row[19]) ? $Row[19] : "",
            'due_amnt' => isset($Row[20]) ? $Row[20] : "",
            'doc_charge_cal' => isset($Row[21]) ? $Row[21] : "",
            'proccessing_fees_cal' => isset($Row[22]) ? $Row[22] : "",
            'net_cash' => isset($Row[23]) ? $Row[23] : "",
            'due_start_date' => isset($Row[24]) ? $Row[24] : "",
            'maturity_date' => isset($Row[25]) ? $Row[25] : "",
            'centre_limit' => isset($Row[26]) ? $Row[26] : "",
        );

        $profit_typeArray = ['Calculation' => '1', 'Scheme' => '2'];
        $dataArray['profit_type'] = $this->arrayItemChecker($profit_typeArray, $dataArray['profit_type']);
        $referred_typeArray = ['Monthly' => '1', 'Weekly' => '2'];
        $dataArray['due_method'] = $this->arrayItemChecker($referred_typeArray, $dataArray['due_method']);
        $profit_typeArray = ['Pre Benefit' => '1', 'After Benefit' => '2'];
        $dataArray['benefit_method'] = $this->arrayItemChecker($profit_typeArray, $dataArray['benefit_method']);
       
        if (!empty($dataArray['day'])) {
            $schemeday_typeArray = ['Monday' => '1', 'Tuesday' => '2', 'Wednesday' => '3', 'Thursday' => '4', 'Friday' => '5', 'Saturday' => '6', 'Sunday' => '7'];
            $dataArray['day'] = $this->arrayItemChecker($schemeday_typeArray, $dataArray['day']);
        }
        $dataArray['due_start_date'] = $this->dateFormatChecker($dataArray['due_start_date']);
        $dataArray['maturity_date'] = $this->dateFormatChecker($dataArray['maturity_date']);
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
    function getLoanCategoryId($pdo, $loan_category)
    {
        $stmt = $pdo->query("SELECT lcc.id FROM loan_category_creation lcc LEFT JOIN loan_category lc ON lcc.loan_category = lc.id WHERE LOWER(REPLACE(TRIM(lc.loan_category),' ' ,'')) = LOWER(REPLACE(TRIM('$loan_category'),' ' ,'')) ");
        //  $stmt->execute(['loan_category' => $loan_category]);
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $loan_cat_id = $row["id"];
        } else {
            $loan_cat_id = 'Not Found';
        }

        return $loan_cat_id;
    }
    
    function getSchemeId($pdo, $scheme_name)
    {
        $stmt = $pdo->query("SELECT s.id
        FROM `loan_category_creation` lcc 
        JOIN scheme s ON FIND_IN_SET(s.id, lcc.scheme_name)
        WHERE LOWER(REPLACE(TRIM(s.scheme_name),' ' ,'')) = LOWER(REPLACE(TRIM('$scheme_name'),' ' ,'')) ");
        if ($stmt->rowCount() > 0) {
            $scheme_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
        } else {
            $scheme_id = '';
        }
        return $scheme_id;
    }
   


    function loanEntryTables($pdo, $data)
    {
        // Print or log $data to see what values are being passed
        $user_id = $_SESSION['user_id'];
        $che_query = "SELECT loan_id FROM loan_entry_loan_calculation WHERE  loan_id = '" . $data['loan_id'] . "'";
        $result2 = $pdo->query($che_query);
        if ($result2->rowCount() == 0) {
            $insertQuery = "INSERT INTO `loan_entry_loan_calculation`( `centre_id`, `loan_id`,`loan_category`, `loan_amount`, `total_customer`, `loan_amt_per_cus`, `profit_type`, `due_month`, `benefit_method`,`scheme_day_calc`, `interest_rate`, `due_period`, `doc_charge`, `processing_fees`, `scheme_name`, `scheme_date`,  `loan_amount_calc`, `principal_amount_calc`, `intrest_amount_calc`, `total_amount_calc`, `due_amount_calc`, `document_charge_cal`, `processing_fees_cal`, `net_cash_calc`, `due_start`, `due_end`, `loan_status`, `insert_login_id`,`created_on`) VALUES (
          '" . strip_tags($data['centre_id']) . "',
            '" . strip_tags($data['loan_id']) . "',
             '" . strip_tags($data['loan_category_id']) . "',
             '" . strip_tags($data['loan_amount']) . "',
            '" . strip_tags($data['total_customer']) . "',
        '" . strip_tags($data['loan_amt_per_cus']) . "',
        '" . strip_tags($data['profit_type']) . "',
            '" . strip_tags($data['due_method']) . "',
            '" . strip_tags($data['benefit_method']) . "',
            '" . strip_tags($data['day']) . "',
            '" . strip_tags($data['interest_rate']) . "',
            '" . strip_tags($data['due_period']) . "',
            '" . strip_tags($data['doc_charge']) . "',
            '" . strip_tags($data['processing_fees']) . "',
            '" . strip_tags($data['scheme_id']) . "',
            '" . strip_tags($data['date']) . "',
            '" . strip_tags($data['loan_amount']) . "',
            '" . strip_tags($data['principal_amnt']) . "',
            '" . strip_tags($data['intreset_amnt']) . "',
            '" . strip_tags($data['total_amnt']) . "',
            '" . strip_tags($data['due_amnt']) . "',
            '" . strip_tags($data['doc_charge_cal']) . "',
            '" . strip_tags($data['proccessing_fees_cal']) . "',
            '" . strip_tags($data['net_cash']) . "',
            '" . strip_tags($data['due_start_date']) . "',
            '" . strip_tags($data['maturity_date']) . "',
            1,
               '" . $user_id . "', 
            NOW()
        )";
            $pdo->query($insertQuery);
        }
        $pdo->query("UPDATE centre_creation 
        SET centre_limit = '" . strip_tags($data['centre_limit']) . "', update_login_id = '$user_id', updated_on = NOW() 
        WHERE centre_id = '" . strip_tags($data['centre_id']) . "' ");
    }



   
    function handleError($data)
    {
        $errcolumns = array();


        if ($data['centre_name'] == '') {
            $errcolumns[] = 'Centre Name';
        }
        if (!empty($data['centre_id'])) {
            if ($data['centre_id'] == 'Not Found') {
                $errcolumns[] = 'Centre ID';
            }
        }
        if ($data['loan_id'] == '') {
            $errcolumns[] = 'Loan ID';
        }
     
        if ($data['loan_category_id'] == 'Not Found') {
            $errcolumns[] = 'Loan Category ID';
        }
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['loan_amount'])) {
            $errcolumns[] = 'Loan Amount';
        }
        if (!preg_match('/^[0-9]+$/', $data['total_customer'])) {
            $errcolumns[] = 'Total Customers';
        }
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['loan_amt_per_cus'])) {
            $errcolumns[] = 'Loan Amount per Customer';
        }
        if ($data['profit_type'] != 'Not Found') {
            // Subcondition 7.1
            if ($data['profit_type'] == '1') {
                if (
                    $data['due_method'] == 'Not Found'
                    || $data['benefit_method'] == 'Not Found'
                ) {
                    $errcolumns[] = 'Due Method or Benfit Method';
                }
            }

            // Subcondition 7.2
            if ($data['profit_type'] == '2') {
                if ($data['due_method'] == 'Not Found' || $data['benefit_method'] == 'Not Found' || $data['scheme_id'] == '') {
                    $errcolumns[] = 'Due Method or Benfit Method or Scheme Name';
                }
            }
        } else {
            $errcolumns[] = 'Profit Type';
        }
        if ($data['due_method'] == '1') {
            if (!preg_match('/^[0-9]+$/', $data['date'])) {
                $errcolumns[] = 'Date';
            }
        }else{
            if (
                $data['day'] == 'Not Found'
            ) {
                $errcolumns[] = 'Day';
            }
        }
       
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['interest_rate'])) {
            $errcolumns[] = 'Interest Rate';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['due_period'])) {
            $errcolumns[] = 'Due Period';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['doc_charge'])) {
            $errcolumns[] = 'Document Charge';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['processing_fees'])) {
            $errcolumns[] = 'Processing Fee';
        }
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['principal_amnt'])) {
            $errcolumns[] = 'Principal Amount Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['intreset_amnt'])) {
            $errcolumns[] = 'Interest Amount Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['total_amnt'])) {
            $errcolumns[] = 'Total Amount Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['due_amnt'])) {
            $errcolumns[] = 'Due Amount Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['doc_charge_cal'])) {
            $errcolumns[] = 'Document Charge Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['proccessing_fees_cal'])) {
            $errcolumns[] = 'Processing Fee Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['net_cash'])) {
            $errcolumns[] = 'Net Cash Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['centre_limit'])) {
            $errcolumns[] = 'Centre Limit';
        }
        
        if ($data['due_start_date'] == 'Invalid Date') {
            $errcolumns[] = 'Due Start From';
        }

        if ($data['maturity_date'] == 'Invalid Date') {
            $errcolumns[] = 'Maturity Date';
        }
        return $errcolumns;
    }
}
