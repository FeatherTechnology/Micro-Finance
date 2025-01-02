<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$branch_id = $_POST['branch_id'];
$area_id = $_POST['area_id'];
$id = $_POST['id'];
$current_date = date('Y-m-d ');

if ($id != '0') {
    $pdo->query("UPDATE `area_creation` SET `branch_id`='$branch_id',`area_id`='$area_id',`status`='1',`update_login_id`='$user_id',`update_on`='$current_date' WHERE `id`='$id'");
    $result = 0; //update

} else {
    $pdo->query("INSERT INTO `area_creation`(`branch_id`, `area_id`, `insert_login_id`, `created_on`) VALUES ('$branch_id','$area_id','$user_id', '$current_date')");
    $result = 1; //Insert
}

echo json_encode($result);
