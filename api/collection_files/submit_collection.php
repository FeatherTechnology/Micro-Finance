<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

// Get the data from the AJAX request
$loan_id = $_POST['loan_id'];
$sub_status = $_POST['sub_status'];
$status = $_POST['status'];
$loanTotalAmnt = $_POST['loanTotalAmnt'];
$loanPaidAmnt = $_POST['loanPaidAmnt'];
$loanBalance = $_POST['loanBalance'];
$loanDueAmnt = $_POST['loanDueAmnt'];
$loanPayableAmnt = $_POST['loanPayableAmnt'];
$loanPendingAmnt = $_POST['loanPendingAmnt'];
$loanPenaltyAmnt = $_POST['loanPenaltyAmnt'];
$loanFineAmnt = $_POST['loanFineAmnt'];
$collectionData = $_POST['collectionData'];  // This is an array containing individual row data
$totalCollectionPenalty = 0;
$totalCollectionFine = 0;
// Loop through each row of collectionData to insert individual rows
foreach ($collectionData as $data) {
  $cus_mapping_id = $data['id'];  // Get customer mapping ID from the row
  $due_amnt = $data['individual_amount'];
  $pending_amt = $data['pending'];
  $payable_amt = $data['payable'];
  $penalty = $data['penalty'];
  $fine_charge = $data['fine_charge'];
  $coll_date = date('Y-m-d', strtotime($data['collection_date']));
  $collection_due = $data['collection_due'];
  $collection_penalty = $data['collection_penalty'];
  $collection_fine = $data['collection_fine'];
  $total_collection = $data['total_collection'];
  // Insert data into the collection table
  $totalCollectionPenalty += $collection_penalty;
  $totalCollectionFine += $collection_fine;

  if ($collection_due >= $payable_amt) {
    $coll_status = '1';
  } else {
    $coll_status = '2';
  }

  $query =  $pdo->query("INSERT INTO `collection` (
                `loan_id`, `cus_mapping_id`, `loan_total_amnt`, `loan_paid_amnt`, `loan_balance`, `loan_due_amnt`, 
                `loan_pending_amnt`, `loan_payable_amnt`, `loan_penalty`, `loan_fine`, `coll_status`,`status`,`sub_status`, `due_amnt`, 
                `pending_amt`, `payable_amt`, `penalty`, `fine_charge`, `coll_date`, `due_amt_track`, 
                `penalty_track`, `fine_charge_track`, `total_paid_track`, `insert_login_id`, 
                `created_on`) 
              VALUES (
                '$loan_id', '$cus_mapping_id', '$loanTotalAmnt', '$loanPaidAmnt', '$loanBalance', '$loanDueAmnt', 
                '$loanPendingAmnt', '$loanPayableAmnt', '$loanPenaltyAmnt', '$loanFineAmnt', '$coll_status','$status','$sub_status', '$due_amnt', 
                '$pending_amt', '$payable_amt', '$penalty', '$fine_charge', '$coll_date ". date(' H:i:s') ."', '$collection_due', 
                '$collection_penalty', '$collection_fine', '$total_collection', '$user_id', 
                CURRENT_TIMESTAMP())");
  if ($query) {
    $response = 1;  // If both queries executed successfully
  } else {
    $response = 0;  // If any query failed
  }

  if (($collection_penalty != '' and $collection_penalty > 0)) {
    $qry1 = $pdo->query("INSERT INTO `penalty_charges`(`cus_mapping_id`,`loan_id`, `paid_date`, `paid_amnt`, `created_date`) VALUES ('$cus_mapping_id','$loan_id','$coll_date','$collection_penalty', current_timestamp) ");
  }
  
  if ($collection_fine != '' and $collection_fine > 0) {
    $qry2 = $pdo->query("INSERT INTO `fine_charges`(`cus_mapping_id`,`loan_id`, `paid_date`, `paid_amnt`) VALUES ('$cus_mapping_id','$loan_id','$coll_date','$collection_fine')");
  }
  
}



$paidCustomersCount = $pdo->query("SELECT SUM(due_amt_track) as paid_count 
FROM collection WHERE loan_id = '$loan_id' ")->fetch(PDO::FETCH_ASSOC)['paid_count'];
$check = intval($loanTotalAmnt)  - intval($paidCustomersCount);

$penalty_check = intval($loanPenaltyAmnt) - intval($totalCollectionPenalty);
$fine_charge_check = intval($loanFineAmnt) - intval($totalCollectionFine);

if ($check == 0 && $penalty_check == 0 && $fine_charge_check == 0) {
  $closedQry = $pdo->query("UPDATE `loan_entry_loan_calculation` SET `loan_status`='8',`update_login_id`='$user_id',`updated_on`=now() WHERE `loan_id`='$loan_id' "); //balance is zero change the customer status as 8, moved to closed.
  if ($closedQry) {
    $result = '3';
  }
}
// Return response to the client
echo json_encode($response);
