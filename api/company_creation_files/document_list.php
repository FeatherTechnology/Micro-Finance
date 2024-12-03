<?php
require '../../ajaxconfig.php';
$doc_need_arr = array();
$qry = $pdo->query("SELECT * FROM company_document where 1 ");
if ($qry->rowCount() > 0) {
    while ($DocNeed_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        $DocNeed_info['upload'] = "<a href='./uploads/company_creation/documents/" . $DocNeed_info['file'] . "' target='_blank'>" . $DocNeed_info['file'] . "</a>";
        $DocNeed_info['action'] = "<span class='icon-trash-2 docNeedDeleteBtn' value='" . $DocNeed_info['s_no'] . "'></span>";
        $doc_need_arr[] = $DocNeed_info;
    }
}
$pdo = null; //Connection Close.
echo json_encode($doc_need_arr);