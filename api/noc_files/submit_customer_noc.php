<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];
$current_date = date('Y-m-d');
if (isset($_POST['customers'])) {
    $customers = $_POST['customers'];
    foreach ($customers as $customer) {
        $loan_id = $customer['loan_id'];
        $date_of_noc = $customer['date_of_noc'];  
        $customer_name = $customer['customer_name']; 
        $doc_id = $customer['doc_id'];
       
        $stmt = $pdo->query("UPDATE `document_info` SET `noc_status`='1',`date_of_noc`=' $date_of_noc',`hand_over_person`='$customer_name',`update_login_id`='$user_id',`updated_on`='$current_date' WHERE  doc_id='$doc_id '");
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No data received']);
}
