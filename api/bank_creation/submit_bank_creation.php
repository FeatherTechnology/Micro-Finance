<?php
require '../../ajaxconfig.php';
@session_start();

$bank_name = $_POST['bank_name'];
$bank_short_name = $_POST['bank_short_name'];
$account_number = $_POST['account_number'];
$ifsc_code = $_POST['ifsc_code'];
$branch_name = $_POST['branch_name'];
$gpay = $_POST['gpay'];
$under_branch = $_POST['under_branch'];
$created_date = date('Y-m-d H:i:s');


// Set default value for status if not provided
$status = isset($_POST['status']) ? $_POST['status'] : '1'; // default to '1'

$user_id = $_SESSION['user_id'];
$bank_id = $_POST['bank_id'];

$response = 'Error';

try {
    if ($bank_id != '') {
        $stmt = $pdo->query("UPDATE `bank_creation` SET `bank_name`='$bank_name',`bank_short_name`='$bank_short_name',`account_number`='$account_number',`ifsc_code`='$ifsc_code',`branch_name`='$branch_name',`gpay`='$gpay',`under_branch`='$under_branch',`status`='1',`update_login_id`='$user_id',`updated_date`=now() WHERE `id`='$bank_id'");

        if ($stmt) {
            $response = 'Success'; // Update successful
        }
    } else {
        // Use prepared statements for insert
        $stmt = $pdo->query("INSERT INTO `bank_creation`(`bank_name`, `bank_short_name`, `account_number`, `ifsc_code`, `branch_name`, `gpay`, `under_branch`,`status`, `insert_login_id`,`created_date`) VALUES ('$bank_name','$bank_short_name','$account_number','$ifsc_code','$branch_name','$status','$gpay', '$under_branch','$user_id','$created_date')");

        if ($stmt) {
            $response = 'Success'; // Insert successful
        }
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
echo json_encode($response);
