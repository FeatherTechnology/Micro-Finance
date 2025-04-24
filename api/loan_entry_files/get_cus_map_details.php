<?php
require "../../ajaxconfig.php";

// Initialize the response array
$cus_map_arr = array();

// Get the loan_id_calc from the POST request
$loan_id_calc = isset($_POST['loan_id_calc']) ? $_POST['loan_id_calc'] : ''; // Ensure the variable is set

// Customer mapping array

// Check if loan_id_calc is not empty or invalid
if ($loan_id_calc != '') {
    // Directly inject the loan_id_calc value into the query (be cautious)
    $qry = $pdo->query("SELECT gcm.id, gcm.cus_id as map_id, cc.cus_id, cc.first_name, gcm.customer_mapping, cc.aadhar_number, cc.mobile1, anc.areaname, gcm.designation ,gcm.loan_amount ,gcm.loan_id ,lelc.loan_status,gcm.centre_id
                          FROM loan_cus_mapping gcm 
                          JOIN customer_creation cc ON gcm.cus_id = cc.id 
                          LEFT JOIN area_name_creation anc ON cc.area = anc.id  
                          LEFT JOIN loan_entry_loan_calculation lelc ON lelc.loan_id = gcm.loan_id  
                          WHERE gcm.loan_id = '$loan_id_calc' ");

    // Check if the query returns any rows
    if ($qry->rowCount() > 0) {
        // Fetch the results and process them
        while ($gcm_info = $qry->fetch(PDO::FETCH_ASSOC)) {
            // Map the customer mapping to its corresponding label
            
            // Add the delete button action
            $gcm_info['action'] = "</span><span class='icon-trash-2 cusMapDeleteBtn' value='" . $gcm_info['id'] . "'  profile_id='" . $gcm_info['map_id'] . "' loan_id='" . $gcm_info['loan_id'] . "' loan_status='" . $gcm_info['loan_status'] . "'></span>";

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
