<?php
require "../../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$qry = $pdo->query("SELECT account_access FROM users u  where u.id = '$user_id'");

if ($qry->rowCount() > 0) {
    $row = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close Connection.
echo json_encode($row);

?>