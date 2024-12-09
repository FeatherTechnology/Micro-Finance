<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
$centre_id = $_POST['centre_id'];
$centre_create_id = $_POST['centre_create_id'];


try {
    $qry = $pdo->query("SELECT * FROM representative_info WHERE centre_id = '$centre_id' ");
    if ($qry->rowCount() == 1 && $centre_create_id != '') { //If Only one count of kyc for the customer then restrict to delete.
        $result = '0';
    } else {
        $qry = $pdo->prepare("DELETE FROM `representative_info` WHERE `id` = :id");
        $qry->bindParam(':id', $id, PDO::PARAM_INT);
        if ($qry->execute()) {
            $result = 1; // Deleted.
        } else {
            throw new Exception();
        }
    }
} catch (Exception $e) {
    $result = 2; // Handle general exceptions
}

echo json_encode($result);