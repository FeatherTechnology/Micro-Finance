<?php
require '../../ajaxconfig.php';


$response = [];

    $qry = $pdo->prepare("
        SELECT anc.id, anc.areaname 
    FROM area_creation ac
    JOIN area_name_creation anc ON FIND_IN_SET(anc.id, ac.area_id)
    WHERE ac.status = 1
    ");
    $qry->execute();
    if ($qry->rowCount() > 0) {
        $response = $qry->fetchAll(PDO::FETCH_ASSOC);
    }
$pdo = null;

// Output the response as JSON
echo json_encode($response);
?>

