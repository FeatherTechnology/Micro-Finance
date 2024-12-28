<?php
require '../../ajaxconfig.php';
include '../adminclass.php';
$loan_id = $_POST['loan_id'];
$obj = new overallClass();
$cus_sts_arr = array();

if ($loan_id!= '') {
    $qry = $pdo->query("SELECT  cc.cus_id ,cc.first_name ,cm.id,cm.closed_sub_status,cm.closed_remarks from loan_cus_mapping cm
    left join  customer_creation cc ON cm.cus_id = cc.id  
    WHERE cm.loan_id='$loan_id'");
    if ($qry->rowCount() > 0) {
        while ($gcs_info = $qry->fetch(PDO::FETCH_ASSOC)) {
            $gcs_info['status'] = $obj->getCustomerStatus($pdo, $gcs_info['id']);
            $action = "<div class='dropdown'>
            <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
           <div class='dropdown-content'>";
                $action .= "<a href='#' class='cus_due_chart' value='" . $gcs_info['id'] . "'  title='Due Chart'>Due Chart</a>";
                $action .= "<a href='#' class='cus_penalty_chart' value='" . $gcs_info['id'] . "' title='Penalty Chart'>Penalty Chart</a>";
                $action .= "<a href='#' class='cus_fine_chart' value='" . $gcs_info['id']. "' title='Fine Chart'>Fine Chart</a>";
            $action .= "</div></div>";
            $gcs_info['chart'] = $action;
            $cus_sts_arr[] = $gcs_info;
        }
    }
} else {
    $cus_sts_arr = [];
}


$pdo = null;
echo json_encode($cus_sts_arr);
