<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$name = $_POST['name'];
$user_code = $_POST['user_code'];
$role = $_POST['role'];
$designation = $_POST['designation'];
$address = $_POST['address'];
$place = $_POST['place'];
$email = $_POST['email'];
$mobile_no = $_POST['mobile_no'];
$user_name = $_POST['user_name'];
$password = $_POST['password'];
$branch_name = implode(',', $_POST['branch_name']);
$centre_name = implode(',', $_POST['centre_name']);
$loan_category = implode(',', $_POST['loan_category']);
$account_access = implode(',', $_POST['account_access']);
$collection_access = $_POST['collection_access'];
$download_access = $_POST['download_access'];
$submenus = implode(',', $_POST['submenus']);
$id = $_POST['id'];

$qry = $pdo->query("SELECT * FROM users WHERE REPLACE(TRIM(user_name), ' ', '') = REPLACE(TRIM('$user_name'), ' ', '') AND `user_code` !='$user_code' ");
if ($qry->rowCount() > 0) {
    $status ='3';
    $last_id = '0'; //Already exists.
} else {
    if ($id != '0' && $id != '') {
        $qry = $pdo->query("UPDATE `users` SET `name`='$name',`user_code`='$user_code',`role`='$role',`designation`='$designation',`address`='$address',`place`='$place',`email`='$email',`mobile`='$mobile_no',`user_name`='$user_name',`password`='$password',`branch`='$branch_name',`loan_category`='$loan_category',`centre_name`='$centre_name', `collection_access`= '$collection_access',`download_access`='$download_access',`account_access`='$account_access',`screens`='$submenus',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$id'");
        if ($qry) {
            $status = '1';
            $last_id = $id;
        }
    } else {
         $qry = $pdo->query("SELECT user_code FROM users WHERE user_code !='' ORDER BY id DESC ");
        if ($qry->rowCount() > 0) {
            $qry_info = $qry->fetch(); //US-001
            $usr_code_f = substr($qry_info['user_code'], 0, 3);
            $usr_code_s = substr($qry_info['user_code'], 3, 5);
            $final_code = str_pad($usr_code_s + 1, 3, 0, STR_PAD_LEFT);
            $user_code_final = $usr_code_f.$final_code;
        } else {
        $user_code_final = "US-" . "001";
        }
        if($user_code_final !="" &&   $qry ){
             $qry = $pdo->query("INSERT INTO `users`(`name`, `user_code`, `role`, `designation`, `address`, `place`, `email`, `mobile`, `user_name`, `password`, `branch`, `loan_category`, `centre_name`, `collection_access`,`download_access`,`account_access`, `screens`, `insert_login_id`, `created_on`) VALUES ('$name','$user_code_final','$role','$designation','$address','$place','$email','$mobile_no','$user_name','$password','$branch_name','$loan_category','$centre_name', '$collection_access','$download_access','$account_access','$submenus','$user_id',now())");
            if ($qry) {
                $status ='2'; 
                $last_id = $pdo->lastInsertId();
            }

        }
           
    }
}
$result = array('status'=>$status, 'last_id'=> $last_id);
$pdo = null; //Close Connection
echo json_encode($result);