<?php
require '../../ajaxconfig.php';

$property_list_arr = array();
$centre_id = $_POST['centre_id'];
$i = 0;
$qry = $pdo->query("SELECT * 
FROM representative_info 
WHERE centre_id = '$centre_id'");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

        $row['action'] = "<span class='icon-border_color representActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;<span class='icon-delete representDeleteBtn' value='" . $row['id'] . "'></span>";

        $property_list_arr[$i] = $row; // Append to the array
        $i++;
    }
}

$pdo = null; //Close connection.
echo json_encode($property_list_arr);

