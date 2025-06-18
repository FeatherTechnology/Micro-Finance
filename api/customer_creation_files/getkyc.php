<?php
require '../../ajaxconfig.php';

$cus_id = $_POST['cus_id'];
$kyc_list = array();

$qry = $pdo->query("SELECT * FROM `kyc` WHERE cus_id='$cus_id '");

if ($qry->rowCount() > 0) {
    while ($data = $qry->fetch(PDO::FETCH_ASSOC)) {
        $action_buttons = "<span class='icon-border_color kycedit' value='" . $data['id'] . "'></span>&nbsp;&nbsp;&nbsp;";
        $action_buttons .= "<span class='icon-delete kycdelet' value='" . $data['id'] . "'></span>";
        $data['action'] = $action_buttons;
        $kyc_list[] = $data;
    }
}
$pdo = null; //Close Connection
echo json_encode($kyc_list);

?>