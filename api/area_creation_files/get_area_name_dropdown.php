<?php
require "../../ajaxconfig.php";
$branch_id = $_POST['branch_id'];

$areaname_arr = array();

$result = $pdo->query("SELECT id,areaname,status FROM area_name_creation WHERE branch_id ='$branch_id' AND status=1");
while ($row = $result->fetch()) {
    $id = $row['id'];
    $areaname = $row['areaname'];

    $checkQry = $pdo->query("SELECT * FROM area_creation where status=1 and FIND_IN_SET($id,area_id)");
    if ($checkQry->rowCount() > 0) {
        $disabled = true;
    } else {
        $disabled = false;
    }

    $areaname_arr[] = array("id" => $id, "areaname" => $areaname, "disabled" => $disabled);
}

echo json_encode($areaname_arr);
