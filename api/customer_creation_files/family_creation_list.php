<?php
require '../../ajaxconfig.php';
$cus_id = $_POST['cus_id'];
$family_list_arr = array();
$i = 0;
$qry = $pdo->query("SELECT id,fam_name,fam_relationship,fam_age,fam_occupation,fam_aadhar,fam_mobile FROM family_info WHERE cus_id = '$cus_id' ");

if ($qry->rowCount() > 0) {

    $default_live = " ";

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        // Assign the values to the family list array
        $family_list_arr[$i]['id'] = $row['id'];
        $family_list_arr[$i]['fam_name'] = $row['fam_name'];
        $family_list_arr[$i]['fam_relationship'] = $row['fam_relationship'];
        $family_list_arr[$i]['fam_age'] = $row['fam_age'];
        $family_list_arr[$i]['fam_occupation'] = $row['fam_occupation'];
        $family_list_arr[$i]['fam_aadhar'] = $row['fam_aadhar'];
        $family_list_arr[$i]['fam_mobile'] = $row['fam_mobile'];

        // Construct action buttons
        $action_buttons = "<span class='icon-border_color familyActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;";
        $action_buttons .= "<span class='icon-delete familyDeleteBtn' value='" . $row['id'] . "'></span>";
        $family_list_arr[$i]['action'] = $action_buttons;

        $i++;
    }
}

echo json_encode($family_list_arr);
$pdo = null; // Close Connection




