<?php
require '../../ajaxconfig.php';

$loan_id = $_POST['loan_id'];
$qry = $pdo->query("SELECT loan_id FROM loan_entry_loan_calculation WHERE id = '$loan_id'");
$loan_cat_id =$qry->fetchAll(PDO::FETCH_ASSOC);

if(isset($loan_cat_id[0]['loan_id'])) {
    $loanId = $loan_cat_id[0]['loan_id'];
    $mappingCountStmt = $pdo->query("SELECT COUNT(*) FROM loan_cus_mapping WHERE loan_id = '$loanId'");
    $current_mapping_count = $mappingCountStmt->fetchColumn();

    $cus = $pdo->query("SELECT total_customer FROM loan_entry_loan_calculation WHERE loan_id = '$loanId'");
    $totalCustomer =$cus->fetchAll(PDO::FETCH_ASSOC);
    $total_cus = $totalCustomer[0]['total_customer'];
    
    if($current_mapping_count==$total_cus ){
        $result="0";
    }
    else if($current_mapping_count>$total_cus){
        $result= "1";
    }
    else if($current_mapping_count<$total_cus){
        $result= "2";
    }
    echo json_encode( $result);


}
?>