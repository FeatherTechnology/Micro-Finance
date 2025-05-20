<?php
include '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];

$column = array(
    'c.id',
    'lelc.loan_id',
    'lelc.loan_date',
    'cntr.centre_id ',
    'cntr.centre_no' , 
    'cntr.centre_name',
    'bc.branch_name',
    'cc.mobile1',
    'lc.loan_category',
    'r.role',
    'u.name',
    'c.coll_date',
    'c.id',
    'c.id',
    'c.id',
    'c.id',
    'c.id',
    'c.id',
    'c.id'
);

$query = "SELECT c.id, lelc.loan_id , lelc.loan_date ,lelc.due_month, lelc.due_start, lelc.scheme_date, lelc.scheme_day_calc, lelc.principal_amount_calc, lelc.intrest_amount_calc, cntr.centre_id , cntr.centre_no , cntr.centre_name , bc.branch_name , cc.mobile1 , lc.loan_category , u.name , r.role ,c.status,  c.sub_status ,c.coll_date, SUM(c.due_amt_track)
 AS  due_amt_track ,lelc.due_period, lelc.due_amount_calc ,SUM(c.total_paid_track) AS total_paid_track ,SUM(fine_charge_track) as fine_charge FROM collection c JOIN loan_entry_loan_calculation lelc ON c.loan_id = lelc.loan_id JOIN centre_creation cntr ON cntr.centre_id = lelc.centre_id JOIN loan_cus_mapping lcm ON
    c.cus_mapping_id = lcm.id JOIN customer_creation cc ON cc.id = lcm.cus_id JOIN branch_creation bc ON cntr.branch = bc.id JOIN loan_category lc ON lelc.loan_category = lc.id  JOIN users u ON u.id = c.insert_login_id LEFT JOIN role r ON u.role = r.id WHERE u.id ='$user_id'  AND DATE(c.coll_date) BETWEEN '$from_date' AND '$to_date'GROUP by lelc.loan_id";

if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $query .= " and (lelc.loan_id LIKE '%" . $_POST['search'] . "%'
                    OR bc.branch_name LIKE '%" . $_POST['search'] . "%'
                    OR cc.mobile1 LIKE '%" . $_POST['search'] . "%'
                    OR cntr.centre_id LIKE '%" . $_POST['search'] . "%'
                    OR cntr.centre_name LIKE '%" . $_POST['search'] . "%'
                    OR r.role LIKE '%" . $_POST['search'] . "%'
                    OR u.name LIKE '%" . $_POST['search'] . "%'
                    OR c.coll_date LIKE '%" . $_POST['search'] . "%') ";
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

        $date_day = date('d-m-Y', strtotime($scheme_day . '-' . $month . '-' . $year));
    }
    else{
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
     $response = calculatePrincipalAndInterest(intVal($row['principal_amount_calc']) / $row['due_period'], intVal($row['intrest_amount_calc']) / $row['due_period'],intVal($row['due_amt_track']));
        $principal = moneyFormatIndia(intVal($response['principal_paid']));
        $rounderd_int = intVal($row['due_amt_track']) - $response['principal_paid'];
        $intrest = moneyFormatIndia(intVal($rounderd_int));

    $sub_array[] = $sno;
    $sub_array[] =isset( $date_day) ?  $date_day : '';
    $sub_array[] = isset($row['loan_id']) ? $row['loan_id'] : '';
    $sub_array[] = isset($row['loan_date']) ? date('d-m-Y', strtotime($row['loan_date'])) : '';
    $sub_array[] = isset($row['centre_id']) ? $row['centre_id'] : '';
    $sub_array[] = isset($row['centre_no']) ? $row['centre_no'] : '';
    $sub_array[] = isset($row['centre_name']) ? $row['centre_name'] : '';
    $sub_array[] = isset($row['branch_name']) ? $row['branch_name'] : '';
    $sub_array[] = isset($row['mobile1']) ? $row['mobile1'] : '';
    $sub_array[] = isset($row['loan_category']) ? $row['loan_category'] : '';
    $sub_array[] = isset($row['role']) ? $row['role'] : '';
    $sub_array[] = isset($row['name']) ? $row['name'] : '';
    $sub_array[] = isset($row['coll_date']) ? date('d-m-Y', strtotime($row['coll_date'])) : '';
    $sub_array[] = moneyFormatIndia(intval($row['due_amt_track']));
    $sub_array[] = $principal;
    $sub_array[] = $intrest;
    $sub_array[] = moneyFormatIndia(intval($row['fine_charge']));
    $sub_array[] = moneyFormatIndia(intval($row['total_paid_track']));
    $sub_array[] = isset($row['status']) ? $row['status'] : '';
    $sub_array[] = isset($row['sub_status']) ? $row['sub_status'] : '';
    $data[]      = $sub_array;
    $sno = $sno + 1;
}
function count_all_data($pdo)
{
    $query = "SELECT id FROM collection c  GROUP BY c.loan_id ";
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
function calculatePrincipalAndInterest($principal,  $interest,  $paidAmount): array
{
    $principal_paid = 0;
    $interest_paid = 0;

    while ($paidAmount > 0) {
        if ($paidAmount >= $principal) {
            $principal_paid += $principal;
            $paidAmount -= $principal;
        } else {
            $principal_paid += $paidAmount;
            break;
        }

        if ($paidAmount >= $interest) {
            $interest_paid += $interest;
            $paidAmount -= $interest;
        } else {
            $interest_paid += $paidAmount;
            break;
        }
    }

    return [
        'principal_paid' => (int) $principal_paid,
        'interest_paid' => (int) $interest_paid
    ];
}
