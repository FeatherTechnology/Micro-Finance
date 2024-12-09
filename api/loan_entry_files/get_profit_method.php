<?php
require "../../ajaxconfig.php";

$scheme_id = $_POST['scheme_id'];


// Initialize the $response variable
$response = [];
$qry = $pdo->query("SELECT s.due_method, s.benefit_method
FROM `scheme` s
WHERE s.id = '$scheme_id'
GROUP BY s.id ");

if($qry->rowCount() > 0) {
    // Fetch the results if available
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close the connection

// Return the response as a JSON object
echo json_encode($response);
?>
