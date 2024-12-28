<?php
require '../../ajaxconfig.php';
include 'loanIssueUploadClass.php';
require_once('../../vendor/csvreader/php-excel-reader/excel_reader2.php');
require_once('../../vendor/csvreader/SpreadsheetReader_XLSX.php');


$obj = new loanIssueUploadClass();

$allowedFileType = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'text/csv', 'text/xml', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
if (in_array($_FILES["excelFile"]["type"], $allowedFileType)) {

    $excelfolder = $obj->uploadFiletoFolder();

    $Reader = new SpreadsheetReader_XLSX($excelfolder);
    $sheetCount = count($Reader->sheets());

    for ($i = 0; $i < $sheetCount; $i++) {

        $Reader->ChangeSheet($i);
        $rowChange = 0;
        foreach ($Reader as $Row) {
            if ($rowChange != 0) { // omitted 0,1 to avoid headers

                $data = $obj->fetchAllRowData($Row);
                $centre_id = $obj->centreName($pdo, $data['centre_name']);
                $data['centre_id'] = $centre_id;

                $cust_id = $obj->getCustomerId($pdo, $data['cus_aadhar']);
                $data['cust_id'] = $cust_id;
             
                $err_columns = $obj->handleError($data);
                if (empty($err_columns)) {
                    // Call LoanEntryTables function
                    $obj->cusMappingTable($pdo, $data);
                    $cus_mapping_id = $obj->getCustomerMappingId($pdo, $data['cust_id'], $data['loan_id']);
                    $data['cus_mapping_id'] = $cus_mapping_id;
                    $cus_data_response = $obj->checkCustomerData($pdo, $data['cust_id']);
                    $data['cus_data'] = $cus_data_response['cus_data'];
               
                    if ($data['cus_data'] == 'Existing' && isset($cus_data_response['cus_status'])) {
                        $data['cus_status'] = $cus_data_response['cus_status'];
                    } else {
                        $data['cus_status'] = ''; // Default for new customers or if 'cus_status' is not provided
                    }
                    $obj->loanIssueTable($pdo, $data);
                    $obj->statusUpdate($pdo, $data);
                } else {
                    $errtxt = "Please Check the input given in Serial No: " . ($rowChange) . " on below. <br><br>";
                    $errtxt .= "<ul>";
                    foreach ($err_columns as $columns) {
                        $errtxt .= "<li>$columns</li>";
                    }
                    $errtxt .= "</ul><br>";
                    $errtxt .= "Insertion completed till Serial No: " . ($rowChange - 1);
                    echo $errtxt;
                    exit();
                }
            }

            $rowChange++;
        }
    }
    $message = 'Bulk Upload Completed.';
} else {
    $message = 'File is not in Excel Format.';
}

echo $message;
