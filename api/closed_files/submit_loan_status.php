<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$loan_id = $_POST['loan_id']; 
$sub_status = $_POST['sub_status']; 
$centre_id = $_POST['centre_id']; 
$closed_date = date('Y-m-d');

$qry = $pdo->query("INSERT INTO `closed_loan`( `loan_id`,centre_id, `closed_sub_status`, `closed_date`, `insert_login_id`) VALUES ('$loan_id','$centre_id','$sub_status','$closed_date','$user_id') ");
if($qry ){
    $query = $pdo->query("UPDATE `loan_entry_loan_calculation` SET `loan_status`= 9 ,`updated_on` = '$closed_date' WHERE loan_id='$loan_id'");
    if($query){
        $result="0";
    }
    else{
        $result="1";
    }
}
else{
    $result= "2";

}
$pdo = null;
echo json_encode($result);