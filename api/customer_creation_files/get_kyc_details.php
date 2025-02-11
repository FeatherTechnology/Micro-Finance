
<?php
require '../../ajaxconfig.php';

$id = isset($_POST['id']) ? $_POST['id'] : ""; // Check if id is set

$response = array();

if ($id !="") {
    $qry = $pdo->prepare("SELECT * FROM `kyc` WHERE id ='$id' ");
    $qry->execute();
    if ($qry->rowCount() > 0) {
        $response = $qry->fetchAll(PDO::FETCH_ASSOC);
    }
}

$pdo = null; // Close connection.

echo json_encode($response);
?>
