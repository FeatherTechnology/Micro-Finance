<?php
require "../../ajaxconfig.php";

$doc_info_arr = array();
$cus_id = $_POST['cus_id'];
$qry = $pdo->query("SELECT di.id as d_id, di.*, cc.cus_id,cc.first_name FROM document_info di LEFT JOIN loan_cus_mapping lcm ON di.cus_id = lcm.id JOIN
customer_creation cc ON lcm.cus_id = cc.id WHERE di.cus_id = '$cus_id' ");
if ($qry->rowCount() > 0) {
    while ($doc_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        $doc_info['doc_type'] = ($doc_info['doc_type'] == '1') ? 'Original' : 'Xerox';
        $doc_info['upload'] = "<a href='uploads/loan_issue/doc_info/".$doc_info['upload']."' target='_blank'>".$doc_info['upload']."</a>";
        $doc_info['action'] = " <span class='icon-trash-2 docDeleteBtn' value='" . $doc_info['d_id'] . "'></span>";
        $doc_info_arr[] = $doc_info;
    }
}
$pdo = null; //Connection Close.
echo json_encode($doc_info_arr);