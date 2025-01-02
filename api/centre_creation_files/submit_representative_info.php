<?php
require '../../ajaxconfig.php';
@session_start();

$centre_id = $_POST['centre_id']; // Add this line to get the customer ID
$rep_name = $_POST['rep_name'];
$rep_aadhar = $_POST['rep_aadhar'];
$rep_occupation = $_POST['rep_occupation'];
$rep_mobile = $_POST['rep_mobile'];
$designation = $_POST['designation'];
$remark = $_POST['remark'];
$user_id = $_SESSION['user_id']; // Corrected session variable name
$represent_id = $_POST['represent_id'];
$current_date = date('Y-m-d ');

if ($represent_id != '') {
    $qry = $pdo->query("UPDATE `representative_info` SET `centre_id`='$centre_id', `rep_name`='$rep_name', `rep_aadhar`='$rep_aadhar', `rep_occupation`='$rep_occupation', `rep_mobile`='$rep_mobile', `designation`='$designation', `remark`='$remark', `update_login_id`='$user_id', updated_on = '$current_date' WHERE `id`='$represent_id'");
    $result = 0; // Update
} else {
    $qry = $pdo->query("INSERT INTO `representative_info`(`centre_id`, `rep_name`, `rep_aadhar`, `rep_occupation`, `rep_mobile`, `designation`, `remark`, `insert_login_id`, `created_on`) VALUES ('$centre_id', '$rep_name', '$rep_aadhar', '$rep_occupation', '$rep_mobile', '$designation', '$remark', '$user_id', '$current_date')");
    $result = 1; // Insert
}

echo json_encode($result);
?>
