<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];


$loan_id = $_POST['loan_id'];
$doc_id = $_POST['doc_id'];
$customer_name = $_POST['customer_name'];
$doc_name = $_POST['doc_name'];
$doc_type = $_POST['doc_type'];
$doc_count = $_POST['doc_count'];
$remark = $_POST['remark'];

if (!empty($_FILES['doc_upload']['name'])) {
    $path = "../../uploads/loan_issue/doc_info/";
    $picture = $_FILES['doc_upload']['name'];
    $pic_temp = $_FILES['doc_upload']['tmp_name'];
    $picfolder = $path . $picture;
    $fileExtension = pathinfo($picfolder, PATHINFO_EXTENSION); //get the file extention
    $picture = uniqid() . '.' . $fileExtension;
    while (file_exists($path . $picture)) {
        //this loop will continue until it generates a unique file name
        $picture = uniqid() . '.' . $fileExtension;
    }
    move_uploaded_file($pic_temp, $path . $picture);
} else {
    $picture = (isset($_POST['doc_upload_edit'])) ? $_POST['doc_upload_edit'] : '';
}

$status = 0;
    $qry = $pdo->query("INSERT INTO `document_info`(`loan_id`,`doc_id`,`cus_id`,`doc_name`, `doc_type`, `count`, `remark`, `upload`, `insert_login_id`, `created_on`) VALUES ('$loan_id','$doc_id','$customer_name','$doc_name','$doc_type','$doc_count','$remark','$picture','$user_id',now())");
    $status = 1; //Insert


    $pdo = null; //Close Connection
echo json_encode($status);