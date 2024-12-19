<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];
$created_date = date('Y-m-d');

$column = array(
    'lelc.id',
    'lelc.loan_id',
    'cc.centre_id',
    'cc.centre_no',
    'cc.centre_name',
    'cc.mobile1',
    'anc.areaname',
    'bc.branch_name',
    'lelc.loan_status',
);
$query = "SELECT lelc.id, lelc.loan_id, lelc.centre_id,lelc.loan_amount,lelc.due_start,lelc.due_end, cc.centre_no,cc.centre_name, cc.mobile1,anc.areaname,bc.branch_name,lelc.loan_status,cl.closed_sub_status
 FROM loan_entry_loan_calculation lelc 
 LEFT JOIN centre_creation cc ON lelc.centre_id = cc.centre_id
 LEFT JOIN closed_loan cl ON lelc.loan_id=cl.loan_id
 LEFT JOIN area_name_creation anc ON anc.id = cc.area
 LEFT JOIN branch_creation bc ON bc.id = cc.branch
 LEFT JOIN closed_status cs ON cs.loan_id = lelc.loan_id
 WHERE lelc.loan_status = 8 or lelc.due_end <'$created_date' or lelc.loan_status = 9";

if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $search = $_POST['search'];
        $query .= " AND (lelc.loan_id LIKE '" . $search . "%'
                      OR cc.centre_id LIKE '%" . $search . "%'
                      OR cc.centre_name LIKE '%" . $search . "%'
                      OR cc.centre_no LIKE '%" . $search . "%'
                      OR cc.mobile1 LIKE '%" . $search . "%'
                      OR anc.areaname LIKE '%" . $search . "%'
                      OR bc.branch_name LIKE '%" . $search . "%'
                      OR lelc.loan_status LIKE '%" . $search . "%')";
    }
}

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
foreach ($result as $row) {
    $sub_array = array();

    $sub_array[] = $sno++;
    $sub_array[] = isset($row['loan_id']) ? $row['loan_id'] : '';
    $sub_array[] = isset($row['centre_id']) ? $row['centre_id'] : '';
    $sub_array[] = isset($row['centre_no']) ? $row['centre_no'] : '';
    $sub_array[] = isset($row['centre_name']) ? $row['centre_name'] : '';
    $sub_array[] = isset($row['areaname']) ? $row['areaname'] : '';
    $sub_array[] = isset($row['branch_name']) ? $row['branch_name'] : '';
    $sub_array[] = isset($row['mobile1']) ? $row['mobile1'] : '';
    $sub_array[] = isset($row['due_start']) ? $row['due_start'] : '';
    $sub_array[] = isset($row['due_end']) ? $row['due_end'] : '';
    $sub_array[] = isset($row['loan_amount']) ? $row['loan_amount'] : '';
    $sub_array[] = isset($row['loan_status']) ? ($row['loan_status'] == 8 ? 'Closed' : 'Closed') : "";
    $sub_array[] = isset($row['closed_sub_status']) ? ($row['closed_sub_status'] == 1 ? 'Consider' : ($row['closed_sub_status'] == 2 ? 'Blocked' : '')) : '';
    $chart = "<div class='dropdown'>
    <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
   <div class='dropdown-content'>";
    $chart .= "<a href='#' class='due_chart' value='" . $row['id'] . "' centre_id='" . $row['centre_id'] . "' title='Due Chart'>Due Chart</a>";
    $chart .= "<a href='#' class='penalty_chart' value='" . $row['id'] . "' title='Penalty Chart'>Penalty Chart</a>";
    $chart .= "<a href='#' class='fine_chart' value='" . $row['id'] . "' title='Fine Chart'>Fine Chart</a>";
    $chart .= "</div></div>";
    $sub_array[] = $chart;

    $action = "<div class='dropdown'>
    <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
   <div class='dropdown-content'>";
    if ($row['loan_status'] < '9') {
        $action .= "<a href='#' class='view' value='" . $row['id'] . "' loan_id='" . $row['loan_id'] . "' title='View'>View</a>";
    }
    if ($row['loan_status'] == '9') {
        $action  .= "<a href='#' class='move-noc' value='" . $row['id'] . "' title='Move'>Move</a>";
    }
    $action .= "</div></div>";
    $sub_array[] = $action;
    $data[] = $sub_array;
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
