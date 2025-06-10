<?php
require '../../ajaxconfig.php';
@session_start();
if (!empty($_FILES['pic']['name'])) {
    $path = "../../uploads/centre_creation/centre_pic/";
    $picture = $_FILES['pic']['name'];
    $pic_temp = $_FILES['pic']['tmp_name'];
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
$centre_id = $_POST['centre_id'];
$centre_no = $_POST['centre_no'];
$centre_name = $_POST['centre_name'];
$mobile1 = $_POST['mobile1'];
$mobile2 = $_POST['mobile2'];
$area = $_POST['area'];
$branch = $_POST['branch'];
$latlong = $_POST['latlong'];
$centre_create_id = $_POST['centre_create_id'];
$user_id = $_SESSION['user_id'];
$current_date = date('Y-m-d ');

if ($centre_create_id != '') {
    $qry = $pdo->query("UPDATE `centre_creation` SET `centre_id`='$centre_id', `centre_no`='$centre_no',`centre_name`='$centre_name', `mobile1`='$mobile1', `mobile2`='$mobile2', `area`='$area', `branch`='$branch',`latlong`='$latlong', `pic`='$picture',`update_login_id`='$user_id', updated_on = '$current_date' WHERE `id`='$centre_create_id'");
    $result = 0; // Update
} else {
      $qry = $pdo->query("SELECT centre_id FROM centre_creation WHERE centre_id !='' ORDER BY id DESC ");
    if ($qry->rowCount() > 0) {
        $qry_info = $qry->fetch(); //LID-101
        $l_no = ltrim(strstr($qry_info['centre_id'], '-'), '-'); 
        $l_no = $l_no+1;
        $cus_ID_final = "M-"."$l_no";
    } else {
        $cus_ID_final = "M-" . "101";
    }
    $qry = $pdo->query("INSERT INTO `centre_creation`(`centre_id`, `centre_no`,`centre_name`,`mobile1`, `mobile2`, `area`, `branch`,`latlong`,`pic`, `insert_login_id`, `created_on`) VALUES ('$cus_ID_final', '$centre_no','$centre_name','$mobile1', '$mobile2', '$area', '$branch','$latlong', '$picture', '$user_id', '$current_date')");
    $result = 1; // Insert
}

echo json_encode($result);
?>
