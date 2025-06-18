<?php
include '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$to_date = $_POST['to_date'];
$current_time = '23:59:59';
$dateTime = $to_date . ' ' . $current_time;

$query = "SELECT
    lelc.loan_id,
    lelc.loan_date,
    cntr.centre_id,
    cntr.centre_no,
    cntr.centre_name,
    lelc.due_end,
    bc.branch_name,
    cc.mobile1,
    lc.loan_category,
    lelc.loan_amount,
    lelc.due_month,
    lelc.due_start,
    lelc.scheme_date,
    lelc.scheme_day_calc,
    u.name,
    c.loan_due_amnt,
    c.coll_date,
    lelc.due_period,
    SUM(c.due_amt_track)AS  due_amt_track ,
    lelc.principal_amount_calc,
    lelc.intrest_amount_calc,
    lelc.total_amount_calc,
    c.status,
    c.sub_status
FROM
    loan_entry_loan_calculation lelc
JOIN collection c ON
    c.loan_id = lelc.loan_id
JOIN loan_cus_mapping lcm ON
    lcm.id = c.cus_mapping_id
JOIN centre_creation cntr ON
    lelc.centre_id = cntr.centre_id
JOIN branch_creation bc ON
    cntr.branch = bc.id
JOIN customer_creation cc ON
    cc.id = lcm.cus_id
JOIN loan_category lc ON
    lc.id = lelc.loan_category
left join customer_status cs on 
    cs.cus_map_id = lcm.id
left join closed_loan cl on 
    cl.loan_id = lelc.loan_id
JOIN users u ON
    u.id = c.insert_login_id
    WHERE
 c.coll_date <= '$dateTime' AND  (cs.balance_amount != 0 OR cl.closed_date > '$dateTime')
GROUP BY
    lelc.loan_id
";

if (isset($_POST['search']) && $_POST['search'] != "") {
    $search = $_POST['search'];
    $query .= " AND (
        lelc.loan_id LIKE '%$search%' OR
        lelc.loan_date LIKE '%$search%' OR
        cntr.centre_id LIKE '%$search%' OR
        cntr.centre_name LIKE '%$search%' OR
        u.name LIKE '%$search%' OR
        lelc.due_end LIKE '%$search%' OR
        lelc.due_month LIKE '%$search%' OR
        c.coll_date LIKE '%$search%' OR
        bc.branch_name LIKE '%$search%' OR
        cc.mobile1 LIKE '%$search%' OR
        lc.loan_category LIKE '%$search%'
    )";
}

$statement = $pdo->prepare($query);
$statement->execute();
$number_filter_row = $statement->rowCount();

$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? -1;
if ($length != -1) {
    $query .= " LIMIT $start, $length";
}

$statement = $pdo->prepare($query);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

$data = [];
$sno = 1;

foreach ($result as $row) {
    $sub_array = [];
    $balance_amt = intVal($row['total_amount_calc']) - intVal($row['due_amt_track']);

    $princ_amt = intVal($row['principal_amount_calc']) / $row['due_period'];
    $int_amt = intVal($row['intrest_amount_calc']) / $row['due_period'];
    $response = calculatePrincipalAndInterest($princ_amt, $int_amt, $balance_amt);

    if (intVal($response['principal_paid']) > intVal($row['loan_amount'])) {
        $diff = intVal($response['principal_paid']) - intVal($row['loan_amount']);
        $response['interest_paid'] += $diff;
        $response['principal_paid'] = intVal($row['loan_amount']);
    }

    $bal_due = round($balance_amt / $row['loan_due_amnt'], 1);

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



    $sub_array[] = $sno;
    $sub_array[] = isset($date_day) ?  $date_day : '';
    $sub_array[] = isset($row['loan_id']) ? $row['loan_id'] : '';
    $sub_array[] = isset($row['loan_date']) ? date('d-m-Y', strtotime($row['loan_date'])) : '';
    $sub_array[] = isset($row['due_end']) ? date('d-m-Y', strtotime($row['due_end'])) : '';
    $sub_array[] = isset($row['centre_id']) ? $row['centre_id'] : '';
    $sub_array[] = isset($row['centre_no']) ? $row['centre_no'] : '';
    $sub_array[] = isset($row['centre_name']) ? $row['centre_name'] : '';
    $sub_array[] = isset($row['branch_name']) ? $row['branch_name'] : '';
    $sub_array[] = isset($row['mobile1']) ? $row['mobile1'] : '';
    $sub_array[] = isset($row['loan_category']) ? $row['loan_category'] : '';
    $sub_array[] = isset($row['name']) ? $row['name'] : '';
    $sub_array[] = moneyFormatIndia($row['loan_amount']);
    $sub_array[] = moneyFormatIndia($row['loan_due_amnt']);
    $sub_array[] = isset($row['due_period']) ? $row['due_period'] : '';
    $sub_array[] = moneyFormatIndia($row['total_amount_calc']);
    $sub_array[] = moneyFormatIndia($balance_amt);
    $sub_array[] = moneyFormatIndia($response['principal_paid']);
    $sub_array[] = moneyFormatIndia($response['interest_paid']);
    $sub_array[] = $bal_due;
    $sub_array[] = isset($row['status']) ? $row['status'] : '';
    $sub_array[] = isset($row['sub_status']) ? $row['sub_status'] : '';
    $data[] = $sub_array;
    $sno++;
}

function count_all_data($pdo)
{
    $query = "SELECT id FROM collection group by loan_id";
    $statement = $pdo->prepare($query);
    $statement->execute();
    return $statement->rowCount();
}

$output = [
    'draw' => intval($_POST['draw']),
    'recordsTotal' => count_all_data($pdo),
    'recordsFiltered' => $number_filter_row,
    'data' => $data,
];

$pdo = null; //Close Connection
echo json_encode($output);

function moneyFormatIndia($num)
{
    $explrestunits = "";
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3);
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

function calculatePrincipalAndInterest($principal, $interest, $paidAmount)
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
        'principal_paid' => (int)$principal_paid,
        'interest_paid' => (int)$interest_paid,
    ];
}
