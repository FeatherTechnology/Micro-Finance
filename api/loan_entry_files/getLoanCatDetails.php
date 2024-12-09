<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
$profitType = $_POST['profitType'];

// Initialize the $response variable
$response = [];

$qry = $pdo->query("SELECT * FROM loan_category_creation WHERE id ='$id' AND FIND_IN_SET('$profitType', profit_type)");

if($qry->rowCount() > 0) {
    // Fetch the results if available
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close the connection

// Return the response as a JSON object
echo json_encode($response);
?>
