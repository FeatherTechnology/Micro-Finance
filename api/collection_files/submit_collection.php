<?php
require '../../ajaxconfig.php';
include '../common_files/get_customer_status.php';
@session_start();
$currentDate = date('Y-m-d');
$obj = new CustomerStatus($pdo);
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
$loanFineAmnt = $_POST['loanFineAmnt'];
$collectionData = $_POST['collectionData'];  // This is an array containing individual row data
$overall_collection = 0;
$overall_fine = 0;
// Loop through each row of collectionData to insert individual rows
foreach ($collectionData as $data) {
  $cus_mapping_id = $data['id'];  // Get customer mapping ID from the row
  $due_amnt = $data['individual_amount'];
  $pending_amt = $data['pending'];
  $payable_amt = $data['payable'];
  $fine_charge = $data['fine_charge'];
  $coll_date = date('Y-m-d', strtotime($data['collection_date']));
  $collection_due = $data['collection_due'];
  $collection_savings = $data['collection_savings'];
  $collection_fine = $data['collection_fine'];
  $total_collection = $data['total_collection'];
  // Insert data into the collection table
  if ($collection_due >= $payable_amt) {
    $coll_status = '1';
  } else {
    $coll_status = '2';
  }
  
  $query =  $pdo->query("INSERT INTO `collection` (
                `loan_id`, `cus_mapping_id`, `loan_total_amnt`, `loan_paid_amnt`, `loan_balance`, `loan_due_amnt`, 
                `loan_pending_amnt`, `loan_payable_amnt`, `loan_fine`, `coll_status`,`status`,`sub_status`, `due_amnt`, 
                `pending_amt`, `payable_amt`, `fine_charge`, `coll_date`, `due_amt_track`, 
                `penalty_track`, `fine_charge_track`, `total_paid_track`, `insert_login_id`, 
                `created_on`) 
              VALUES (
                '$loan_id', '$cus_mapping_id', '$loanTotalAmnt', '$loanPaidAmnt', '$loanBalance', '$loanDueAmnt', 
                '$loanPendingAmnt', '$loanPayableAmnt', '$loanFineAmnt', '$coll_status','$status','$sub_status', '$due_amnt', 
                '$pending_amt', '$payable_amt', '$fine_charge', '$coll_date " . date(' H:i:s') . "', '$collection_due', 
                '$collection_savings', '$collection_fine', '$total_collection', '$user_id', 
                CURRENT_TIMESTAMP())");
  if ($query) {
    $response = 1;  // If both queries executed successfully
  } else {
    $response = 0;  // If any query failed
  }

  if (($collection_savings != '' and $collection_savings > 0)) {
    $qry1 = $pdo->query("INSERT INTO `customer_savings`(`cus_map_id`,`loan_id`, `paid_date`, `savings_amount`) VALUES ('$cus_mapping_id','$loan_id','$coll_date','$collection_savings') ");
  }

  if ($collection_fine != '' and $collection_fine > 0) {
    $qry2 = $pdo->query("INSERT INTO `fine_charges`(`cus_mapping_id`,`loan_id`, `paid_date`, `paid_amnt`) VALUES ('$cus_mapping_id','$loan_id','$coll_date','$collection_fine')");
  }

  $customer_status = $obj->custStatus($cus_mapping_id, $loan_id);
  
  $status = $customer_status['status'];  // Get the status
  $balanceAmount = $customer_status['balanceAmount'];  // Get the individual amount
  
  $payable_amount = intval($payable_amt) - $collection_due;
  $payable_amnts = ($payable_amount > 0) ? $payable_amount : 0;

  if ($query) {
    $qry2 = $pdo->query("UPDATE `customer_status` SET `sub_status`=' $status',`payable_amount`='$payable_amnts',`balance_amount`='$balanceAmount',`insert_login_id`='$user_id',`created_date`='$currentDate'WHERE  `cus_map_id`='$cus_mapping_id' and `loan_id`='$loan_id'");
  }
 
  $overall_collection += $collection_due;
  $overall_fine += $collection_fine;
}

  $check = intval($overall_collection)  - intval($loanBalance);
  $fine_charge_check = intval($overall_fine)  - intval($loanFineAmnt);

  if ($check == 0 && $fine_charge_check == 0) {
    $closedQry = $pdo->query("UPDATE `loan_entry_loan_calculation` SET `loan_status`='8',`update_login_id`='$user_id',`updated_on`=now() WHERE `loan_id`='$loan_id' "); //balance is zero change the customer status as 8, moved to closed.
    if ($closedQry) {
      $result = '3';
    }
  }


// Return response to the client
echo json_encode($response);
