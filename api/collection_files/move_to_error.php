<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$id = $_POST['id'];
$action = $_POST['action'];
if($action==='move'){
    $qry = $pdo->query("UPDATE `loan_cus_mapping` SET `cus_status`='12' WHERE id='$id' ");
}else if($action==='back'){
    $qry = $pdo->query("UPDATE `loan_cus_mapping` SET `cus_status`='0' WHERE id='$id' ");

}
if($qry){
    $result = 0;
}
else{
    $result = 1;
}
$pdo=null; //Close Connection.
echo json_encode($result);
?>