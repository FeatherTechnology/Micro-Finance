<?php
include '../../ajaxconfig.php';
include '../common_files/get_customer_status.php';
$obj = new CustomerStatus($pdo);
@session_start();
$user_id = $_SESSION['user_id'];

$search_date = $_POST['search_date'];
$due_type = $_POST['due_type'];



$column = array(
    'lelc.loan_id',
    'lelc.loan_id',
    'lelc.loan_id',
    'lelc.loan_date',
    'lelc.due_end',
    'cntr.centre_id',
    'cntr.centre_name',
    'cc.cus_id',
    'cc.first_name',
    'cc.mobile1',
    'anc.areaname',
    'lelc.loan_amount_calc'

);


$query = "SELECT
    lelc.due_month,
    lelc.due_start,
    lelc.scheme_date,
    lelc.scheme_day_calc,
    lelc.loan_id,
    lelc.loan_date,
    lelc.due_end,
    lelc.due_period,
    cntr.centre_id,
    cntr.centre_no,
    cntr.centre_name,
    cc.mobile1,
    cc.first_name,
    cc.cus_id,
    anc.areaname,
    lelc.loan_amount_calc,
    cl.closed_sub_status,
    lcm.id
FROM
    loan_entry_loan_calculation lelc
JOIN centre_creation cntr ON
    cntr.centre_id = lelc.centre_id
JOIN loan_cus_mapping lcm ON
    lcm.loan_id = lelc.loan_id
JOIN area_name_creation anc ON
    cntr.area = anc.id
JOIN customer_creation cc ON
    cc.id = lcm.cus_id
left JOIN closed_loan cl ON
    cl.centre_id = cntr.centre_id
JOIN loan_issue li ON
    li.loan_id = lelc.loan_id
WHERE
    li.issue_date <= '$search_date' and lelc.due_month='$due_type'  GROUP by lcm.id";


if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $query .= " and (anc.areaname LIKE '%" . $_POST['search'] . "%' OR
            lelc.loan_id LIKE '%" . $_POST['search'] . "%' OR
            cntr.centre_id LIKE '%" . $_POST['search'] . "%' OR
            cntr.centre_no LIKE '%" . $_POST['search'] . "%' OR
            cc.mobile1 LIKE '%" . $_POST['search'] . "%' OR
            cc.first_name LIKE '%" . $_POST['search'] . "%' OR
            cc.cus_id LIKE '%" . $_POST['search'] . "%' OR
            lc.loan_category LIKE '%" . $_POST['search'] . "%' OR
            lelc.due_end LIKE '%" . $_POST['search'] . "%' OR
            cl.closed_date LIKE '%" . $_POST['search'] . "%' ) ";
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
    $loan_id = $row['loan_id'];
    $centre = $row['centre_id'];
    $query = $pdo->query("SELECT `total_amount_calc`,`loan_id`,`total_customer`,`due_period` FROM `loan_entry_loan_calculation` WHERE`loan_id`='$loan_id' and  centre_id='$centre'");
    $issueDate = $query->fetch(PDO::FETCH_ASSOC);
    $loan_amount = round(floatval($issueDate['total_amount_calc']) / floatval($issueDate['total_customer']));
    $due_amount = round($loan_amount / floatval($issueDate['due_period']));

$cus_mapping_id=$row['id'];
    $customer_status = $obj->custStatus($cus_mapping_id, $loan_id);
    $status = $customer_status['status'];
    $pending = $customer_status['pendings'];
    $payable = $customer_status['payable'];
    $totalPaidAmt = $customer_status['totalPaidAmt'];
    $paid_due=$totalPaidAmt/$due_amount;
    $balance_due=$issueDate['due_period']-$paid_due;
    $balance_due_pending = number_format($balance_due, 2, '.', '');


    $sub_array[] = $sno;
    $sub_array[] = isset($combined_date) ?  $combined_date : '';
    $sub_array[] = isset($row['loan_id']) ? $row['loan_id'] : '';
    $sub_array[] = isset($row['loan_date']) ? date('d-m-Y', strtotime($row['loan_date'])) : '';
    $sub_array[] = date('d-m-Y', strtotime($row['due_end']));
    $sub_array[] = isset($row['centre_id']) ? $row['centre_id'] : '';
    $sub_array[] = isset($row['centre_no']) ? $row['centre_no'] : '';
    $sub_array[] = isset($row['centre_name']) ? $row['centre_name'] : '';
    $sub_array[] = isset($row['cus_id']) ? $row['cus_id'] : '';
    $sub_array[] = isset($row['first_name']) ? $row['first_name'] : '';
    $sub_array[] = isset($row['mobile1']) ? $row['mobile1'] : '';
    $sub_array[] = isset($row['areaname']) ? $row['areaname'] : '';
    $sub_array[] = moneyFormatIndia($loan_amount);
    $sub_array[] = moneyFormatIndia($due_amount);
    $sub_array[] = isset($issueDate['due_period']) ? $issueDate['due_period'] : '';
    $sub_array[] = isset($pending) ? ($pending < 0 ? 0 : $pending) : '';
    $sub_array[] = isset($balance_due_pending) ? $balance_due_pending : '';
    $sub_array[] = isset($payable) ? ($payable < 0 ? 0 : $payable) : '';
    $sub_array[] = isset($status ) ? $status  : '';

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
