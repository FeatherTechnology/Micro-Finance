<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

include '../collection_files/reset_customer_status.php';
$centre_sub_status = [ null =>'',''=>'',1 => 'Consider',2=> 'Blocked'];
$collectionSts = new CollectStsClass($pdo);
$current_date = date('Y-m-d');
$column = array(
    'lelc.id',
    'lelc.loan_id',
    'cc.centre_id',
    'cc.centre_no',
    'cc.centre_name',
    'bc.branch_name',
    'lelc.id',
    'lelc.id',
    'lelc.id'
);
$query = "SELECT lelc.id as loan_calc_id, lelc.loan_id, cc.centre_id, cc.centre_no, cc.centre_name, bc.branch_name,lelc.loan_status,lelc.loan_amount,lelc.due_end,cl.closed_sub_status
          FROM loan_entry_loan_calculation lelc
          LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
          LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
          LEFT JOIN centre_creation cc ON lelc.centre_id = cc.centre_id
          LEFT JOIN loan_cus_mapping lcm ON lelc.loan_id = lcm.loan_id
          LEFT JOIN closed_loan cl ON lelc.loan_id = cl.loan_id
          LEFT JOIN branch_creation bc ON cc.branch = bc.id
 	JOIN users us ON FIND_IN_SET(lelc.loan_category, us.loan_category)
    WHERE us.id = '$user_id' 
  AND ('$current_date' > lelc.due_end OR lelc.loan_status >= 8) ";
if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $search = $_POST['search'];
        $query .= " AND (lelc.loan_id LIKE '" . $search . "%'
                      OR cc.centre_id LIKE '%" . $search . "%'
                      OR cc.centre_name LIKE '%" . $search . "%'
                      OR cc.centre_no LIKE '%" . $search . "%'
                      OR lelc.loan_amount LIKE '%" . $search . "%'
                      OR bc.branch_name LIKE '%" . $search . "%')";
    }
}
$query .= "GROUP BY lelc.loan_id ";

// Ordering functionality
if (isset($_POST['order'])) {
    $columnIndex = $_POST['order'][0]['column'];  // Index of the column to be sorted
    $sortDirection = $_POST['order'][0]['dir'];  // Sort direction (asc/desc)
    
    if (isset($column[$columnIndex])) {
        // Apply sorting using the column and direction provided
        $query .= " ORDER BY " . $column[$columnIndex] . " " . $sortDirection;
    }
} else {
    // Default sorting (if no sorting is applied from frontend)
    $query .= ' ORDER BY cc.id DESC';
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

    $status = $collectionSts->updateCollectStatus($row['loan_id']);
    if($status == "Paid"){
        $collection_status = "Completed";
    }else{
        $collection_status = "In Collection";
    }
    $centre_status = 'Closed';
    $sub_array = array();

    $sub_array[] = $sno++;

    $sub_array[] = isset($row['centre_id']) ? $row['centre_id'] : '';
    $sub_array[] = isset($row['centre_no']) ? $row['centre_no'] : '';
    $sub_array[] = isset($row['centre_name']) ? $row['centre_name'] : '';
    $sub_array[] = isset($row['loan_id']) ? $row['loan_id'] : '';
    $sub_array[] = isset($row['loan_amount']) ? moneyFormatIndia($row['loan_amount']) : '';
    $sub_array[] = isset($row['branch_name']) ? $row['branch_name'] : '';
    $sub_array[] = $centre_status;
    $sub_array[] = $centre_sub_status[$row['closed_sub_status']];
    $sub_array[] = $collection_status;
    $sub_array[] = "<button class='btn btn-primary closedViewBtn' data-loan-id='" . $row['loan_id'] . "' data-centre-id='" . $row['centre_id'] . "'>&nbsp;View</button>";
    $sub_array[] = "<button class='btn btn-primary closeledgerViewBtn' value='" . $row['loan_id'] . "'>&nbsp;Ledger View</button>";
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

$pdo = null; //Close connection.
echo json_encode($output);
function moneyFormatIndia($num1)
{
    if ($num1 < 0) {
        $num = str_replace("-", "", $num1);
    } else {
        $num = $num1;
    }
    $explrestunits = "";
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3, strlen($num));
        $restunits = substr($num, 0, strlen($num) - 3);
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
        $expunit = str_split($restunits, 2);
        for ($i = 0; $i < sizeof($expunit); $i++) {
            if ($i == 0) {
                $explrestunits .= (int)$expunit[$i] . ",";
            } else {
                $explrestunits .= $expunit[$i] . ",";
            }
        }
        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $num;
    }

    if ($num1 < 0 && $num1 != '') {
        $thecash = "-" . $thecash;
    }

    return $thecash;
}
