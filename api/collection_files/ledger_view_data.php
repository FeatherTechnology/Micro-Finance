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

if ($grp['due_month'] == '1') {
    // If Due method is Monthly, Calculate penalty by checking the month has ended or not

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
    // If Due method is Weekly, Calculate penalty by checking the week has ended or not
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
print_r($dueMonth);
$qry = $pdo->query("SELECT lelc.loan_id, lelc.centre_id, cuc.cus_id, cuc.first_name
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

    .th_bg > th {
        background-color: #381f42 !important;
    }

    .th_bg1 > th {
        background-color: #381f42 !important;
    }

    .th_bg2 > th {
        background-color: #381f42 !important;
    }

    .th_bg3 > th {
        background-color: #381f42 !important;
    }
</style>

<table id="ledger_view_chart_table" class="table custom-table">
    <thead>
        <?php
        // Handling different due months and calculations
        if ($grp['due_month'] == 1) {
            // For Monthly due method
            $due_date = $grp['due_start']; // Actual due date
            $scheme_day = $grp['scheme_date']; // Example: 4 (day of the month)

            // Extract the year and month from the due_date
            $year = date('Y', strtotime($due_date));  // Extracts '2024' from due_date
            $month = date('m', strtotime($due_date)); // Extracts '12' (December) from due_date

            // Combine the scheme_day (day) with the extracted month and year
            $combined_date = date('m-Y', strtotime($scheme_day . '-' . $month . '-' . $year));
        ?>
            <tr class="th_bg">
                <th colspan="<?php echo intval($grp['due_period']) + 5; ?>" style="font-size: 22px; text-align: center;">
                    <?php echo "Loan ID: " . $grp['loan_id'] . " | Centre ID: " . $grp['centre_id'] . " | Centre Name: " . $grp['centre_name'] . " | Due Method: Monthly | Date: " . $combined_date; ?>
                </th>
                <th></th>
            </tr>
            <tr class="th_bg1">
                <th class="th_cls">Sl.No</th>
                <th>Customer ID</th>
                <th>Customer Name</th>
                <th>Due Amount</th>
                <?php
                // Displaying the month headers for monthly due method
                foreach ($dueMonth as $date) {
                    echo "<th>" . date('M-Y', strtotime($date)) . "</th>";
                }
                ?>
                <th>Chart</th>
            </tr>
            <tr class="th_bg3">
                <?php 
                     foreach ($dueMonth as $date) {
                         echo "<th colspan='5'>" . date('d-M-Y', strtotime($date)) . "</th>";
                    }
                ?>
                <th></th>
            </tr>
        <?php
        } else {
            // For Weekly due method
            $daysOfWeek = [
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday',
                7 => 'Sunday'
            ];
            $scheme_day = $grp['scheme_day_calc']; // Assuming this is the day of the week (1-7)
        ?>
            <tr class="th_bg">
                <th colspan="<?php echo intval($grp['due_period']) + 5; ?>" style="font-size: 22px; text-align: center;">
                    <?php
                    echo "Loan ID: " . $grp['loan_id'] . " | Centre ID: " . $grp['centre_id'] . " | Centre Name: " . $grp['centre_name'] . " | Due Method: Weekly | Day: ";
                    if (isset($daysOfWeek[$scheme_day])) {
                        echo $daysOfWeek[$scheme_day]; // Display the day of the week
                    }
                    ?>
                </th>
                <th></th>
            </tr>
            <tr class="th_bg2">
                <th class="th_cls">Sl.No</th>
                <th>Customer ID</th>
                <th>Customer Name</th>
                <th>Due Amount</th>
                <?php
                // Displaying the dates for weekly due method
                $weekDates = [];
                foreach ($dueMonth as $start_date) {
                    echo "<th>" . date('M-Y', strtotime($start_date)) . "</th>";
                }
                ?>
                <th>Chart</th>
            </tr>
        <?php } ?>
    </thead>
</table>
