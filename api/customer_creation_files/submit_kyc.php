<?php
require '../../ajaxconfig.php';

if (!empty($_FILES['upload']['name'])) {
    $path = "../../uploads/customer_creation/kyc/";
    $picture = $_FILES['upload']['name'];
    $pic_temp = $_FILES['upload']['tmp_name'];
    $fileExtension = pathinfo($picture, PATHINFO_EXTENSION); 
    $picture = uniqid() . '.' . $fileExtension;
    while (file_exists($path . $picture)) {
        $picture = uniqid() . '.' . $fileExtension;
    }
    move_uploaded_file($pic_temp, $path . $picture);
} else {
    $picture = $_POST['upload_files'] ?? '';
}


$lable = $_POST['lable'];
$Details = $_POST['Details'];
$cus_id = $_POST['cus_id'];
$kycID = $_POST['kycID'];
// $upload_files = $_POST['upload_files'];

$result=0;
if ($kycID===""){
    $qry = $pdo->query("INSERT INTO `kyc`( `cus_id`, `label`, `details`, `upload`) VALUES ('$cus_id','$lable','$Details','$picture')");
    $result=1;
}
else{
    $qry = $pdo->query("UPDATE `kyc` SET `cus_id`='$cus_id',`label`='$lable',`details`='$Details',`upload`='$picture' WHERE id='$kycID'");
    $result=2;
}


$pdo = null; 
echo json_encode($result);
?>