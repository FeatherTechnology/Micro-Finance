<?php
include "../../ajaxconfig.php";

$loan_id = $_POST['loan_id'];

$grp_qry = $pdo->query("SELECT lelc.loan_id, lelc.centre_id, cc.centre_name, lelc.due_period, lelc.due_month, lelc.scheme_day_calc, lelc.scheme_date, lelc.due_start, lelc.due_end
FROM loan_entry_loan_calculation lelc
LEFT JOIN centre_creation cc ON cc.centre_id = lelc.centre_id
WHERE lelc.loan_id = '$loan_id'");
$grp = $grp_qry->fetch(PDO::FETCH_ASSOC);
$due_start_from = $grp['due_start'];
$maturity_month = $grp['due_end'];
$due_period = $grp['due_period'];

if ($grp['due_month'] == '1') {
    // If Due method is Monthly, Calculate penalty by checking if the month has ended or not

    // Create a DateTime object from the given date
    $maturity_month = new DateTime($maturity_month);
    $maturity_month = $maturity_month->format('Y-m-d');

    $due_start_from = date('Y-m-d', strtotime($due_start_from));
    $maturity_month = date('Y-m-d', strtotime($maturity_month));
    $current_date = date('Y-m-d');

    $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
    $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
    $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);
    $interval = new DateInterval('P1M'); // Create a one-month interval
    $i = 1;
    $dueMonth[] = $due_start_from;
    while ($start_date_obj < $end_date_obj) {
        $start_date_obj->add($interval);
        $dueMonth[] = $start_date_obj->format('Y-m-d');
    }
} else if ($grp['due_month'] == '2') {
    // If Due method is Weekly, Calculate penalty by checking if the week has ended or not
    $current_date = date('Y-m-d');

    // Create a DateTime object from the given date
    $maturity_month = new DateTime($maturity_month);
    $maturity_month = $maturity_month->format('Y-m-d');

    $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
    $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
    $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

    $interval = new DateInterval('P1W'); // Create a one-week interval

    $i = 1;
    $dueMonth[] = $due_start_from;
    while ($start_date_obj < $end_date_obj) {
        $start_date_obj->add($interval);
        $dueMonth[] = $start_date_obj->format('Y-m-d');
    }

   
}
$qry = $pdo->query("SELECT lcm.id as cus_mapping_id, lelc.loan_id, lelc.centre_id, cuc.cus_id, cuc.first_name,lelc.due_amount_calc,lelc.due_month,lelc.total_customer,lcm.issue_status,lelc.due_start
FROM loan_cus_mapping lcm
LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
LEFT JOIN customer_creation cuc ON lcm.cus_id = cuc.id
WHERE lcm.loan_id = '$loan_id'");
$customer_details = $qry->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .th_cls {
        text-align: end;
    }

    .th_bg>th {
        background-color: #381f42 !important;
    }

    .th_bg1>th {
        background-color: #381f42 !important;
    }

    .th_bg2>th {
        background-color: #381f42 !important;
    }

    .th_bg3>th {
        background-color: #381f42 !important;
    }
</style>

<table id="ledger_view_chart_table" class="table custom-table">
    <thead>
        <?php
        if ($grp['due_month'] == 1) {
            // For Monthly due method
            $due_date = $grp['due_start'];
            $scheme_day = $grp['scheme_date'];

            $year = date('Y', strtotime($due_date));
            $month = date('m', strtotime($due_date));

            $combined_date = date('d-m-Y', strtotime($scheme_day . '-' . $month . '-' . $year));
        ?>
            <tr class="th_bg">
                <th colspan="<?php echo intval($grp['due_period']) + 4; ?>" style="font-size: 22px; text-align: center;">
                    <?php echo "Loan ID: " . $grp['loan_id'] . " | Centre ID: " . $grp['centre_id'] . " | Centre Name: " . $grp['centre_name'] . " | Due Method: Monthly | Date: " . $combined_date; ?>
                </th>
                <th></th>
            </tr>
            <tr class="th_bg1">
                <th colspan="4" class="th_cls"></th>
                <?php
                for ($i = 1; $i <= $due_period; $i++) {
                    echo "<th>{$i}</th>";
                }
                ?>
                <th></th>
            </tr>
            <tr class="th_bg2">
                <th colspan="4" class="th_cls"></th>
                <?php
                foreach ($dueMonth as $date) {
                    echo "<th>" . date('M-Y', strtotime($date)) . "</th>";
                }
                ?>
                <th></th>
            </tr>
            <tr class="th_bg1">
                <th class="th_cls">Sl.No</th>
                <th>Customer ID</th>
                <th>Customer Name</th>
                <th>Due Amount</th>
                <?php
                foreach ($dueMonth as $date) {
                    echo "<th>" . date('d-m-Y', strtotime($date)) . "</th>";
                }
                ?>
                <th>Chart</th>
            </tr>
        <?php
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
            $scheme_day = $grp['scheme_day_calc'];
        ?>
            <tr class="th_bg">
                <th colspan="<?php echo intval($grp['due_period']) + 4; ?>" style="font-size: 22px; text-align: center;">
                    <?php
                    echo "Loan ID: " . $grp['loan_id'] . " | Centre ID: " . $grp['centre_id'] . " | Centre Name: " . $grp['centre_name'] . " | Due Method: Weekly | Day: ";
                    if (isset($daysOfWeek[$scheme_day])) {
                        echo $daysOfWeek[$scheme_day];
                    }
                    ?>
                </th>
                <th></th>
            </tr>
            <tr class="th_bg1">
                <th colspan="4" class="th_cls"></th>
                <?php
                for ($i = 1; $i <= $due_period; $i++) {
                    echo "<th>{$i}</th>";
                }
                ?>
                <th></th>
            </tr>
            <tr class="th_bg2">
                <th colspan="4" class="th_cls"></th>
                <?php
                foreach ($dueMonth as $start_date) {
                    echo "<th>" . date('M-Y', strtotime($start_date)) . "</th>";
                }
                ?>
                <th></th>
            </tr>
            <tr class="th_bg2">
                <th class="th_cls">Sl.No</th>
                <th>Customer ID</th>
                <th>Customer Name</th>
                <th>Due Amount</th>
                <?php
                foreach ($dueMonth as $start_date) {
                    echo "<th>" . date('d-m-Y', strtotime($start_date)) . "</th>";
                }
                ?>
                <th>Chart</th>
            </tr>
        <?php } ?>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($customer_details as $customer) {
        ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $customer['cus_id']; ?></td>
                <td><?php echo $customer['first_name']; ?></td>
                <td>
                    <?php
                    $individual_amount = floor($customer['due_amount_calc'] / $customer['total_customer']);
                    echo $individual_amount;
                    ?>
                </td>
                <?php
                if ($grp['due_month'] == '1') {
                    foreach ($dueMonth as $date) {
                        if ($customer['due_start'] == $date) {
                            // Query for coll_date before or equal to the specified date
                            $qry = $pdo->query("
                                SELECT SUM(due_amt_track) AS coll_amnt 
                                FROM collection 
                                WHERE loan_id = '$loan_id'
                                AND cus_mapping_id = '" . $customer['cus_mapping_id'] . "'
                                AND (
                                    (YEAR(coll_date) < YEAR('$date')) 
                                    OR (YEAR(coll_date) = YEAR('$date') AND MONTH(coll_date) <= MONTH('$date'))
                                )
                            ");
                        } else {
                            // Query for coll_date in the exact month and year of the specified date
                            $qry = $pdo->query("
                                SELECT SUM(due_amt_track) AS coll_amnt 
                                FROM collection 
                                WHERE loan_id = '$loan_id'
                                AND cus_mapping_id = '" . $customer['cus_mapping_id'] . "'
                                AND MONTH(coll_date) = MONTH('$date') 
                                AND YEAR(coll_date) = YEAR('$date')
                            ");
                        }

                        // Fetch and display the result
                        $row = $qry->fetch();
                        echo "<td>" . ($row['coll_amnt'] ? $row['coll_amnt'] : '') . "</td>";
                    }
                } else {
                    foreach ($dueMonth as $start_date) {
                        if ($customer['due_start'] == $start_date) {
                            // Query for records in the same year and week <= the current week of $start_date
                        
                            $qry = $pdo->query("
                                SELECT SUM(due_amt_track) AS coll_amnt 
                                FROM collection 
                                WHERE loan_id = '$loan_id' 
                                AND cus_mapping_id = '" . $customer['cus_mapping_id'] . "' 
                               AND (
                                    (YEAR(coll_date) < YEAR('$start_date')) 
                                    OR (YEAR(coll_date) = YEAR('$start_date') AND WEEK(coll_date) <= WEEK('$start_date'))
                                )
                            ");
                        } else {
                            // Query for records exactly in the same week and year as $start_date
                            $qry = $pdo->query("
                                SELECT SUM(due_amt_track) AS coll_amnt 
                                FROM collection 
                                WHERE loan_id = '$loan_id' 
                                AND cus_mapping_id = '" . $customer['cus_mapping_id'] . "' 
                                AND WEEK(coll_date) = WEEK('$start_date') 
                                AND YEAR(coll_date) = YEAR('$start_date')
                            ");
                        }

                        // Fetch and display the result
                        $row = $qry->fetch();
                        echo "<td>" . ($row['coll_amnt'] ? $row['coll_amnt'] : '') . "</td>";
                    }
                }
                ?>
                <td>
                    <input type="button"
                        class="btn btn-primary due-chart"
                        value="Due Chart"
                        data-id='<?php echo $customer['cus_mapping_id']; ?>'
                        <?php echo ($customer['issue_status'] != 1) ? 'disabled' : ''; ?>>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>