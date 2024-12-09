<?php
require "../../ajaxconfig.php";

$scheme_id = $_POST['scheme_id'];
$scheme_arr = array();
$qry = $pdo->query("SELECT `id`,`scheme_name`, `due_method`, `benefit_method`, `interest_rate_percent_min`, `interest_rate_percent_max`,`due_period_percent_min`, `due_period_percent_max`,`scheme_penalty_type`, `doc_charge_min`, `doc_charge_max`, `processing_fee_min`, `processing_fee_max`,`overdue_penalty_percent` FROM `scheme` WHERE FIND_IN_SET(id, '$scheme_id') ");
if ($qry->rowCount() > 0) {
    while ($scheme_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        if($scheme_info['due_method'] =='1'){
            $scheme_info['due_method'] = 'Monthly';

        }else if($scheme_info['due_method'] =='2'){
            $scheme_info['due_method'] = 'Weekly';   

        }
        if($scheme_info['benefit_method'] =='1'){
            $scheme_info['benefit_method'] = 'Pre Benefit';

        }else if($scheme_info['benefit_method'] =='2'){
            $scheme_info['benefit_method'] = 'After Benefit';   

        }
        $scheme_info['overdue_penalty_percent'] = ($scheme_info['scheme_penalty_type'] == 'percent') ? $scheme_info['overdue_penalty_percent'].'%' : '₹ '.$scheme_info['overdue_penalty_percent'];
        $scheme_arr[] = $scheme_info;
    }
}

$pdo = null; //Connection Close.

echo json_encode($scheme_arr);
