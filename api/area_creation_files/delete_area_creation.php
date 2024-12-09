<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
try {
        $qry = $pdo->prepare("DELETE FROM `area_creation` WHERE id = :id");
        $qry->bindParam(':id', $id, PDO::PARAM_INT);
        $qry->execute();
        $result = 1; // Deleted.
    
} catch (PDOException $e) {
    if ($e->getCode() == '23000') {
        // Integrity constraint violation
        $result = 0; // Already used in another Table.
    } else {
        // Some other error occurred
        $result = -1; // Indicate a general error.
    }
}

echo json_encode($result);