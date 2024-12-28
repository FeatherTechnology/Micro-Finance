<?php
include '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];



$column = array(
    'lelc.loan_id',
    'li.issue_date',
    'cntr.centre_id',
    'cntr.centre_name',
    'anc.areaname',
    'bc.branch_name',
    'cc.mobile1',
    'lc.loan_category'
   
);


$query = "SELECT lelc.due_month , lelc.due_start , lelc.scheme_date , lelc.scheme_day_calc,lelc.loan_id,  lelc.loan_date , lelc.loan_amount_calc , lelc.due_end , cntr.centre_id , cntr.centre_no , cntr.centre_name , bc.branch_name , cc.mobile1, lc.loan_category, cl.closed_date, cl.closed_sub_status FROM loan_entry_loan_calculation lelc JOIN centre_creation cntr ON cntr.centre_id = lelc.centre_id JOIN loan_cus_mapping lcm ON lcm.loan_id = lelc.loan_id JOIN branch_creation bc ON cntr.branch = bc.id JOIN customer_creation cc ON cc.id = lcm.cus_id JOIN loan_category lc ON lc.id = lelc.loan_category JOIN closed_loan cl ON cl.centre_id = cntr.centre_id where cl.closed_date BETWEEN '$from_date' AND '$to_date' GROUP by lelc.loan_id";


if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $query .= " and (anc.areaname LIKE '%" . $_POST['search'] . "%' OR
            lelc.loan_id LIKE '%" . $_POST['search'] . "%' OR
            cntr.centre_id LIKE '%" . $_POST['search'] . "%' OR
            cntr.centre_id LIKE '%" . $_POST['search'] . "%' OR
            lc.loan_category LIKE '%" . $_POST['search'] . "%' OR
            lelc.due_end LIKE '%" . $_POST['search'] . "%' OR
            cl.closed_date LIKE '%" . $_POST['search'] . "%' OR
            bc.branch_name LIKE '%" . $_POST['search'] . "%' ) ";
    }
}
if (isset($_POST['order'])) {
    $query .= " ORDER BY " . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
} else {
    $query .= ' ';
}

$query1 = "";
if ($_POST['length'] != -1) {
    $query1 = " LIMIT " . $_POST['start'] . ", " . $_POST['length'];
}

$statement = $pdo->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $pdo->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();

$data = array();
$sno = 1;
foreach ($result as $row) {
    $sub_array   = array();
    if ($row['due_month'] == 1) {
        // For Monthly due method
        $due_date = $row['due_start'];
        $scheme_day = $row['scheme_date'];

        $year = date('Y', strtotime($due_date));
        $month = date('m', strtotime($due_date));

        $combined_date = date('d-m-Y', strtotime($scheme_day . '-' . $month . '-' . $year));
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
        $combined_date = $daysOfWeek[$scheme_day];
    }


    $sub_array[] = $sno;
    $sub_array[] = isset($combined_date) ?  $combined_date : '';
    $sub_array[] = isset($row['loan_id']) ? $row['loan_id'] : '';
    $sub_array[] = isset($row['loan_date']) ? date('d-m-Y', strtotime($row['loan_date'])) : '';
    $sub_array[] = isset($row['centre_id']) ? $row['centre_id'] : '';
    $sub_array[] = isset($row['centre_no']) ? $row['centre_no'] : '';
    $sub_array[] = isset($row['centre_name']) ? $row['centre_name'] : '';
    $sub_array[] = isset($row['branch_name']) ? $row['branch_name'] : '';
    $sub_array[] = isset($row['mobile1']) ? $row['mobile1'] : '';
    $sub_array[] = isset($row['loan_category']) ? $row['loan_category'] : '';
    $sub_array[] = moneyFormatIndia($row['loan_amount_calc']);
    $sub_array[] = date('d-m-Y', strtotime($row['due_end']));
    $sub_array[] = date('d-m-Y', strtotime($row['closed_date']));
    $sub_array[] = isset($row['closed_sub_status']) ? ($row['closed_sub_status'] == 1 ? 'Consider' : ($row['closed_sub_status'] == 2 ? 'Blocked' : '')) : '';

    $data[]      = $sub_array;
    $sno = $sno + 1;
}

function count_all_data($pdo)
{
    $query = "SELECT id FROM loan_entry_loan_calculation where loan_status > 8 ";
    $statement = $pdo->prepare($query);
    $statement->execute();
    return $statement->rowCount();
}

$output = array(
    'draw' => intval($_POST['draw']),
    'recordsTotal' => count_all_data($pdo),
    'recordsFiltered' => $number_filter_row,
    'data' => $data
);

echo json_encode($output);
function moneyFormatIndia($num)
{
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
    return $thecash;
}
