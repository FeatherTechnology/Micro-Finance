<?php
require '../../ajaxconfig.php';

    $loan_id = $_POST['loan_id'];
    $remark = $_POST['remark'];
    $loan_sts = $_POST['loan_sts'];

 if ($loan_id != ''){
        // Update customer_profile table
        $qry = $pdo->query("UPDATE loan_entry_loan_calculation SET `remarks` = '$remark' ,`loan_status` = '$loan_sts' WHERE `id` = '$loan_id'");
        $result = 0;
 }
       
echo json_encode($result);
?>


