<?php
require '../../ajaxconfig.php';
$cus_id = $_POST['cus_id'];
$trans_list_arr = array();
$qry = $pdo->query("SELECT
    c.id,
    c.cus_id,
    c.first_name,
    lelc.loan_id,
    lcm.id as map_id,
    lelc.loan_status,
    cc.centre_id,
    cc.centre_name
FROM
    loan_entry_loan_calculation lelc
LEFT JOIN loan_cus_mapping lcm ON
    lcm.centre_id = lelc.centre_id
LEFT JOIN customer_creation c ON
    lcm.cus_id = c.id
LEFT JOIN centre_creation cc ON
    lcm.centre_id = cc.centre_id
WHERE
    c.cus_id='$cus_id'");

if ($qry->rowCount() > 0) {
    while ($result = $qry->fetch()) {
        if($result['loan_status']>7){
            $result['centre_status'] = "Closed";
        } 
        elseif($result['loan_status']<=7 && $result['loan_status']!=6 && $result['loan_status']!=5 ){
            $result['centre_status'] = "Current";

        }
        $result['action'] = "<button class='btn btn-primary addDocument' value='" . $result['map_id'] . "'>&nbsp;Add Document</button>";

        $trans_list_arr[] = $result;
        
    }
}

echo json_encode($trans_list_arr);
$pdo = null; // Close Connection




