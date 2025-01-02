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
    $loan_id = $customer['loan_id'];

    $qry = $pdo->query("SELECT di.id as d_id, di.*, cc.cus_id,cc.first_name,di.noc_status FROM document_info di LEFT JOIN loan_cus_mapping lcm ON di.cus_id = lcm.id 
 
JOIN
customer_creation cc ON lcm.cus_id = cc.id WHERE di.loan_id = '$loan_id'  ");
    $results = $qry->fetchAll(PDO::FETCH_ASSOC);

    $allNocStatusOne = true;

    foreach ($results as $row) {
        if ($row['noc_status'] != 1) {
            $allNocStatusOne = false;
            break;
        }
    }

    if ($allNocStatusOne) {
        $updateQry = $pdo->prepare("
        UPDATE `loan_entry_loan_calculation` 
        SET `loan_status` = :loan_status 
        WHERE `loan_id` = :loan_id
    ");
        $updateQry->execute([
            ':loan_status' => 11,
            ':loan_id' => $loan_id
        ]);

        echo json_encode(['success' => true]);
        return;
    }



    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No data received']);
}
