<?php
require "../../ajaxconfig.php";

// Initialize the response array
$cus_map_arr = array();
$id=$_POST['id'];

// Check if loan_id_calc is not empty or invalid
if ($id != '') {
    // Directly inject the loan_id_calc value into the query (be cautious)
    $qry = $pdo->query("SELECT gcm.id, gcm.loan_id, cc.cus_id, cc.first_name, gcm.customer_mapping, cc.aadhar_number, cc.mobile1, anc.areaname, gcm.designation,gcm.loan_amount,lelc.loan_status
                          FROM loan_cus_mapping gcm 
                          JOIN customer_creation cc ON gcm.cus_id = cc.id 
                          LEFT JOIN area_name_creation anc ON cc.area = anc.id  
                          LEFT JOIN loan_entry_loan_calculation lelc ON gcm.loan_id = lelc.loan_id
                          WHERE lelc.centre_id = '$id' AND (gcm.closed_sub_status IS NULL OR gcm.closed_sub_status = 0 OR gcm.closed_sub_status = 1)");

    // Check if the query returns any rows
    if ($qry->rowCount() > 0) {
        // Fetch the results and process them
        while ($gcm_info = $qry->fetch(PDO::FETCH_ASSOC)) {
            // Map the customer mapping to its corresponding label
            // Add the delete button action
            $gcm_info['action'] = "<span class='icon-trash-2 cusMapDeleteBtn' value='" . $gcm_info['id'] . "'  loan_id='" . $gcm_info['loan_id'] . "' loan_status='" . $gcm_info['loan_status'] . "'></span>";

            // Add the processed record to the response array
            $cus_map_arr[] = $gcm_info;
        }
    }
} else {
    // If loan_id_calc is empty or invalid, return an empty array
    $cus_map_arr = [];
}

// Close the database connection
$pdo = null;

// Return the response as JSON
echo json_encode($cus_map_arr);
?>
