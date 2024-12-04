<?php
require '../../ajaxconfig.php';
$id = $_POST['id'];
if ($id != '0' && $id != '') {
    $qry = $pdo->query("SELECT centre_id FROM centre_creation WHERE id = '$id'");
    $qry_info = $qry->fetch();
    $cus_ID_final = $qry_info['centre_id'];
} else {

    $qry = $pdo->query("SELECT centre_id FROM centre_creation WHERE centre_id !='' ORDER BY id DESC ");
    if ($qry->rowCount() > 0) {
        $qry_info = $qry->fetch(); //LID-101
        $l_no = ltrim(strstr($qry_info['centre_id'], '-'), '-'); 
        $l_no = $l_no+1;
        $cus_ID_final = "M-"."$l_no";
    } else {
        $cus_ID_final = "M-" . "101";
    }
}
echo json_encode($cus_ID_final);
?>
