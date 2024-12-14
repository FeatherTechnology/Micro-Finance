<?php
require "../../ajaxconfig.php";
$id = $_POST['id'];
// Delete customer mapping
$qry = $pdo->query("DELETE FROM `loan_cus_mapping` WHERE `id` = '$id'");

if ($qry) {
        $result = 1; // Success (status not found, deletion allowed)
} else {
    $result = 2; // Failure to delete mapping
}

echo json_encode($result);
?>
