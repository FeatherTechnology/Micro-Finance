<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];
$loan_status = [1 => 'Process', 2 => 'Created'];
$column = array(
    'lelc.id',
    'lelc.loan_id',
    'cc.centre_id',
    'cc.centre_no',
    'cc.centre_name',
    'cc.mobile1',
    'anc.areaname',
    'lc.loan_category',
    'lelc.loan_amount',
    'lelc.id',
    'lelc.id'
);

$query = "SELECT lelc.id as loan_calc_id, lelc.loan_id, lc.loan_category, cc.centre_id, cc.centre_no, cc.centre_name, cc.mobile1, anc.areaname, lelc.loan_amount, lelc.loan_status,lelc.loan_date ,lelc.due_month, lelc.due_start, lelc.scheme_date, lelc.scheme_day_calc
          FROM loan_entry_loan_calculation lelc
          LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
          LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
          LEFT JOIN centre_creation cc ON lelc.centre_id = cc.centre_id
          LEFT JOIN area_name_creation anc ON cc.area = anc.id
          JOIN users us ON FIND_IN_SET(lelc.loan_category, us.loan_category)
          WHERE lelc.loan_status = 4 AND us.id = '$user_id'";

if (isset($_POST['search']) && $_POST['search'] != "") {
    $search = $_POST['search'];
    $query .= " AND (lelc.loan_id LIKE '" . $search . "%'
                      OR lc.loan_category LIKE '%" . $search . "%'
                      OR cc.centre_id LIKE '%" . $search . "%'
                      OR cc.centre_name LIKE '%" . $search . "%'
                      OR cc.centre_no LIKE '%" . $search . "%'
                      OR cc.mobile1 LIKE '%" . $search . "%'
                      OR anc.areaname LIKE '%" . $search . "%'
                      OR lelc.loan_amount LIKE '%" . $search . "%'
                      OR lelc.loan_status LIKE '%" . $search . "%')";
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


    // Check if loan_date is empty or null
    if (empty($row['loan_date'])) {
        $issue_status = 'In Issue';
    } else {
        $issue_status = 'Pending';
    }

    if ($row['due_month'] == 1) {
        // For Monthly due method
        $due_date = $row['due_start'];
        $scheme_day = $row['scheme_date'];

        $year = date('Y', strtotime($due_date));
        $month = date('m', strtotime($due_date));

        $date_day = date('d-m-Y', strtotime($scheme_day . '-' . $month . '-' . $year));
    } else {
        $daysOfWeek = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday'
        ];
        $scheme_day = $row['scheme_day_calc'];
        $date_day = $daysOfWeek[$scheme_day];
    }
    // Add the issue status to the response data
    $sub_array[] = $issue_status;
    $sub_array = [];
    $sub_array[] = $sno++;
    $sub_array[] = isset($row['loan_id']) ? $row['loan_id'] : '';
    $sub_array[] = isset($date_day) ?  $date_day : '';
    $sub_array[] = isset($row['centre_id']) ? $row['centre_id'] : '';
    $sub_array[] = isset($row['centre_no']) ? $row['centre_no'] : '';
    $sub_array[] = isset($row['centre_name']) ? $row['centre_name'] : '';
    $sub_array[] = isset($row['mobile1']) ? $row['mobile1'] : '';
    $sub_array[] = isset($row['areaname']) ? $row['areaname'] : '';
    $sub_array[] = isset($row['loan_category']) ? $row['loan_category'] : '';
    $sub_array[] = isset($row['loan_amount']) ?moneyFormatIndia($row['loan_amount']): '';
    $sub_array[] = $issue_status;

    $action = "<div class='dropdown'>
                <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
               <div class='dropdown-content'>";
    $action .= "<a href='#' class='edit-loan-issue' value='" . $row['loan_id'] . "' title='Edit details'>Edit</a>";
    $action .= "</div></div>";
    $sub_array[] = $action;

    $data[] = $sub_array;
}

function count_all_data($pdo) {
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

$pdo = null; //Close Connection
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
?>
