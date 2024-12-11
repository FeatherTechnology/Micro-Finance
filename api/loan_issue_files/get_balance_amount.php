<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$qry = $pdo->query("
    SELECT COALESCE(SUM(issue_amount), 0) as issue_amount 
FROM loan_issue 
WHERE cus_mapping_id = '$id'
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetch(PDO::FETCH_ASSOC);
} else {
    $result = ['issue_amount' => 0]; // Default to 0 if no records found
}

$pdo = null; // Close connection
echo json_encode($result);
?>