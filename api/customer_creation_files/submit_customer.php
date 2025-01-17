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
$cus_status = ''; 
$qry = $pdo->query("SELECT lelc.loan_status 
              FROM customer_creation cc
              LEFT JOIN loan_cus_mapping lcm ON cc.id = lcm.cus_id
              LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id where cc.cus_id='$cus_id' AND lelc.loan_status>=1");

if ($qry->rowCount() > 0) {
    $result = $qry->fetch();
    $status = $result['loan_status'];  // Customer status from the customer_status table

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
try {
    // Begin transaction
    $pdo->beginTransaction();

    // Check if customer exists based on Aadhar number
    $check_query = $pdo->query("SELECT * FROM customer_creation WHERE aadhar_number = '$aadhar_number'");

    // Get the latest customer ID
    $selectIC = $pdo->query("SELECT cus_id FROM customer_creation WHERE cus_id != '' ORDER BY id DESC LIMIT 1 FOR UPDATE");

if ($check_query->rowCount() > 0) {
    $row = $check_query->fetch();
    $customer_profile_id = $row['id']; // Get the existing customer's ID
    $qry = $pdo->query("UPDATE `customer_creation` SET `cus_id`='$cus_id', `aadhar_number`='$aadhar_number', `cus_data`='$cus_data',`cus_status`='$cus_status',`first_name`='$first_name', `last_name`='$last_name', `dob`='$dob',`age`='$age',`area`='$area', `mobile1`='$mobile1', `mobile2`='$mobile2', `whatsapp`='$whatsapp', `occupation`='$occupation',`occ_detail`='$occ_detail',`address`='$address', `native_address`='$native_address',`pic`='$picture',`multiple_loan`='$multiple_loan',  `update_login_id`='$user_id', updated_on = now() WHERE `id`='$customer_profile_id'");
    $result = 0; // Update
} else {
    if ($selectIC->rowCount() > 0) {
        $row = $selectIC->fetch();
        $ac2 = $row["cus_id"];
        $appno2 = ltrim(strstr($ac2, '-'), '-'); // Extract numeric part after the hyphen
        $appno2 = $appno2 + 1;
        $cus_id = "C-" . $appno2;
    } else {
        $cus_id = "C-101"; // If no previous customer ID exists, start with C-1
    } 
    
    $qry = $pdo->query("INSERT INTO `customer_creation`(`cus_id`,`aadhar_number`, `cus_data`,`cus_status`,`first_name`, `last_name`,`dob`,`age`,`area`, `mobile1`, `mobile2`, `whatsapp`,`occupation`,`occ_detail`, `address`, `native_address`,`pic`, `multiple_loan`, `insert_login_id`, `created_on`) VALUES ('$cus_id','$aadhar_number', '$cus_data','$cus_status','$first_name', '$last_name','$dob','$age','$area', '$mobile1', '$mobile2', '$whatsapp','$occupation','$occ_detail', '$address', '$native_address', '$picture','$multiple_loan', '$user_id', now())");
    $result = 1; // Insert

}
   // Commit transaction
   $pdo->commit();

} catch (Exception $e) {
    // Rollback the transaction on error
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
    exit;
}
  echo json_encode($result);
