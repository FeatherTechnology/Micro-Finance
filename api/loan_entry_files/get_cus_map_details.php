<?php
require "../../ajaxconfig.php";

$cus_map_arr = array();
$loan_id_calc = $_POST['loan_id_calc'];
$qry = $pdo->query("SELECT gcm.id, cc.cus_id, cc.first_name, cc.cus_data, cc.aadhar_number, cc.mobile1, anc.areaname
                    FROM loan_cus_mapping gcm 
                    JOIN customer_creation cc ON gcm.cus_id = cc.id 
                    LEFT JOIN area_name_creation anc ON cc.area = anc.id  
                    WHERE gcm.loan_id = '$loan_id_calc'");
if ($qry->rowCount() > 0) {
    while ($gcm_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        $gcm_info['action'] = "<span class='icon-trash-2 cusMapDeleteBtn' value='" . $gcm_info['id'] . "'></span>";
        
        // Adding input field in 'designation' column
        $gcm_info['designation'] = '
                                        <div class="input-container">
                                            <input type="number" name="cus_value[]" class="form-control" value="" placeholder="Enter Designation">
                                        </div>
                                    ';

        $cus_map_arr[] = $gcm_info;
    }
}

$pdo = null; // Connection Close.
echo json_encode($cus_map_arr);
