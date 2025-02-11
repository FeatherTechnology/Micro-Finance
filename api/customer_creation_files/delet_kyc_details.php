
<?php
require '../../ajaxconfig.php';

$id = isset($_POST['id']) ? $_POST['id'] : ""; // Check if id is set



if ($id !="") {
   $qry = $pdo->query("DELETE FROM `kyc` WHERE id='$id'");
}
$result=0;
if($qry){
    $result=1;
}

$pdo = null; // Close connection.

echo json_encode($result);
?>
