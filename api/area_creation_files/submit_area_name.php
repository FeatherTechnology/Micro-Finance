<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$areaname = $_POST['areaname'];
$branch_id = $_POST['branch_id'];
$status = $_POST['status'];
$id = $_POST['id'];

$qry = $pdo->query("SELECT * FROM `area_name_creation` WHERE REPLACE(TRIM(areaname), ' ', '') = REPLACE(TRIM('$areaname'), ' ', '') AND branch_id = '$branch_id' and status = '$status' ");
if ($qry->rowCount() > 0) {
    $result = 2; //already Exists.

} else {
    if ($id != '0') {
        if ($status == '0') {
            $checkQry = $pdo->query("SELECT * FROM area_creation where FIND_IN_SET($id,area_id)");
            if ($checkQry->rowCount() > 0) {
                $result = 3; //Already added in Area Creation.

                echo json_encode($result);
                return;
            }
        }
        $pdo->query("UPDATE `area_name_creation` SET `areaname`='$areaname', `branch_id`='$branch_id',`status`='$status',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$id'");
        $result = 0; //update

    } else {
        $pdo->query("INSERT INTO `area_name_creation`(`areaname`, `branch_id`, `status`, `insert_login_id`, `created_on`) VALUES ('$areaname', '$branch_id', '$status', '$user_id', now())");
        $result = 1; //Insert
    }
}

echo json_encode($result);
