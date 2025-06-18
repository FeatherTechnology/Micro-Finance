<?php
require '../../ajaxconfig.php';
@session_start();

$company_name = $_POST['company_name'];
$branch_code=$_POST['branch_code'];
$branch_name=$_POST['branch_name'];
$address = $_POST['address'];
$state = $_POST['state'];
$district = $_POST['district'];
$taluk = $_POST['taluk'];
$place = $_POST['place'];
$pincode = $_POST['pincode'];
$email_id = $_POST['email_id'];
$mobile_number = $_POST['mobile_number'];
$whatsapp = $_POST['whatsapp'];
$landline = $_POST['landline'];
$landline_code = $_POST['landline_code'];
$user_id = $_SESSION['user_id'];
$branchid = $_POST['branchid'];
$current_date = date('Y-m-d ');


if($branchid !='0' && $branchid !=''){
    $qry = $pdo->query("UPDATE `branch_creation` SET `company_name`='$company_name',`branch_code`='$branch_code',`branch_name`='$branch_name',`address`='$address',`state`='$state',`district`='$district',`taluk`='$taluk',`place`='$place',`pincode`='$pincode',`email_id`='$email_id',`mobile_number`='$mobile_number',`whatsapp`='$whatsapp',`landline_code`='$landline_code',`landline`='$landline',`update_login_id`='$user_id',updated_date = '$current_date' WHERE `id`='$branchid'");
    $result = 0; //update

}else{
    $qry = $pdo->query("INSERT INTO `branch_creation`(`company_name`, `branch_code`,`branch_name`,`address`, `state`, `district`, `taluk`, `place`, `pincode`,`email_id`, `mobile_number`, `whatsapp`, `landline_code`,`landline`, `insert_login_id`,`created_date`) VALUES ('$company_name','$branch_code', '$branch_name','$address','$state','$district','$taluk','$place','$pincode','$email_id','$mobile_number','$whatsapp','$landline_code','$landline','$user_id','$current_date')");
    $result = 1; //Insert
}

$pdo = null; //Close connection.
echo json_encode($result);
?>