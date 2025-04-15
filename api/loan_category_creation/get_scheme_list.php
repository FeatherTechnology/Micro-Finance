<?php
require "../../ajaxconfig.php";
$scheme_arr = array();
$enabled_schemes = array(); // Array to store enabled scheme IDs
$qry = $pdo->query("SELECT `id`,`scheme_name`, `due_method`, `benefit_method`, `interest_rate_percent_min`, `due_period_percent_min`, `scheme_penalty_type`, `doc_charge_min`, `doc_charge_max`, `processing_fee_min`, `processing_fee_max`,`overdue_penalty_percent`,`scheme_status` FROM `scheme` ");
if ($qry->rowCount() > 0) {
    while ($scheme_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        if($scheme_info['due_method'] == '1'){
            $scheme_info['due_method'] = 'Monthly';
        } else if($scheme_info['due_method'] == '2'){
            $scheme_info['due_method'] = 'Weekly';
        }
        
        if($scheme_info['benefit_method'] == '1'){
            $scheme_info['benefit_method'] = 'Pre Benefit';
        } else if($scheme_info['benefit_method'] == '2'){
            $scheme_info['benefit_method'] = 'After Benefit';
        }
        
        if($scheme_info['scheme_status'] == '0'){
            $scheme_info['scheme_status'] = 'Disable';
        } else if($scheme_info['scheme_status'] == '1'){
            $scheme_info['scheme_status'] = 'Enable';
            $enabled_schemes[] = $scheme_info['id']; // Collect enabled scheme IDs
        }
        
        $scheme_info['overdue_penalty_percent'] = ($scheme_info['scheme_penalty_type'] == 'percent') ? $scheme_info['overdue_penalty_percent'].'%' : '₹ '.$scheme_info['overdue_penalty_percent'];
        $scheme_info['action'] = "<span class='icon-check1 schemeActiveBtn' data-value='" .  $scheme_info['id'] . "'></span>";
       
        $scheme_arr[] = $scheme_info;
    }
}

$pdo = null; // Close connection.

// Send both the scheme list and enabled scheme IDs as a JSON response
echo json_encode([
    'schemeList' => $scheme_arr,
    'enabledSchemes' => $enabled_schemes
]);
