<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

$checkQry = $pdo->query("SELECT * FROM area_creation where FIND_IN_SET($id,area_id)");
if ($checkQry->rowCount() > 0) {
    $result = 0; //Already added in Area Creation.
} else {
    $qry = $pdo->query("DELETE FROM `area_name_creation` WHERE id = '$id'");
    $result = 1; // Deleted.
}

echo json_encode($result);
