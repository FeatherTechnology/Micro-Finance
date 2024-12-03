<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
try {
  
        $qry = $pdo->query("DELETE FROM `area_creation` WHERE id = '$id'");
        $qry->execute();
        $result = 1; // Deleted.
   
} catch (PDOException $e) {

        $result = -1; // Indicate a general error.
    
}

echo json_encode($result);
