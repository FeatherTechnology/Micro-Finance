<?php
require '../../ajaxconfig.php';

$areacreation_list_arr = array();
$qry = $pdo->query("SELECT ac.id, ac.status, bc.branch_name, GROUP_CONCAT(anc.areaname ORDER BY anc.areaname SEPARATOR ', ') as areaname 
FROM `area_creation` ac 
LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
LEFT JOIN area_name_creation anc ON FIND_IN_SET(anc.id, ac.area_id) 
WHERE ac.status = 1
GROUP BY ac.id ");
if ($qry->rowCount() > 0) {
    while ($areaCreationInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        $areaCreationInfo['action'] = "<span class='icon-border_color areaCreationActionBtn' value='" . $areaCreationInfo['id'] . "'></span> <span class='icon-trash-2 areaCreationDeleteBtn' value='" . $areaCreationInfo['id'] . "'></span>";
        $areacreation_list_arr[] = $areaCreationInfo; // Append to the array
    }
}

$pdo = null; //Close Connection.

echo json_encode($areacreation_list_arr);