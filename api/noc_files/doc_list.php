<?php
require "../../ajaxconfig.php";

$doc_info_arr = array();
$loan_id = $_POST['loan_id'];
$qry = $pdo->query("SELECT di.id as d_id, di.*, cc.cus_id,cc.first_name FROM document_info di LEFT JOIN loan_cus_mapping lcm ON di.cus_id = lcm.id 
 
JOIN
customer_creation cc ON lcm.cus_id = cc.id WHERE di.loan_id = '$loan_id' ");
if ($qry->rowCount() > 0) {
    while ($doc_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        $doc_info['doc_type'] = ($doc_info['doc_type'] == '1') ? 'Original' : 'Xerox';
        
        if (!empty($doc_info['hand_over_person'])) {
            $doc_info['action'] = "<input type='checkbox' class='docCheckbox'  data-id='".'1'."' checked />";
        } else {
            $doc_info['action'] = "<input type='checkbox' class='docCheckbox'  data-id='".'0'."' />";
        }
        $doc_info_arr[] = $doc_info;
    }
}
$pdo = null; //Connection Close.
echo json_encode($doc_info_arr);