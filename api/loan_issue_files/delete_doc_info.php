<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];
$result = 0;

$qry = $pdo->query("SELECT upload FROM `document_info` WHERE id='$id'");

if($qry->rowCount() > 0) {
    $row = $qry->fetch();
    
    // Check if the upload field is not empty before trying to delete the file
    if (!empty($row['upload'])) {
        $filePath = "../../uploads/loan_issue/doc_info/" . $row['upload'];
        
        // Check if the file exists before attempting to delete
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the file
        }
    }
    
    // Delete the record from the database
    $deleteQuery = $pdo->query("DELETE FROM `document_info` WHERE id='$id'");
    
    if ($deleteQuery) {
        $result = 1; // Success
    } else {
        $result = 2; // Deletion failed
    }
}

$pdo = null; // Close connection

echo json_encode($result);
