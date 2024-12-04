<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
try {

 
        $qry = $pdo->prepare("DELETE FROM `family_info` WHERE `id` = :id");
        $qry->bindParam(':id', $id, PDO::PARAM_INT);
        if ($qry->execute()) {
            $result = 1; // Deleted.
        } else {
            throw new Exception();
        }
    }
 catch (Exception $e) {
    $result = 2; // Handle general exceptions
}

echo json_encode($result);
