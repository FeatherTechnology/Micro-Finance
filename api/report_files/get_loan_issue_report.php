<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$from_date = $_POST['params']['from_date'];
$to_date = $_POST['params']['to_date'];

$column = array(
    'lelc.loan_id',
    'anc.areaname',
    'bc.branch_name',
    'cc.mobile1',
    'lc.loan_category',
    'li.issue_date',
    'lelc.loan_amount_calc',
    'lelc.principal_amount_calc',
    'lelc.interest_amount_calc',
    'lelc.document_charge_calc',
    'lelc.processing_fees_calc',
    'lelc.total_amount_calc',
    'lelc.net_cash_calc'   
);

$query = "SELECT lcm.id, anc.areaname, bc.branch_name , cc.mobile1, lc.loan_category, li.issue_date,lelc.loan_id, lelc.loan_amount, lelc.principal_amount_calc , lelc.intrest_amount_calc, lelc.document_charge_cal, lelc.processing_fees_cal,lelc.loan_date,lelc.due_month,lelc.due_start,lelc.scheme_date,lelc.scheme_day_calc, lelc.total_amount_calc, lelc.net_cash_calc,cnt.centre_id,cnt.centre_no,cnt.centre_name FROM loan_issue li JOIN loan_cus_mapping lcm ON li.loan_id = lcm.loan_id JOIN customer_creation cc ON lcm.cus_id = cc.id JOIN loan_entry_loan_calculation lelc ON li.loan_id = lelc.loan_id JOIN area_name_creation anc ON cc.area = anc.id JOIN loan_category lc ON lc.id = lelc.loan_category JOIN centre_creation cnt ON cnt.centre_id = lelc.centre_id JOIN branch_creation bc ON cnt.branch = bc.id  WHERE li.issue_date BETWEEN '$from_date' AND '$to_date ' group by lelc.loan_id ";
if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $search = $_POST['search'];
        $query .=     " AND (lelc.loan_id LIKE '%" . $search . "%'
        OR lelc.loan_id LIKE '%" . $search . "%'
        OR anc.areaname LIKE '%" . $search . "%'
        OR bc.branch_name LIKE '%" . $search . "%'
        OR cc.mobile1 LIKE '%" . $search . "%' 
        OR lc.loan_category LIKE '%" . $search . "%' 
        OR li.issue_date LIKE '%" . $search . "%' 
        OR lelc.loan_amount_calc LIKE '%" . $search . "%' 
        OR lelc.principal_amount_calc LIKE '%" . $search . "%' 
        OR lelc.intrest_amount_calc LIKE '%" . $search . "%' 
        OR lelc.document_charge_cal LIKE '%" . $search . "%' 
        OR lelc.processing_fees_cal LIKE '%" . $search . "%' 
        OR lelc.total_amount_calc LIKE '%" . $search . "%' 
        OR lelc.net_cash_calc LIKE '%" . $search . "%' 
         )";
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
    if ($row['due_month'] == 1) {
        // For Monthly due method
        $due_date = $row['due_start'];
        $scheme_day = $row['scheme_date'];

        $year = date('Y', strtotime($due_date));
        $month = date('m', strtotime($due_date));
        $date_day = date('d-m-Y', strtotime($scheme_day . '-' . $month . '-' . $year));
    }
     else {
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
        $date_day= $daysOfWeek[$scheme_day];
    }

    $sub_array = array();

    $sub_array[] = $sno++;
    $sub_array[] = isset($row['loan_id']) ? $row['loan_id'] : '';
    $sub_array[] = isset($row['centre_id']) ? $row['centre_id'] : '';
    $sub_array[] = isset($row['centre_no']) ? $row['centre_no'] : '';
    $sub_array[] = isset($row['centre_name']) ? $row['centre_name'] : '';
    $sub_array[] = isset( $date_day) ?  $date_day : '';
    $sub_array[] = isset($row['branch_name']) ? $row['branch_name'] : '';
    $sub_array[] = isset($row['mobile1']) ? $row['mobile1'] : '';
    $sub_array[] = isset($row['loan_category']) ? $row['loan_category'] : '';
    $sub_array[] = isset($row['loan_date']) ? date('d-m-Y', strtotime($row['loan_date'])) : '';
    $sub_array[] = isset($row['loan_amount']) ? moneyFormatIndia($row['loan_amount']) : '';
    $sub_array[] = isset($row['principal_amount_calc']) ? moneyFormatIndia($row['principal_amount_calc']) : '';
    $sub_array[] = isset($row['intrest_amount_calc']) ? moneyFormatIndia($row['intrest_amount_calc']) : '';
    $sub_array[] = isset($row['document_charge_cal']) ? moneyFormatIndia($row['document_charge_cal']) : '';
    $sub_array[] = isset($row['processing_fees_cal']) ? moneyFormatIndia($row['processing_fees_cal']) : '';
    $sub_array[] = isset($row['total_amount_calc']) ? moneyFormatIndia($row['total_amount_calc']) : '';
    $sub_array[] = isset($row['net_cash_calc']) ? moneyFormatIndia($row['net_cash_calc']) : '';

    $data[] = $sub_array;
}
function count_all_data($pdo)
{
    $query = "SELECT COUNT(*) FROM loan_issue";
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

function moneyFormatIndia($num)
{
    $isNegative = false;
    if ($num < 0) {
        $isNegative = true;
        $num = abs($num);
    }

    $explrestunits = "";
    if (strlen((string)$num) > 3) {
        $lastthree = substr((string)$num, -3);
        $restunits = substr((string)$num, 0, -3);
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
        $expunit = str_split($restunits, 2);
        foreach ($expunit as $index => $value) {
            if ($index == 0) {
                $explrestunits .= (int)$value . ",";
            } else {
                $explrestunits .= $value . ",";
            }
        }
        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $num;
    }

    $thecash = $isNegative ? "-" . $thecash : $thecash;
    $thecash = $thecash == 0 ? "" : $thecash;
    return $thecash;
}
