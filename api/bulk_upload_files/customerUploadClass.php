<?php
require '../../ajaxconfig.php';
@session_start();

class customerUploadClass
{
    public function uploadFiletoFolder()
    {
        $excel = $_FILES['excelFile']['name'];
        $excel_temp = $_FILES['excelFile']['tmp_name'];
        $excelfolder = "../../uploads/excel_format/customer_centre_format/" . $excel;

        $fileExtension = pathinfo($excelfolder, PATHINFO_EXTENSION); //get the file extension

        $excel = uniqid() . '.' . $fileExtension;
        while (file_exists("../../uploads/excel_format/customer_centre_format/" . $excel)) {
            // this loop will continue until it generates a unique file name
            $excel = uniqid() . '.' . $fileExtension;
        }
        $excelfolder = "../../uploads/excel_format/customer_centre_format/" . $excel;
        move_uploaded_file($excel_temp, $excelfolder);
        return $excelfolder;
    }

    public function fetchAllRowData($Row)
    {
        $dataArray = array(
            'first_name' => isset($Row[1]) ? $Row[1] : "",
            'aadhar_number' => isset($Row[2]) ? $Row[2] : "",
            'customer_data' => isset($Row[3]) ? $Row[3] : "",
            'mobile1' => isset($Row[4]) ? $Row[4] : "",
            'area' => isset($Row[5]) ? $Row[5] : "",
            'multiple_loan' => isset($Row[6]) ? $Row[6] : "",
            'fam_name' => isset($Row[7]) ? $Row[7] : "",
            'fam_aadhar' => isset($Row[8]) ? $Row[8] : "",
            'fam_mobile' => isset($Row[9]) ? $Row[9] : "",
            'centre_name' => isset($Row[10]) ? $Row[10] : "",
            'centre_no' => isset($Row[11]) ? $Row[11] : "",
            'centre_mobile' => isset($Row[12]) ? $Row[12] : "",
            'centre_area' => isset($Row[13]) ? $Row[13] : "",
            'branch' => isset($Row[14]) ? $Row[14] : "",
            'rep_name' => isset($Row[15]) ? $Row[15] : "",
            'rep_aadhar' => isset($Row[16]) ? $Row[16] : "",
            'rep_mobile' => isset($Row[17]) ? $Row[17] : "",
        );

        $dataArray['rep_aadhar'] = strlen($dataArray['rep_aadhar']) == 12 ? $dataArray['rep_aadhar'] : 'Invalid';
        $dataArray['mobile1'] = strlen($dataArray['mobile1']) == 10 ? $dataArray['mobile1'] : 'Invalid';
        $dataArray['fam_aadhar'] = strlen($dataArray['fam_aadhar']) == 12 ? $dataArray['fam_aadhar'] : 'Invalid';
        $dataArray['aadhar_number'] = strlen(trim($dataArray['aadhar_number'])) == 12 ? $dataArray['aadhar_number'] : 'Invalid';
        $dataArray['fam_mobile'] = strlen($dataArray['fam_mobile']) == 10 ? $dataArray['fam_mobile'] : 'Invalid';
        $dataArray['rep_mobile'] = strlen($dataArray['rep_mobile']) == 10 ? $dataArray['rep_mobile'] : 'Invalid';
        $referred_typeArray = ['Enable' => '1', 'Disable' => '0'];
        $dataArray['multiple_loan'] = $this->arrayItemChecker($referred_typeArray, $dataArray['multiple_loan']);
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

    function getcusId($pdo, $id)
    {
        if (!isset($id) || $id == '') {
            $qry = $pdo->query("SELECT cus_id FROM customer_creation WHERE cus_id != '' ORDER BY id DESC LIMIT 1");

            if ($qry->rowCount() > 0) {
                $qry_info = $qry->fetch();
                $l_no = ltrim(strstr($qry_info['cus_id'], '-'), '-');
                $l_no = $l_no + 1;
                $cus_ID_final = "C-" . "$l_no";
            } else {
                $cus_ID_final = "C-101";
            }
        } else {
            $stmt = $pdo->prepare("SELECT cus_id FROM customer_creation WHERE id = :id");
            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() > 0) {
                $qry_info = $stmt->fetch();
                $cus_ID_final = $qry_info['cus_id'];
            } else {
                $cus_ID_final = "C-101"; // Default value if not found
            }
        }

        return $cus_ID_final;
    }
    function getcentreId($pdo, $id)
    {
        if (!isset($id) || $id == '') {
            $qry = $pdo->query("SELECT centre_id FROM centre_creation WHERE centre_id != '' ORDER BY id DESC LIMIT 1");

            if ($qry->rowCount() > 0) {
                $qry_info = $qry->fetch();
                $l_no = ltrim(strstr($qry_info['centre_id'], '-'), '-');
                $l_no = $l_no + 1;
                $cen_ID_final = "M-" . "$l_no";
            } else {
                $cen_ID_final = "M-101";
            }
        } else {
            $stmt = $pdo->prepare("SELECT centre_id FROM centre_creation WHERE id = :id");
            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() > 0) {
                $qry_info = $stmt->fetch();
                $cen_ID_final = $qry_info['centre_id'];
            } else {
                $cen_ID_final = "M-101"; // Default value if not found
            }
        }

        return $cen_ID_final;
    }

    function getBranchId($pdo, $branch)
    {
        $stmt = $pdo->query("SELECT b.id
FROM `branch_creation` b
LEFT JOIN `centre_creation` gc ON FIND_IN_SET(b.id, gc.branch)
WHERE LOWER(REPLACE(TRIM(b.branch_name), ' ', ''))  = LOWER(REPLACE(TRIM('$branch'), ' ', ''))");

        if ($stmt->rowCount() > 0) {
            $branch_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
        } else {
            $branch_id = 'Not Found'; // Return 'Not Found' if branch does not exist
        }
        return $branch_id;
    }
    function getAreaId($pdo, $areaname)
    {
        $stmt = $pdo->query("SELECT anc.id, anc.areaname  FROM area_creation ac JOIN area_name_creation anc ON FIND_IN_SET(anc.id, ac.area_id)
        WHERE  LOWER(REPLACE(TRIM(anc.areaname),' ','')) = LOWER(REPLACE(TRIM('$areaname'),' ',''))");
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $area_id = $row["id"];
        } else {
            $area_id = 'Not Found';
        }

        return $area_id;
    }
    function checkCustomerData($pdo, $cus_id)
    {
        $cus_id = strip_tags($cus_id); // Sanitize input

        // Query to check customer profile and status
        $qry = $pdo->query("SELECT lelc.loan_status 
              FROM customer_creation cc
              LEFT JOIN loan_cus_mapping lcm ON cc.id = lcm.cus_id
              LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id where cc.cus_id='$cus_id' AND lelc.loan_status>=1");

        if ($qry && $qry->rowCount() > 0) {
            $result = $qry->fetch(PDO::FETCH_ASSOC);
            $status = $result['status'];  // Fetch the customer status

            // Determine cus_status based on status value
            if ($status >= 1 && $status <= 6) {
                $cus_status = '';  // For status between 1 and 6, cus_status is empty
            } elseif ($status == 7 || $status == 8) {
                $cus_status = 'Additional';  // For status 7 or 8, cus_status is 'Additional'
            } elseif ($status >= 9) {
                $cus_status = 'Renewal';  // For status 9 or above, cus_status is 'Renewal'
            }

            $response['cus_data'] = 'Existing';  // Customer is 'Existing'
            $response['id'] = $result['id'];     // Return the customer ID
            $response['cus_status'] = $cus_status;  // Include the determined cus_status

        } else {
            // If no result is found, it's a new customer
            $response['cus_data'] = 'New';   // Customer is 'New'
            $response['id'] = '';            // No ID for new customers
            $response['cus_status'] = '';    // cus_status is empty for new customers
        }

        return $response;
    }
    function FamilyTable($pdo, $data)
    {
        $user_id = $_SESSION['user_id'];

        // Check if the customer exists based on aadhar_number
        $check_queryss = "SELECT cus_id FROM customer_creation WHERE aadhar_number = '" . strip_tags($data['aadhar_number']) . "'";

        // Execute the query and fetch the cus_id
        $result1 = $pdo->query($check_queryss);
        $cusData = $result1->fetch(PDO::FETCH_ASSOC);

        // If cus_id is found, proceed with further checks
        if ($cusData) {
            $cus_ids = $cusData['cus_id']; // Get cus_id from query result

            // Check if family member already exists based on the provided details
            $check_query3 = "SELECT cus_id 
                             FROM family_info 
                             WHERE cus_id = '" . strip_tags($cus_ids) . "' 
                             AND fam_name = '" . strip_tags($data['fam_name']) . "' 
                             AND fam_aadhar = '" . strip_tags($data['fam_aadhar']) . "' 
                             AND fam_mobile = '" . strip_tags($data['fam_mobile']) . "'";

            $result2 = $pdo->query($check_query3);

            // If no family member exists, insert new family member info
            if ($result2->rowCount() == 0) {
                // Prepare the insert query for family_info
                $insert_query1 = "INSERT INTO family_info (cus_id, fam_name, fam_aadhar, fam_mobile, insert_login_id, created_on) 
                                  VALUES (
                                      '" . strip_tags($cus_ids) . "',
                                      '" . strip_tags($data['fam_name']) . "',
                                      '" . strip_tags($data['fam_aadhar']) . "',
                                      '" . strip_tags($data['fam_mobile']) . "',
                                      '" . $user_id . "',
                                      NOW()
                                  )";

                // Execute the insert query
                $pdo->query($insert_query1);
            }
        } else {
            // Handle case where aadhar_number is not found in customer_creation
            echo "Error: Customer with the given Aadhar number not found.";
        }
    }


    function customerEntryTables($pdo, $data)
    {
        // Print or log $data to see what values are being passed
        $user_id = $_SESSION['user_id'];
        $che_query = "SELECT id FROM customer_creation WHERE  aadhar_number = '" . $data['aadhar_number'] . "'";
        $result2 = $pdo->query($che_query);
        if ($result2->rowCount() == 0) {
            $insertQuery = "INSERT INTO `customer_creation` (
             `cus_id`,
            `aadhar_number`, 
            `cus_data`, 
            `cus_status`, 
            `first_name`, 
            `area`, 
            `mobile1`, 
            `multiple_loan`, 
            `insert_login_id`, `created_on`
        ) VALUES (
        '" . strip_tags($data['cus_id']) . "',
            '" . strip_tags($data['aadhar_number']) . "',
             '" . strip_tags($data['customer_data']) . "',
             '" . strip_tags($data['cus_status']) . "',
            '" . strip_tags($data['first_name']) . "',
        '" . strip_tags($data['area_id']) . "',
        '" . strip_tags($data['mobile1']) . "',
            '" . strip_tags($data['multiple_loan']) . "',
               '" . $user_id . "', 
            NOW()
        )";
            $pdo->query($insertQuery);
        }
    }


    function centreTable($pdo, $data)
    {
        $user_id = $_SESSION['user_id'];
        // Check if the place already exists (case-insensitive and ignoring spaces)
        $check_querys = "SELECT centre_name FROM centre_creation 
  WHERE LOWER(REPLACE(TRIM(centre_name), ' ', '')) = LOWER(REPLACE(TRIM('" . $data['centre_name'] . "'), ' ', ''))";
        // Execute the query
        $result1 = $pdo->query($check_querys);

        // If the place does not exist, insert it
        if ($result1->rowCount() == 0) {
            $insert_query = "INSERT INTO `centre_creation` (
            `centre_id`, `centre_no`, `centre_name`, `mobile1`, `area`, `branch`,`insert_login_id`, `created_on`
        ) VALUES (
            '" . strip_tags($data['centre_id']) . "', 
            '" . strip_tags($data['centre_no']) . "', 
            '" . strip_tags($data['centre_name']) . "', 
            '" . strip_tags($data['centre_mobile']) . "', 
            '" . strip_tags($data['area_id']) . "', 
            '" . strip_tags($data['branch_id']) . "', 
            '" . $user_id . "', 
            NOW()
        );";

            $pdo->query($insert_query);
        }
    }
    function representativeTable($pdo, $data)
    {
        $user_id = $_SESSION['user_id'];

        // Check if the customer exists based on aadhar_number
        $check_queryss = "SELECT centre_id FROM centre_creation WHERE centre_name = '" . strip_tags($data['centre_name']) . "'";

        // Execute the query and fetch the cus_id
        $result1 = $pdo->query($check_queryss);
        $cusData = $result1->fetch(PDO::FETCH_ASSOC);

        // If cus_id is found, proceed with further checks
        if ($cusData) {
            $cus_ids = $cusData['centre_id']; // Get cus_id from query result

            // Check if family member already exists based on the provided details
            $check_query3 = "SELECT centre_id 
                             FROM representative_info 
                             WHERE centre_id = '" . strip_tags($cus_ids) . "' 
                             AND rep_name = '" . strip_tags($data['rep_name']) . "' 
                             AND rep_aadhar = '" . strip_tags($data['rep_aadhar']) . "' 
                             AND rep_mobile = '" . strip_tags($data['rep_mobile']) . "'";

            $result2 = $pdo->query($check_query3);

            // If no family member exists, insert new family member info
            if ($result2->rowCount() == 0) {
                // Prepare the insert query for family_info
                $insert_query1 = "INSERT INTO representative_info (centre_id, rep_name, rep_aadhar, rep_mobile, insert_login_id, created_on) 
                                  VALUES (
                                      '" . strip_tags($cus_ids) . "',
                                      '" . strip_tags($data['rep_name']) . "',
                                      '" . strip_tags($data['rep_aadhar']) . "',
                                      '" . strip_tags($data['rep_mobile']) . "',
                                      '" . $user_id . "',
                                      NOW()
                                  )";

                // Execute the insert query
                $pdo->query($insert_query1);
            }
        } else {
            // Handle case where aadhar_number is not found in customer_creation
            echo "Error: Customer with the given Aadhar number not found.";
        }
    }
    function handleError($data)
    {
        $errcolumns = array();


        if ($data['first_name'] == '') {
            $errcolumns[] = 'First Name';
        }
        if ($data['rep_name'] == '') {
            $errcolumns[] = 'Representative Name';
        }
        if ($data['aadhar_number'] == 'Invalid') {
            $errcolumns[] = 'Customer Aadhar Number';
        }
        if ($data['rep_aadhar'] == 'Invalid') {
            $errcolumns[] = 'Representative Aadhar Number';
        }
        if ($data['mobile1'] == 'Invalid') {
            $errcolumns[] = 'Mobile Number';
        }
        if ($data['rep_mobile'] == 'Invalid') {
            $errcolumns[] = ' Representative Mobile Number';
        }
        if ($data['centre_mobile'] == 'Invalid') {
            $errcolumns[] = 'Centre Mobile Number';
        }
        if (!empty($data['fam_aadhar'])) {
            if ($data['fam_aadhar'] == 'Invalid') {
                $errcolumns[] = 'Family Aadhar';
            }
        }
        if (!empty($data['fam_mobile'])) {
            if ($data['fam_mobile'] == 'Invalid') {
                $errcolumns[] = 'Family Mobile Number';
            }
        }

        if ($data['cus_id'] == 'Not Found') {
            $errcolumns[] = 'Customer ID';
        }
       
        if ($data['centre_name'] == '') {
            $errcolumns[] = 'Centre Name';
        }
        if ($data['centre_area'] == '') {
            $errcolumns[] = 'Centre Name';
        }
        if ($data['centre_no'] == '') {
            $errcolumns[] = 'Centre Number';
        }
        if ($data['branch_id'] == 'Not Found') {
            $errcolumns[] = 'Branch ID';
        }  
        if ($data['area_id'] == 'Not Found') {
            $errcolumns[] = 'Area ID';
        }
        return $errcolumns;
    }
}
