<?php
require '../../ajaxconfig.php';
if (isset($_POST['customers'])) {
    $customers = $_POST['customers'];
    foreach ($customers as $customer) {
        $cus_id = $customer['cus_id'];
        $sub_status = $customer['sub_status'];  
        $remarks = $customer['remarks']; 
        $stmt = $pdo->query("UPDATE `loan_cus_mapping` SET `closed_sub_status`='$sub_status',`closed_remarks`='$remarks' WHERE  `id`='$cus_id'");
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No data received']);
}
$pdo = null; //Close connection.
