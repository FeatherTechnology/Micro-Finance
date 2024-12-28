<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];
include './reset_customer_status.php';

$collectionSts = new CollectStsClass($pdo);
$column = array(
    'lelc.id',
    'lelc.loan_id',
    'cc.centre_id',
    'cc.centre_no',
    'cc.centre_name',
    'cc.mobile1',
    'anc.areaname',
    'bc.branch_name',
    'lelc.id', 
    'lelc.id',
    'lelc.id',
);
$query = "SELECT lelc.id as loan_calc_id, lelc.loan_id, cc.centre_id, cc.centre_no, cc.centre_name, cc.mobile1, anc.areaname, bc.branch_name,lelc.sub_status,lelc.due_end
          FROM loan_entry_loan_calculation lelc
          LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
          LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
          LEFT JOIN centre_creation cc ON lelc.centre_id = cc.centre_id
          LEFT JOIN loan_cus_mapping lcm ON lelc.loan_id = lcm.loan_id
          LEFT JOIN area_name_creation anc ON cc.area = anc.id
          LEFT JOIN branch_creation bc ON cc.branch = bc.id
 	JOIN users us ON FIND_IN_SET(lelc.loan_category, us.loan_category)
     WHERE lcm.issue_status = '1' AND us.id ='$user_id' ";
if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $search = $_POST['search'];
        $query .= " AND (lelc.loan_id LIKE '" . $search . "%'
                      OR cc.centre_id LIKE '%" . $search . "%'
                      OR cc.centre_name LIKE '%" . $search . "%'
                      OR cc.centre_no LIKE '%" . $search . "%'
                      OR cc.mobile1 LIKE '%" . $search . "%'
                      OR anc.areaname LIKE '%" . $search . "%'
                      OR bc.branch_name LIKE '%" . $search . "%')";
    }
}
 $query .= "GROUP BY lelc.loan_id ";



if (isset($_POST['order'])) {
    $query .= " ORDER BY " . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
} else {
    $query .= ' ';
}
$query1 = '';
if (isset($_POST['length']) && $_POST['length'] != -1) {
    $query1 = ' LIMIT ' . intval($_POST['start']) . ', ' . intval($_POST['length']);
}
$statement = $pdo->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $pdo->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();
$sno = isset($_POST['start']) ? $_POST['start'] + 1 : 1;
$data = [];
$data = [];
foreach ($result as $row) {
    if ($row['sub_status'] != 2) {
        $status = $collectionSts->updateCollectStatus($row['loan_id']);
        if (date('Y-m-d') > $row['due_end']) {
            $coll_status = "Closed";
        } else {
            $coll_status = "Present";
        }
        
        $sub_array = array();

        $sub_array[] = $sno++;
        $sub_array[] = isset($row['loan_id']) ? $row['loan_id'] : '';
        $sub_array[] = isset($row['centre_id']) ? $row['centre_id'] : '';
        $sub_array[] = isset($row['centre_no']) ? $row['centre_no'] : '';
        $sub_array[] = isset($row['centre_name']) ? $row['centre_name'] : '';
        $sub_array[] = isset($row['mobile1']) ? $row['mobile1'] : '';
        $sub_array[] = isset($row['areaname']) ? $row['areaname'] : '';
        $sub_array[] = isset($row['branch_name']) ? $row['branch_name'] : '';
     $sub_array[] = $status;
        $action= "<div class='dropdown'>
                    <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
                   <div class='dropdown-content'>";
    
      $action .= "<a href='#' class='pay-due' data-value='" . $row['loan_id'] . "_" . $row['centre_id'] . "_" . $row['centre_name'] ."_" . $status . "_" . $coll_status . "' title='Pay Fine'>Pay Due</a>";
    
        
      
            $action  .= "<a href='#' class='pay-fine' value='" . $row['loan_id'] . "' title='Fine'>Fine</a>";
        
        $action .= "</div></div>";
        $sub_array[] = $action;
        $sub_array[] = "<button class='btn btn-primary ledgerViewBtn' value='" . $row['loan_id'] . "'>&nbsp;Ledger View</button>";
        $data[] = $sub_array;
    } else {
        $status = $collectionSts->updateCollectStatus($row['loan_id']);
    }
    
}
function count_all_data($pdo)
{
    $query = "SELECT COUNT(*) FROM loan_entry_loan_calculation";
    $statement = $pdo->prepare($query);
    $statement->execute();
    return $statement->fetchColumn();
}

$output = array(
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    'recordsTotal' => count_all_data($pdo),
    'recordsFiltered' => $number_filter_row,
    'data' => $data
);

echo json_encode($output);

