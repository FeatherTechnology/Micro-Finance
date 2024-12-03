<?php
require '../../ajaxconfig.php';

if (!empty($_FILES['document']['name'])) {
    $path = "../../uploads/company_creation/documents/";
    $picture = $_FILES['document']['name'];
    $pic_temp = $_FILES['document']['tmp_name'];
    $picfolder = $path . $picture;
    $fileExtension = pathinfo($picfolder, PATHINFO_EXTENSION); //get the file extention
    $picture = uniqid() . '.' . $fileExtension;
    while (file_exists($path . $picture)) {
        //this loop will continue until it generates a unique file name
        $picture = uniqid() . '.' . $fileExtension;
    }
    move_uploaded_file($pic_temp, $path . $picture);
} else {
    $picture = $_POST['per_pic'];
}

$document_name = $_POST['document_name'];



$qry = $pdo->query("INSERT INTO `company_document`(`document_name`, `file`) VALUES ('$document_name', '$picture')");

if($qry){
    $result=1;
}
else{
    $result= 0;
}
$pdo = null; 
echo json_encode($result);
?>