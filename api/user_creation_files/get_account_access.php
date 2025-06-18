<?php
require "../../ajaxconfig.php";
$result =array();

$qry=$pdo->query("SELECT  account_access FROM user where");
if($qry->rowCount()>0){
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo=null; //Close Connection.

$pdo = null; //Close Connection
echo json_encode($result);
?>