<?php
require('../../../ajaxconfig.php');
if(isset($_POST['centre_id'])){
$centre_id = $_POST['centre_id'];

}else if(isset($_POST['aadhar_num'])){
    $aadhar_num =$_POST['aadhar_num'];
}

if(isset($_POST['centre_id'])){
$row = array();
$qry = $pdo->query("SELECT cc.aadhar_number FROM `loan_cus_mapping`lcm LEFT join customer_creation cc on cc.id = lcm.cus_id WHERE lcm.`centre_id`='$centre_id' GROUP by lcm.cus_id");

if ($qry->rowCount() > 0) {
    $row = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null;
echo json_encode($row);

} else{
    $result = array();
    $qry = $pdo->query("SELECT cus_id , first_name FROM `customer_creation` WHERE aadhar_number = '$aadhar_num' ORDER BY id DESC LIMIT 1");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}else{
    $result = 'New';
}
$pdo = null; //Close connection.

echo json_encode($result);
}


