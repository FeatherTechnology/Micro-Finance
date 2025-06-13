<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

// Fetch current status
$qry = $pdo->prepare("SELECT scheme_status FROM scheme WHERE id = ?");
$qry->execute([$id]);
$row = $qry->fetch(PDO::FETCH_ASSOC);
$current_status = $row['scheme_status'];

// Toggle status
$new_status = $current_status == '1' ? '0' : '1';

// Update status
$update_qry = $pdo->prepare("UPDATE scheme SET scheme_status = ? WHERE id = ?");
$success = $update_qry->execute([$new_status, $id]);

$pdo = null; //Close Connection
echo json_encode(['success' => $success, 'new_status' => $new_status]);
