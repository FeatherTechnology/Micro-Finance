<?php
require '../../ajaxconfig.php';
@session_start();

class centreUploadClass
{
    public function uploadFiletoFolder()
    {
        $excel = $_FILES['excelFile']['name'];
        $excel_temp = $_FILES['excelFile']['tmp_name'];
        $excelfolder = "../../uploads/excel_format/centre_format/" . $excel;

        $fileExtension = pathinfo($excelfolder, PATHINFO_EXTENSION); //get the file extension

        $excel = uniqid() . '.' . $fileExtension;
        while (file_exists("../../uploads/excel_format/centre_format/" . $excel)) {
            // this loop will continue until it generates a unique file name
            $excel = uniqid() . '.' . $fileExtension;
        }
        $excelfolder = "../../uploads/excel_format/centre_format/" . $excel;
        move_uploaded_file($excel_temp, $excelfolder);
        return $excelfolder;
    }

    public function fetchAllRowData($Row)
    {
        $dataArray = array(
            'centre_name' => isset($Row[1]) ? $Row[1] : "",
            'centre_no' => isset($Row[2]) ? $Row[2] : "",
            'centre_mobile' => isset($Row[3]) ? $Row[3] : "",
            'centre_area' => isset($Row[4]) ? $Row[4] : "",
            'branch' => isset($Row[5]) ? $Row[5] : "",
            'rep_name' => isset($Row[6]) ? $Row[6] : "",
            'rep_aadhar' => isset($Row[7]) ? $Row[7] : "",
            'rep_mobile' => isset($Row[8]) ? $Row[8] : "",
        );

            $dataArray['rep_aadhar'] = strlen($dataArray['rep_aadhar']) == 12 ? $dataArray['rep_aadhar'] : 'Invalid';
            $dataArray['rep_mobile'] = strlen($dataArray['rep_mobile']) == 10 ? $dataArray['rep_mobile'] : 'Invalid';
            $dataArray['centre_mobile'] = strlen($dataArray['centre_mobile']) == 10 ? $dataArray['centre_mobile'] : 'Invalid';
            
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
        if ($data['rep_name'] == '') {
            $errcolumns[] = 'Representative Name';
        }
     
        if ($data['rep_aadhar'] == 'Invalid') {
            $errcolumns[] = 'Representative Aadhar Number';
        }
      
        if ($data['rep_mobile'] == 'Invalid') {
            $errcolumns[] = ' Representative Mobile Number';
        }
        if ($data['centre_mobile'] == 'Invalid') {
            $errcolumns[] = ' Centre Mobile Number';
        }
        if ($data['centre_name'] == '') {
            $errcolumns[] = 'Centre Name';
        }
        if ($data['centre_area'] == '') {
            $errcolumns[] = 'Centre Area';
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
