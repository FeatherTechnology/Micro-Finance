<?php
require "../../ajaxconfig.php";
include '../common_files/get_customer_status.php';
$cus_loan_arr = array();
$collectionSts = new CustomerStatus($pdo);
$cus_id = isset($_POST['cus_id']) ? $_POST['cus_id'] : '';

if ($cus_id != '') {
    $qry = $pdo->query("SELECT lelc.loan_date,lelc.loan_id,lc.loan_category,lelc.loan_amount,lcm.id as cus_map_id,lcm.issue_status
from  loan_cus_mapping lcm
left join loan_entry_loan_calculation lelc on lelc.loan_id=lcm.loan_id
left join customer_creation cc on cc.id=lcm.cus_id
left join loan_category lc on lc.id=lelc.loan_category
where lcm.cus_id='$cus_id '");


    if ($qry->rowCount() > 0) {
        while ($gcm_info = $qry->fetch(PDO::FETCH_ASSOC)) {
            $gcm_info['status'] = $collectionSts->custStatus($gcm_info['cus_map_id'],$gcm_info['loan_id']);
            $loan_date = date('d-m-Y', strtotime($gcm_info['loan_date']));
            $gcm_info['loan_date'] = $loan_date;

            if ($gcm_info['issue_status'] == '1') {
                $gcm_info['action'] = "<button class='btn btn-primary view_chart' value='" . $gcm_info['cus_map_id'] . "' loan_id='" . $gcm_info['loan_id'] . "'>Due Chart</button>";
            } else {
                $gcm_info['action'] = "<button class='btn btn-secondary' disabled>Due Chart</button>";
            }
            $cus_loan_arr[] = $gcm_info;
        }
    }
} else {
    $cus_loan_arr = [];
}

$pdo = null;

echo json_encode($cus_loan_arr);
?>
