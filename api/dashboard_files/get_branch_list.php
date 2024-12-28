<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$result =array();
$qry=$pdo->query("SELECT b.branch_name ,b.id FROM users u JOIN branch_creation b ON FIND_IN_SET(b.id, u.branch)WHERE u.id = '$user_id'");
if($qry->rowCount()>0){
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo=null; //Close Connection.

echo json_encode($result);
?>