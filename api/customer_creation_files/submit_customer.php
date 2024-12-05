<?php
require '../../ajaxconfig.php';
@session_start();
if (!empty($_FILES['pic']['name'])) {
    $path = "../../uploads/customer_creation/cus_pic/";
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
$cus_id = $_POST['cus_id'];
$aadhar_number = $_POST['aadhar_number'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$dob = $_POST['dob'];
$age = $_POST['age'];
$area = $_POST['area'];
$mobile1 = $_POST['mobile1'];
$mobile2 = $_POST['mobile2'];
$whatsapp = $_POST['whatsapp'];
$occupation = $_POST['occupation'];
$occ_detail = $_POST['occ_detail'];
$address = $_POST['address'];
$native_address = $_POST['native_address'];
$multiple_loan = $_POST['multiple_loan'];
$customer_profile_id = $_POST['customer_profile_id'];
$user_id = $_SESSION['user_id'];
// Query to get customer profile along with customer status
$qry = $pdo->query("SELECT cp.*, cs.status 
                    FROM customer_creation cp
                    INNER JOIN customer_status cs ON cp.cus_id = cs.cus_id
                    WHERE cp.cus_id = '$cus_id' 
                    AND cp.id != '$customer_profile_id'");

if ($qry->rowCount() > 0) {
    $result = $qry->fetch();
    $status = $result['status'];  // Customer status from the customer_status table

    // If status is between 1 and 6, cus_status should be empty
    if ($status >= 1 && $status <= 6) {
        $cus_status = '';
    } 
    // If status is 7 or 8, cus_status should be 'Additional'
    else if ($status == 7 || $status == 8) {
        $cus_status = 'Additional';
    } 
    // If status is 9 or above, cus_status should be 'Renewal'
    else if ($status >= 9) {
        $cus_status = 'Renewal';
    }

    $cus_data = 'Existing';  // Since we found a matching record, it's considered 'Existing'
} else {
    $cus_data = 'New';        // No matching record found, it's considered 'New'
    $cus_status = '';         // cus_status should be empty for new customers
}
if ($customer_profile_id != '') {
    $qry = $pdo->query("UPDATE `customer_creation` SET `cus_id`='$cus_id', `aadhar_number`='$aadhar_number', `cus_data`='$cus_data',`cus_status`='$cus_status',`first_name`='$first_name', `last_name`='$last_name', `dob`='$dob',`age`='$age',`area`='$area', `mobile1`='$mobile1', `mobile2`='$mobile2', `whatsapp`='$whatsapp', `occupation`='$occupation',`occ_detail`='$occ_detail',`address`='$address', `native_address`='$native_address',`pic`='$picture',`multiple_loan`='$multiple_loan',  `update_login_id`='$user_id', updated_on = now() WHERE `id`='$customer_profile_id'");
    $result = 0; // Update
} else {
    $qry = $pdo->query("INSERT INTO `customer_creation`(`cus_id`,`aadhar_number`, `cus_data`,`cus_status`,`first_name`, `last_name`,`dob`,`age`,`area`, `mobile1`, `mobile2`, `whatsapp`,`occupation`,`occ_detail`, `address`, `native_address`,`pic`, `multiple_loan`, `insert_login_id`, `created_on`) VALUES ('$cus_id','$aadhar_number', '$cus_data','$cus_status','$first_name', '$last_name','$dob','$age','$area', '$mobile1', '$mobile2', '$whatsapp','$occupation','$occ_detail', '$address', '$native_address', '$picture','$multiple_loan', '$user_id', now())");
    $result = 1; // Insert
    
}

echo json_encode($result);
?>