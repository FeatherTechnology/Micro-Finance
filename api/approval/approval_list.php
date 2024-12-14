<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$column = array(
    'lelc.id',
    'lelc.loan_id',
    'lc.loan_category',
    'cc.centre_id',
    'cc.centre_no',
    'cc.centre_name',
    'cc.mobile1',
    'anc.areaname',
    'bc.branch_name',
    'lelc.loan_status', 
    'lelc.id'
);
$query = "SELECT lelc.id, lelc.loan_id, lelc.centre_id, cc.centre_no,cc.centre_name, cc.mobile1,anc.areaname,cc.centre_limit, bc.branch_name ,lc.loan_category,lelc.loan_status
 FROM loan_entry_loan_calculation lelc 
 LEFT JOIN loan_category lc ON lc.id= lelc.loan_category  
 LEFT JOIN centre_creation cc ON lelc.centre_id = cc.centre_id
 LEFT JOIN area_name_creation anc ON anc.id = cc.area
 LEFT JOIN branch_creation bc ON bc.id = cc.branch
 WHERE lelc.loan_status = 3";

if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $search = $_POST['search'];
        $query .= " AND (lelc.loan_id LIKE '" . $search . "%'
                      OR lc.loan_category LIKE '%" . $search . "%'
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
    $sub_array[] = isset($row['loan_category']) ? $row['loan_category'] : '';
    $sub_array[] = isset($row['centre_id']) ? $row['centre_id'] : '';
    $sub_array[] = isset($row['centre_no']) ? $row['centre_no'] : '';
    $sub_array[] = isset($row['centre_name']) ? $row['centre_name'] : '';
    $sub_array[] = isset($row['mobile1']) ? $row['mobile1'] : '';
    $sub_array[] = isset($row['areaname']) ? $row['areaname'] : '';
    $sub_array[] = isset($row['branch_name']) ? $row['branch_name'] : '';
    $sub_array[] =isset($row['loan_status']) ? ($row['loan_status'] == 3 ? 'In Approval' : $row['loan_status']) : '';
    $action = "<div class='dropdown'>
    <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
   <div class='dropdown-content'>";
    if ($row['loan_status'] == '3') {
        $action .= "<a href='#' class='approval-edit' value='" . $row['id'] . "' centre_id='" . $row['centre_id'] . "' title='Edit details'>Edit</a>";
        $action .= "<a href='#' class='approval-approve' value='" . $row['id'] . "'  centre_limit='" . $row['centre_limit'] . "' title='Approve'>Approve</a>";
        $action .= "<a href='#' class='approval-cancel' value='" . $row['id'] . "' title='Cancel'>Cancel</a>";
        $action .= "<a href='#' class='approval-revoke' value='" . $row['id'] . "' title='Revoke'>Revoke</a>";
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
