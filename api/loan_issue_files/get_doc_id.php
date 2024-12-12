<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
if ($id != '0' && $id != '') {
    $qry = $pdo->query("SELECT doc_id FROM document_info WHERE id = '$id'");
    $qry_info = $qry->fetch();
    $loan_ID_final = $qry_info['doc_id'];
} else {

    $qry = $pdo->query("SELECT doc_id FROM document_info WHERE doc_id !='' ORDER BY id DESC ");
    if ($qry->rowCount() > 0) {
        $qry_info = $qry->fetch(); //L-101
        $l_no = ltrim(strstr($qry_info['doc_id'], '-'), '-'); 
        $l_no = $l_no+1;
        $loan_ID_final = "D-"."$l_no";
    } else {
        $loan_ID_final = "D-" . "101";
    }
}
echo json_encode($loan_ID_final);
?>