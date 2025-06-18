<?php
require('../../../ajaxconfig.php');
if(isset($_POST['centre_id'])){
$centre_id = $_POST['centre_id'];

}else if(isset($_POST['aadhar_num'])){
    $aadhar_num =$_POST['aadhar_num'];
}

if(isset($_POST['centre_id'])){
$row = array();
$qry = $pdo->query("SELECT DISTINCT lcm.cus_id, cc.first_name, cc.aadhar_number FROM loan_cus_mapping lcm
JOIN loan_entry_loan_calculation lelc  ON lcm.loan_id = lelc.loan_id 
JOIN customer_creation cc ON lcm.cus_id = cc.id
WHERE lcm.centre_id = '$centre_id' AND lelc.loan_status IN (4 ,7, 8)");

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


