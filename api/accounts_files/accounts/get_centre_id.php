<?php
require('../../../ajaxconfig.php');
@session_start();
$user_id = $_SESSION['user_id'];


$row = array();
$qry = $pdo->query("SELECT c.centre_id
FROM users u
JOIN centre_creation c 
  ON FIND_IN_SET(c.id, u.centre_name)
WHERE u.id = '$user_id'");
if ($qry->rowCount() > 0) {
    $row = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close Connection.
echo json_encode($row);