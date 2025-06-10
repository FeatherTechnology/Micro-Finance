<?php
include '../../ajaxconfig.php';
include '../common_files/get_customer_status.php';
$obj = new CustomerStatus($pdo);
@session_start();
$user_id = $_SESSION['user_id'];

$search_date = $_POST['search_date'];
$due_type = $_POST['due_type'];
$week_days = $_POST['week_days'];

$column = array(
   'lelc.loan_id',
    'lelc.scheme_date',
    'lelc.loan_id',
    'lelc.loan_date',
    'lelc.due_end',
    'cntr.centre_id',
    'cntr.centre_no',
    'cntr.centre_name',
    'cc.cus_id',
    'cc.first_name',
    'cc.mobile1',
    'anc.areaname',
    'lelc.loan_amount_calc',
    'lcm.due_amount',
    'lelc.due_period',
    'lelc.loan_id',
    'lelc.loan_id',
    'lelc.loan_id',
    'lelc.loan_id'
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
    lelc.loan_amount_calc,
    lcm.id AS cus_mapping_id,
    lcm.due_amount,
    cntr.centre_id,
    cntr.centre_no,
    cntr.centre_name,
    cc.mobile1,
    cc.first_name,
    cc.cus_id,
    anc.areaname,
    col.total_paid,
    col.total_paid_track
FROM loan_entry_loan_calculation lelc
JOIN centre_creation cntr ON cntr.centre_id = lelc.centre_id
JOIN loan_cus_mapping lcm ON lcm.loan_id = lelc.loan_id
JOIN customer_status cs ON cs.cus_map_id = lcm.id
JOIN customer_creation cc ON cc.id = lcm.cus_id
JOIN area_name_creation anc ON cntr.area = anc.id
LEFT JOIN closed_loan cl ON cl.centre_id = cntr.centre_id
JOIN loan_issue li ON li.loan_id = lelc.loan_id
LEFT JOIN (
    SELECT 
        loan_id, cus_mapping_id,
        SUM(due_amt_track) AS total_paid,
        SUM(total_paid_track) AS total_paid_track
    FROM collection
    WHERE DATE(coll_date) <= '$search_date'
    GROUP BY loan_id, cus_mapping_id
) col ON col.loan_id = lelc.loan_id AND col.cus_mapping_id = lcm.id
WHERE li.issue_date <= '$search_date'
AND lelc.due_month='$due_type' AND  (cs.balance_amount != 0 OR cl.closed_date > '$search_date') ";

if (isset($_POST['week_days']) && $_POST['week_days'] !='') {
    $query .= " and lelc.scheme_day_calc='$week_days' GROUP by lcm.id";
} else {
    $query .= " GROUP by lcm.id";
}
if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $query .= " and (anc.areaname LIKE '%" . $_POST['search'] . "%' OR
            lelc.loan_id LIKE '%" . $_POST['search'] . "%' OR
            lelc.loan_date LIKE '%" . $_POST['search'] . "%' OR
            cntr.centre_id LIKE '%" . $_POST['search'] . "%' OR
            cntr.centre_no LIKE '%" . $_POST['search'] . "%' OR
            cntr.centre_name LIKE '%" . $_POST['search'] . "%' OR
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
    $loan_id = $row['loan_id'];
    $centre = $row['centre_id'];
    $cus_mapping_id = $row['cus_mapping_id'];
    $due_period = $row['due_period'];
    $due_amount = round(floatval($row['due_amount']));
    $loan_amount = $due_amount * $due_period;

    // Step 1: Get due_start a
    $due_start_date = $row['due_start'];
    $totalPaidAmt = 0;

    // Initialize these to avoid undefined variable warnings
    $month_diff = 0;
    $estimated_pending = 0;

    if ($due_start_date && $due_amount > 0) {
        $start = new DateTime($due_start_date);
        $end = new DateTime($search_date);
        $diff = $start->diff($end);
        $totalPaidAmt = floatval($row['total_paid']);
        $totalPaidTrack = floatval($row['total_paid_track']);

        $month_diff = 0;
        $estimated_pending = 0;

        if ($due_type == '1') { // Monthly
        if (strtotime($row['due_end']) < strtotime($search_date)) {
            $end = strtotime($row['due_end']);
            $start = strtotime($row['due_start']);
            $payable_diff = (date('Y', $end) - date('Y', $start)) * 12 + (date('m', $end) - date('m', $start)) + 1;

            $pending_diff = $payable_diff;
                
        
        } else {
                $start = strtotime($row['due_start']);
                $end = strtotime($search_date);
                $month_diff = max(0, date('m', $end) - date('m', $start));
                $payable_diff = (date('Y', $end) - date('Y', $start)) * 12 + $month_diff  + 1;
                $pending_diff =  max(0, (date('Y', $end) - date('Y', $start)) * 12 + (date('m', $end) - date('m', $start)));

        }
        } elseif ($due_type == '2') { // Weekly
           if (strtotime($row['due_end']) < strtotime($search_date)) {
        // Maturity date has passed
                $start = strtotime($row['due_start']);
                $end = strtotime($row['due_end']);

                $days = ($end - $start) / (60 * 60 * 24); // Convert seconds to days
                $payable_diff = intdiv($days, 7) + (($days % 7) >= 0 ? 1 : 0); // Include partial weeks

                $pending_week = $payable_diff;
            } else {
        // Not yet matured; count till current date (or $to_date)
                $start = strtotime($row['due_start']);
                $end = strtotime(datetime: $search_date);

                $days = ($end - $start) / (60 * 60 * 24); // Convert seconds to days
                $payable_diff = intdiv($days, 7) + (($days % 7) >= 0 ? 1 : 0); // Include partial weeks

                $pending_week = max(($payable_diff - 1), 0);
            }
        }

        // Step 4: Final pending
        $pending = max((($pending_diff * $due_amount) - $totalPaidAmt), 0);

        // Step 5: Pending due count
        $pending_due_count = ($due_amount > 0) ? number_format($pending / $due_amount, 2) : 0;

        // Step 6: Payable amount
        $payable_amount = max(($payable_diff * $due_amount) - $totalPaidTrack, 0);
    }

    $customer_status = $obj->custStatus($cus_mapping_id, $loan_id, $search_date);
    $status = $customer_status['status'];

    $sub_array[] = $sno;
    $sub_array[] = isset($date_day) ?  $date_day : '';
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
    $sub_array[] = isset($row['due_period']) ? $row['due_period'] : '';
    $sub_array[] = moneyFormatIndia(isset($pending) ? ($pending < 0 ? 0 : $pending) : '');
    $sub_array[] = isset($pending_due_count) ? $pending_due_count : '';
    $sub_array[] = moneyFormatIndia(isset($payable_amount) ? ($payable_amount < 0 ? 0 : $payable_amount) : '');
    $sub_array[] = isset($status) ? $status  : '';
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
