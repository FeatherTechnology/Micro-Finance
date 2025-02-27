<?php
require '../../ajaxconfig.php';


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
?>
<table class="table custom-table" id='dueChartListTable'>


    <?php
    $cus_mapping_id = $_POST['cus_mapping_id'];
    $curDateChecker = true;
    if (isset($_POST['closed'])) {
        $closed = $_POST['closed'];
    } else {
        $closed = 'false';
    }
    $loanStart = $pdo->query("SELECT
              lcm.id,
              lcm.loan_id,
              lelc.due_amount_calc,
              lelc.total_customer,
              lelc.due_month,
              lelc.due_start,
              lelc.due_end,
              lelc.scheme_name,
              lelc.loan_category,
              lelc.total_amount_calc,
              lcm.issue_status,
             lcm.due_amount,
              lcm.loan_amount
          FROM loan_cus_mapping lcm
          JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
          WHERE lcm.id = '$cus_mapping_id' ");
    $loanFrom = $loanStart->fetch();
    //If Due method is Monthly, Calculate penalty by checking the month has ended or not
    $due_start_from = $loanFrom['due_start'];
    $maturity_month = $loanFrom['due_end'];


    if ($loanFrom['due_month'] == '1') {
        //If Due method is Monthly, Calculate penalty by checking the month has ended or not

        // Create a DateTime object from the given date
        $maturity_month = new DateTime($maturity_month);
        // Subtract one month from the date
        // $maturity_month->modify('-1 month');
        // Format the date as a string
        $maturity_month = $maturity_month->format('Y-m-d');

        $due_start_from = date('Y-m-d', strtotime($due_start_from));
        $maturity_month = date('Y-m-d', strtotime($maturity_month));
        $current_date = date('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);
        $interval = new DateInterval('P1M'); // Create a one month interval
        //$count = 0;
        $i = 1;
        $dueMonth[] = $due_start_from;
        while ($start_date_obj < $end_date_obj) {
            $start_date_obj->add($interval);
            $dueMonth[] = $start_date_obj->format('Y-m-d');
        }
    } else
        if ($loanFrom['due_month'] == '2') {
        //If Due method is Weekly, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d');

        // Create a DateTime object from the given date
        $maturity_month = new DateTime($maturity_month);
        // Subtract one month from the date
        // $maturity_month->modify('-7 days');
        // Format the date as a string
        $maturity_month = $maturity_month->format('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1W'); // Create a one Week interval

        //$count = 0;
        $i = 1;
        $dueMonth[] = $due_start_from;
        while ($start_date_obj < $end_date_obj) {
            $start_date_obj->add($interval);
            $dueMonth[] = $start_date_obj->format('Y-m-d');
        }
    }

    $issueDate = $pdo->query("SELECT lelc.due_amount_calc,lelc.due_period, lelc.intrest_amount_calc, lelc.total_amount_calc, lelc.principal_amount_calc,lelc.total_customer ,li.issue_date,lelc.due_month,lelc.scheme_day_calc,lelc.scheme_date,lcm.due_amount,lcm.loan_amount
    FROM loan_issue li 
    JOIN loan_cus_mapping lcm ON li.cus_mapping_id = lcm.id
      JOIN loan_entry_loan_calculation lelc ON li.loan_id = lelc.loan_id  
    WHERE li.cus_mapping_id = '$cus_mapping_id' and lcm.issue_status >= 1 ORDER BY lelc.id DESC LIMIT 1 ");

    $loanIssue = $issueDate->fetch();
    //If Due method is Monthly, Calculate penalty by checking the month has ended or not  
    $loan_amt = round($loanIssue['due_amount'] * $loanIssue['due_period']);
    $loan_type = 'emi';

    $scheme_day = $loanIssue['scheme_day_calc'];
    $scheme_date = $loanIssue['scheme_date'];
    $due_amt_1 = round($loanIssue['due_amount']);


    $issue_date = $loanIssue['issue_date'];
    $due_month = $loanIssue['due_month'];
    ?>

    <?php
    // Conditionally render the table header based on the due_month value
    if ($due_month == 1) {
        // Due month is 1, show this header design
    ?>
        <thead>
            <tr>
                <th width="15"> Due No </th>
                <th width="8%"> Month </th>
                <th> Date </th>
                <th> Due Amount </th>
                <th> Pending </th>
                <th> Payable </th>
                <th> Collection Date </th>
                <th> Collection Amount </th>
                <th> Balance Amount </th>
                <th> Action </th>
            </tr>
        </thead>
    <?php
    } elseif ($due_month == 2) {
        // Due month is 2, show this header design
    ?>
        <thead>
            <tr>
                <th width="15"> Due No </th>
                <th> Due Date </th>
                <th> Day </th>
                <th> Month </th>
                <th> Due Amount </th>
                <th> Pending </th>
                <th> Payable </th>
                <th> Collection Date </th>
                <th> Collection Amount </th>
                <th> Balance Amount </th>
                <th> Action </th>
            </tr>
        </thead>
    <?php
    }
    ?>
    <tbody>
        <tr>
            <?php if ($due_month == 1): ?>
                <!-- TDs for due_month 1 -->
                <td> </td>
                <td><?php echo date('M-Y', strtotime($issue_date)); // For Monthly. Show month and year 
                    ?></td>
                <td><?php echo $scheme_date; // Display scheme date 
                    ?></td>
                <td> <!-- Optionally, handle due amount, pending, and other fields --> </td>
                <td> <!-- Pending --> </td>
                <td> <!-- Payable --> </td>
                <td> <!-- Collection Date --> </td>
                <td> <!-- Collection Amount --> </td>
                <td><?php echo moneyFormatIndia($loan_amt); // balance Amount 
                    ?> </td>
                <td></td>

            <?php elseif ($due_month == 2): ?>
                <!-- TDs for due_month 2 -->
                <td> </td>
                <td><?php echo date('d-m-Y', strtotime($issue_date)); // For Weekly && Day. Show day, month, year 
                    ?></td>
                <td><?php
                    // For Weekly & Day, show corresponding weekday name
                    $daysOfWeek = [
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        7 => 'Sunday'
                    ];

                    if (isset($daysOfWeek[$scheme_day])) {
                        echo $daysOfWeek[$scheme_day]; // Display the day of the week
                    }
                    ?></td>
                <td><?php echo date('M', strtotime($issue_date)); // Show month for weekly/day scheme 
                    ?></td>
                <td> <!-- Optionally, handle due amount, pending, and other fields --> </td>
                <td> <!-- Pending --> </td>
                <td> <!-- Payable --> </td>
                <td> <!-- Collection Date --> </td>
                <td> <!-- Collection Amount --> </td>
                <td> <?php echo moneyFormatIndia($loan_amt); // Balance Amount 
                        ?> </td>
                <td></td>
            <?php endif; ?>
        </tr>
        <?php
        $issued = date('Y-m-d', strtotime($issue_date));
        if ($loanFrom['due_month'] == '1') {
            //Query for Monthly.
         
            $run = $pdo->query("SELECT c.id, c.due_amnt, c.pending_amt, c.payable_amt, c.coll_date, c.due_amt_track,c.fine_charge_track,lelc.due_start,lelc.due_end, lelc.due_month,lelc.scheme_day_calc,lelc.scheme_date
            FROM `collection` c
             LEFT JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id
            LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
            WHERE c.cus_mapping_id = '$cus_mapping_id' AND (c.due_amt_track != '') AND c.due_amt_track > 0
            AND(
                (
                    ( MONTH(c.coll_date) >= MONTH('$issued') AND YEAR(c.coll_date) = YEAR('$issued') )
                    AND 
                    ( 
                        (
                            YEAR(c.coll_date) = YEAR('$due_start_from') AND MONTH(c.coll_date) < MONTH('$due_start_from')
                        ) OR (
                            YEAR(c.coll_date) < YEAR('$due_start_from')
                        )
                    )
                ) 
            )");
        } else
        if ($loanFrom['due_month'] == '2') {
            //Query For Weekly. 
            $run = $pdo->query("SELECT c.id, c.due_amnt, c.pending_amt, c.payable_amt, c.coll_date, c.due_amt_track, c.fine_charge_track, lelc.due_start, lelc.due_end, lelc.due_month,lelc.scheme_day_calc,lelc.scheme_date
            FROM `collection` c
            LEFT JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id
            LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
            WHERE c.cus_mapping_id = '$cus_mapping_id' 
            AND c.due_amt_track IS NOT NULL AND c.due_amt_track != '' AND c.due_amt_track > 0
            AND c.coll_date BETWEEN ('$issued')
            AND ('$due_start_from') ");
        }

        //For showing data before due start date
        $due_amt_track = 0;
        $last_bal_amt = 0;
        $bal_amt = 0;
if ($run->rowCount() > 0) {
    while ($row = $run->fetch()) {
        $collectionAmnt = intVal($row['due_amt_track']);
        $due_amt_track = $due_amt_track + intVal($row['due_amt_track']);
        $bal_amt = $loan_amt - $due_amt_track;
?>

        <tr> <!-- Showing From loan date to due start date. if incase due paid before due start date it has to show separately in top row. -->
        <?php
        if ($loanFrom['due_month'] == '1') {
            ?>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><?php echo moneyFormatIndia(intval($row['pending_amt'])); ?></td>
            <td><?php echo moneyFormatIndia(intVal($row['payable_amt'])); ?></td>
            <td><?php echo date('d-m-Y', strtotime($row['coll_date'])); ?></td>
            <td>
                <?php if ($row['due_amt_track'] > 0) {
                    echo moneyFormatIndia($row['due_amt_track']);
                } ?>
            </td>
            <td><?php echo moneyFormatIndia($bal_amt); ?></td>
            <td> 
                <a class='print_due_coll' id="" value="<?php echo $row['id']; ?>"> 
                    <i class="fa fa-print" aria-hidden="true"></i> 
                </a> 
            </td>
            <?php
        } else {
            ?>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><?php echo moneyFormatIndia(intval($row['pending_amt'])); ?></td>
            <td><?php echo moneyFormatIndia(intVal($row['payable_amt'])); ?></td>
            <td><?php echo date('d-m-Y', strtotime($row['coll_date'])); ?></td>
            <td>
                <?php if ($row['due_amt_track'] > 0) {
                    echo moneyFormatIndia($row['due_amt_track']);
                } ?>
            </td>
            <td><?php echo moneyFormatIndia($bal_amt); ?></td>
            <td> 
                <a class='print_due_coll' id="" value="<?php echo $row['id']; ?>"> 
                    <i class="fa fa-print" aria-hidden="true"></i> 
                </a> 
            </td>
            </tr>
        <?php
        }
    }
}
        //For showing collection after due start date
        $due_amt_track = 0;
        $jj = 0;
        $last_int_amt = $due_amt_1;

        $initial_balance_query = $pdo->query("SELECT lcm.due_amount,lelc.due_period 
        FROM loan_cus_mapping lcm
        LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
        WHERE lcm.id = '$cus_mapping_id'");
    
    $initial_balance_result = $initial_balance_query->fetch();
    $initial_balance = floor($initial_balance_result['due_amount'] * $initial_balance_result['due_period']);

    $bal_amt = $bal_amt > 0 ? $bal_amt : $initial_balance;
        $lastCusdueMonth = '1970-00-00';
        foreach ($dueMonth as $cusDueMonth) {
            if ($loanFrom['due_month'] == '1') {
                //Query for Monthly.
                $run = $pdo->query("SELECT c.id, c.due_amnt, c.pending_amt, c.payable_amt, c.coll_date, c.due_amt_track, c.fine_charge_track, lelc.due_start, lelc.due_end, lelc.total_amount_calc,lelc.total_customer,lelc.due_month,lelc.scheme_day_calc,lelc.scheme_date,lcm.due_amount,lelc.due_period
                FROM `collection` c
                LEFT JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id
                LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
                WHERE c.cus_mapping_id = '$cus_mapping_id'
                AND c.due_amt_track != '' AND c.due_amt_track > 0
                AND (
                    MONTH(c.coll_date) = MONTH('$cusDueMonth') 
                    AND YEAR(c.coll_date) = YEAR('$cusDueMonth')
                ) ");
            } elseif ($loanFrom['due_month'] == '2') {
                //Query For Weekly.
                $run = $pdo->query("SELECT c.id, c.due_amnt, c.pending_amt, c.payable_amt, c.coll_date, c.due_amt_track, c.fine_charge_track, lelc.due_start, lelc.due_end,lelc.total_amount_calc,lelc.total_customer, lelc.due_month,lelc.scheme_day_calc,lelc.scheme_date,lcm.due_amount,lelc.due_period
            FROM `collection` c
            LEFT JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id
            LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
            WHERE c.cus_mapping_id = '$cus_mapping_id'
            AND c.due_amt_track != '' AND c.due_amt_track > 0
            AND (
                WEEK(c.coll_date) = WEEK('$cusDueMonth') 
                AND YEAR(c.coll_date) = YEAR('$cusDueMonth')
            ) ");
            }

            if ($run->rowCount() > 0) {

                while ($row = $run->fetch()) {
                    $due_amt_track = intVal($row['due_amt_track']);
                    if (!empty($row['due_amount']) && $row['due_amount'] > 0) {
                        $row['overall_amount'] = $row['due_amount'] * $row['due_period'];
                    } else {
                        $row['overall_amount'] = 0;
                    }
                $bal_amt = max(0,$bal_amt - $due_amt_track);



                ?>
                    <tr> <!-- Showing From Due Start date to Maurity date -->
                        <?php
                        if ($loanFrom['due_month'] == '1') { //this is for monthly loan to check lastcusduemonth comparision
                            if (date('Y-m', strtotime($lastCusdueMonth)) != date('Y-m', strtotime($row['coll_date']))) {
                                // this condition is to check whether the same month has collection again. if yes the no need to show month name and due amount and serial number
                        ?>
                                <td><?php echo $i;
                                    $i++; ?></td>
                                <td><?php //For Monthly.
                                    echo date('M-Y', strtotime($cusDueMonth));
                                    ?>
                                </td>
                                <td><?php echo $scheme_date; // Display scheme date 
                                    ?></td>

                                <td><?php echo moneyFormatIndia($row['due_amnt']); ?></td>

                            <?php } else { ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php }
                        } else { //this is for weekly  loan to check lastcusduemonth comparision
                            if (date('Y-m-d', strtotime($lastCusdueMonth)) != date('Y-m-d', strtotime($row['coll_date']))) {
                                // this condition is to check whether the same month has collection again. if yes the no need to show month name and due amount and serial number
                            ?>
                                <td><?php echo $i;
                                    $i++; ?></td>
                                <td><?php
                                    //For Weekly && Day.
                                    echo date('d-m-Y', strtotime($cusDueMonth));
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    // For Weekly & Day, show corresponding weekday name
                                    $daysOfWeek = [
                                        1 => 'Monday',
                                        2 => 'Tuesday',
                                        3 => 'Wednesday',
                                        4 => 'Thursday',
                                        5 => 'Friday',
                                        6 => 'Saturday',
                                        7 => 'Sunday'
                                    ];

                                    if (isset($daysOfWeek[$scheme_day])) {
                                        echo $daysOfWeek[$scheme_day]; // Display the day of the week
                                    } ?>
                                </td>
                                <td><?php echo date('M', strtotime($cusDueMonth)); // Show month for weekly/day scheme 
                                    ?></td>
                                <td><?php echo moneyFormatIndia($row['due_amnt']); ?></td>

                            <?php } else { ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                        <?php }
                        } ?>

                        <td><?php $pendingMinusCollection = (intVal($row['pending_amt']));
                            if ($pendingMinusCollection != '') {
                                echo moneyFormatIndia($pendingMinusCollection);
                            } else {
                                echo 0;
                            } ?></td>
                        <td><?php $payableMinusCollection = (intVal($row['payable_amt']));
                            if ($payableMinusCollection != '') {
                                echo moneyFormatIndia($payableMinusCollection);
                            } else {
                                echo 0;
                            } ?></td>
                        <td><?php echo date('d-m-Y', strtotime($row['coll_date'])); ?></td>

                        <td>
                            <?php if ($row['due_amt_track'] > 0) {
                                echo moneyFormatIndia($row['due_amt_track']);
                            } ?>
                        </td>
                        <td><?php echo moneyFormatIndia($bal_amt); ?></td>
                        <td> <a class='print_due_coll' id="" value="<?php echo $row['id']; ?>"> <i class="fa fa-print" aria-hidden="true"></i> </a> </td>
                    </tr>

                <?php $lastCusdueMonth = date('d-m-Y', strtotime($cusDueMonth)); //assign this cusDueMonth to check if coll date is already showed before
                }
            } else { //if not paid on due month. else part will show.
                ?>
                <tr>
                    <td><?php echo $i; ?></td>

                    <?php if ($loanFrom['due_month'] == '1') { // For Monthly Due Method 
                    ?>
                        <!-- For Monthly Dues -->
                        <td><?php echo date('M-Y', strtotime($cusDueMonth)); // Due No (Month-Year) 
                            ?></td>
                        <td><?php echo $scheme_date; // Month 
                            ?></td>
                        <td><?php echo moneyFormatIndia($due_amt_1); // Due Amount 
                            ?></td>

                        <?php
                        // Logic for pending and payable amounts
      
                        if (date('Y-m', strtotime($cusDueMonth)) <= date('Y-m')) {
                            $response = getNextLoanDetails($pdo, $cus_mapping_id, $cusDueMonth); ?>
                            <td><?php echo moneyFormatIndia($response['pending']); // Pending 
                                ?></td>
                            <td><?php echo moneyFormatIndia($response['payable']); // Payable 
                                ?></td>
                        <?php } else { ?>
                            <td></td>
                            <td></td>
                        <?php } ?>

                    <?php } elseif ($loanFrom['due_month'] == '2') { // For Weekly/Daily Due Method 
                    ?>
                        <!-- For Weekly/Daily Dues -->
                        <td><?php echo date('d-m-Y', strtotime($cusDueMonth)); // Due Date 
                            ?></td>
                        <td><?php
                            // For Weekly & Day, show corresponding weekday name
                            $daysOfWeek = [
                                1 => 'Monday',
                                2 => 'Tuesday',
                                3 => 'Wednesday',
                                4 => 'Thursday',
                                5 => 'Friday',
                                6 => 'Saturday',
                                7 => 'Sunday'
                            ];

                            if (isset($daysOfWeek[$scheme_day])) {
                                echo $daysOfWeek[$scheme_day]; // Display the day of the week
                            } ?></td>
                        <td><?php echo date('M', strtotime($cusDueMonth)); // Month 
                            ?></td>
                        <td><?php echo moneyFormatIndia($due_amt_1); // Due Amount 
                            ?></td>

                        <?php
                        // Logic for pending and payable amounts
                        if (date('Y-m-d', strtotime($cusDueMonth)) <= date('Y-m-d')) {
                            $response = getNextLoanDetails($pdo, $cus_mapping_id, $cusDueMonth); ?>
                            <td><?php echo moneyFormatIndia($response['pending']); // Pending 
                                ?></td>
                            <td><?php echo moneyFormatIndia($response['payable']); // Payable 
                                ?></td>
                     
                        <?php  }else { ?>
                            <td></td>
                            <td></td>
                    <?php
                        }
                    }
                    ?>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

            <?php
                $i++;
            }
        }
        $initial_balance_query = $pdo->query("SELECT lelc.total_amount_calc,lelc.due_period ,lcm.due_amount
        FROM loan_cus_mapping lcm
        LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
        WHERE lcm.id = '$cus_mapping_id'");
    
    $initial_balance_result = $initial_balance_query->fetch();
    $initial_balance = floor($initial_balance_result['due_amount'] * $initial_balance_result['due_period']);
 // Use total_amount_calc or any other dynamic value
    
 $bal_amt = $bal_amt > 0 ? $bal_amt : $initial_balance;
        $currentMonth = date('Y-m-d');

if ($loanFrom['due_month'] == '1') {
    //Query for Monthly.
    $run = $pdo->query("SELECT c.id, c.due_amnt, c.pending_amt, c.payable_amt, c.coll_date, c.due_amt_track, c.fine_charge_track, 
                                lelc.due_start, lelc.due_end, lelc.due_month, lelc.total_amount_calc, lelc.due_period, 
                                lelc.scheme_day_calc, lelc.scheme_date,lcm.due_amount,lcm.loan_amount
                        FROM `collection` c
                        LEFT JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id
                        LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
                        WHERE c.`cus_mapping_id` = '$cus_mapping_id' 
                        AND (c.due_amt_track != '') AND c.due_amt_track > 0
                        AND (MONTH(c.coll_date) > MONTH('$maturity_month') AND Year(c.coll_date) > Year('$maturity_month') 
                             AND MONTH(c.coll_date) <= MONTH('$currentMonth') AND  Year(c.coll_date) <= Year('$currentMonth') 
                             AND c.coll_date != '0000-00-00')");
                             
} else if ($loanFrom['due_month'] == '2') {
    //Query for Weekly.
    $run = $pdo->query("SELECT c.id, c.due_amnt, c.pending_amt, c.payable_amt, c.coll_date, c.due_amt_track, c.fine_charge_track, 
                                lelc.due_start, lelc.due_end, lelc.due_month, lelc.total_amount_calc, lelc.due_period, 
                                lelc.scheme_day_calc, lelc.scheme_date,lcm.due_amount,lcm.loan_amount
                        FROM `collection` c
                        LEFT JOIN loan_cus_mapping lcm ON c.cus_mapping_id = lcm.id
                        LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
                        WHERE c.`cus_mapping_id` = '$cus_mapping_id' 
                        AND (c.due_amt_track != '') AND c.due_amt_track > 0
                        AND (WEEK(c.coll_date) > WEEK('$maturity_month') AND Year(c.coll_date) > Year('$maturity_month') 
                             AND WEEK(c.coll_date) <= WEEK('$currentMonth') AND Year(c.coll_date) <= Year('$maturity_month') 
                             AND c.coll_date != '0000-00-00')");
}

if ($run->rowCount() > 0) {
    $due_amt_track = 0;
    $waiver = 0;
    while ($row = $run->fetch()) {
        $collectionAmnt = intVal($row['due_amt_track']);
        $due_amt_track = intVal($row['due_amt_track']);
        
        // Calculate overall_amount if total_amount_calc exists
        if (!empty($row['due_amount']) && $row['due_amount'] > 0) {
            $row['overall_amount'] = $row['due_amount'] * $row['due_period'];
        } else {
            $row['overall_amount'] = 0;
        }
        $bal_amt -= $due_amt_track;

        // Display the data based on monthly or weekly due_month
        if ($loanFrom['due_month'] == '1') {
            // Monthly Display
            ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php $pendingMinusCollection = (intVal($row['pending_amt']));
                        if ($pendingMinusCollection != '') {
                            echo moneyFormatIndia($pendingMinusCollection);
                        } else {
                            echo 0;
                        } ?></td>
                    <td><?php $payableMinusCollection = (intVal($row['payable_amt']));
                        if ($payableMinusCollection != '') {
                            echo moneyFormatIndia($payableMinusCollection);
                        }
                        ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row['coll_date'])); ?></td>
                    <td>
                        <?php if ($row['due_amt_track'] > 0) {
                            echo moneyFormatIndia($row['due_amt_track']);
                        } ?>
                    </td>
                    <td><?php echo moneyFormatIndia($bal_amt); ?></td>
                    <td> <a class='print_due_coll' id="" value="<?php echo $row['id']; ?>"> <i class="fa fa-print" aria-hidden="true"></i> </a> </td>
            </tr>
            <?php
        } else if ($loanFrom['due_month'] == '2') {
            // Weekly Display
            ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php $pendingMinusCollection = (intVal($row['pending_amt']));
                        if ($pendingMinusCollection != '') {
                            echo moneyFormatIndia($pendingMinusCollection);
                        } else {
                            echo 0;
                        } ?></td>
                    <td><?php $payableMinusCollection = (intVal($row['payable_amt']));
                        if ($payableMinusCollection != '') {
                            echo moneyFormatIndia($payableMinusCollection);
                        }
                        ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row['coll_date'])); ?></td>
                    <td>
                        <?php if ($row['due_amt_track'] > 0) {
                            echo moneyFormatIndia($row['due_amt_track']);
                        } ?>
                    </td>
                    <td><?php echo moneyFormatIndia($bal_amt); ?></td>
                    <td> <a class='print_due_coll' id="" value="<?php echo $row['id']; ?>"> <i class="fa fa-print" aria-hidden="true"></i> </a> </td>
            </tr>
            <?php
        }
        ?>
           <?php
                $i++;
            }
        }
        ?>
    </tbody>
</table>

<?php
function getNextLoanDetails($pdo, $cus_mapping_id, $date)
{
    $loan_arr = array();
    $coll_arr = array();
    $response = array(); //Final array to return

    $result = $pdo->query("SELECT lcm.due_amount ,lelc.* FROM `loan_entry_loan_calculation` lelc LEFT JOIN loan_cus_mapping lcm ON lcm.loan_id = lelc.loan_id WHERE lcm.id = $cus_mapping_id ");
    if ($result->rowCount() > 0) {
        $row = $result->fetch();
        $loan_arr = $row;

        //(For monthly interest total amount will not be there, so take principals)
        $response['total_amt'] = floatval($loan_arr['due_amount']) * $loan_arr['due_period'];;
        $response['loan_type'] = 'emi';

        $response['due_amnt'] = $loan_arr['due_amount'] ; //Due amount will remain same
    }
    $coll_arr = array();
    $result = $pdo->query("SELECT * FROM `collection` WHERE cus_mapping_id = $cus_mapping_id ");
    if ($result->rowCount() > 0) {
        while ($row = $result->fetch()) {
            $coll_arr[] = $row;
        }
        $total_paid = 0;
        $total_paid_int = 0;


        foreach ($coll_arr as $tot) {
            $total_paid += intVal($tot['due_amt_track']); //only calculate due amount not total paid value, because it will have penalty and coll charge also
        }
        //total paid amount will be all records again request id should be summed
        $response['total_paid'] =  $total_paid;
        $response['total_paid_int'] = $total_paid_int;

        //total amount subracted by total paid amount and subracted with pre closure amount will be balance to be paid
        $response['balance'] = $response['total_amt'] - $response['total_paid'];

        $response = calculateOthers($loan_arr, $response, $date, $pdo);
    } else {
        //If collection table dont have rows means there is no payment against that request, so total paid will be 0
        $response['total_paid'] = 0;
        $response['total_paid_int'] = 0;
        $response['pre_closure'] = 0;
        //If in collection table, there is no payment means balance amount still remains total amount
        $response['balance'] = $response['total_amt'];

        $response = calculateOthers($loan_arr, $response, $date, $pdo);
    }

    //To get the collection charges
    $result = $pdo->query("SELECT SUM(fine_charge) as fine_charge FROM `fine_charges` WHERE cus_mapping_id = '" . $cus_mapping_id . "' ");
    $row = $result->fetch();
    if ($row['fine_charge'] != null) {

        $coll_charges = $row['fine_charge'];

        $result = $pdo->query("SELECT SUM(fine_charge_track) as coll_charge_track FROM `collection` WHERE cus_mapping_id = '" . $cus_mapping_id . "' ");
        if ($result->rowCount() > 0) {
            $row = $result->fetch();
            $coll_charge_track = $row['coll_charge_track'];
        } else {
            $coll_charge_track = 0;
        }

        $response['fine_charge'] = $coll_charges - $coll_charge_track;
    } else {
        $response['fine_charge'] = 0;
    }

    return $response;
}

function calculateOthers($loan_arr, $response, $date, $pdo)
{

    if (isset($_POST['cus_mapping_id'])) {
        $cus_mapping_id = $_POST['cus_mapping_id'];
    }
    //***************************************************************************************************************************************************
    $due_start_from = $loan_arr['due_start'];
    $maturity_month = $loan_arr['due_end'];

    $tot_paid_tilldate = 0;
    $preclose_tilldate = 0;


    $checkcollection = $pdo->query("SELECT SUM(`due_amt_track`) as totalPaidAmt FROM `collection` WHERE `cus_mapping_id` = '$cus_mapping_id'"); // To Find total paid amount till Now.
    $checkrow = $checkcollection->fetch();
    $totalPaidAmt = $checkrow['totalPaidAmt'] ?? 0; //null collation operator
    $checkack = $pdo->query("SELECT lelc.intrest_amount_calc,lelc.due_amount_calc,lelc.due_period,lcm.due_amount FROM `loan_entry_loan_calculation` lelc LEFT JOIN loan_cus_mapping lcm ON lelc.loan_id = lcm.loan_id  WHERE lcm.id = '$cus_mapping_id'"); // To Find Due Amount.
    $checkAckrow = $checkack->fetch();
    $due_amnt = $checkAckrow['due_amount'];

    if ($loan_arr['due_month'] == '1') {

        //Convert Date to Year and month, because with date, it will use exact date to loop months, instead of taking end of month
        $due_start_from = date('Y-m', strtotime($due_start_from));
        $maturity_month = date('Y-m', strtotime($maturity_month));

        // Create a DateTime object from the given date
        $maturity_month = new DateTime($maturity_month);
        // Subtract one month from the date
        // $maturity_month->modify('-1 month');
        // Format the date as a string
        $maturity_month = $maturity_month->format('Y-m');

        //If Due method is Monthly, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m', strtotime($date));

        $start_date_obj = DateTime::createFromFormat('Y-m', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m', $current_date);

        $interval = new DateInterval('P1M'); // Create a one month interval
        //condition start
        $count = 0;
        $loandate_tillnow = 0;
        $countForPenalty = 0;

        $dueCharge = $due_amnt;
        $start = DateTime::createFromFormat('Y-m', $due_start_from);
        $current = DateTime::createFromFormat('Y-m', $current_date);

        $monthsElapsed = $start_date_obj->diff($current_date_obj)->m + ($start_date_obj->diff($current_date_obj)->y * 12) + 1;
        $toPayTillPrev = ($monthsElapsed - 1) * $dueCharge;
        $toPayTillNow = $monthsElapsed * $dueCharge;

        while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) { // To find loan date count till now from start date.
            $penalty_checking_date  = $start_date_obj->format('Y-m-d'); // This format is for query.. month , year function accept only if (Y-m-d).
            $penalty_date  = $start_date_obj->format('Y-m');
            $start_date_obj->add($interval);

            $checkcollection = $pdo->query("SELECT * FROM `collection` WHERE `cus_mapping_id` = '$cus_mapping_id' 
            AND MONTH(coll_date) = MONTH('$penalty_checking_date') 
            AND YEAR(coll_date) = YEAR('$penalty_checking_date')");
            $collectioncount = $checkcollection->rowCount(); // Checking whether the collection are inserted on date or not by using penalty_raised_date.

            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT overdue_penalty AS overdue, penalty_type AS penal_type FROM loan_category_creation WHERE id = '" . $loan_arr['loan_category'] . "'");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent AS overdue, scheme_penalty_type AS penal_type FROM scheme WHERE id = '" . $loan_arr['scheme_name'] . "'");
            }
            $row = $result->fetch();
            $penalty_per = $row['overdue'];
            $penalty_type = $row['penal_type'];

            // Calculate penalty
            if ($penalty_type == 'percent') {
                $penalty = round(($dueCharge * $penalty_per) / 100);
            } else {
                $penalty = $penalty_per;
            }

            if ($totalPaidAmt < $toPayTillNow && $collectioncount == 0) {
                $checkPenalty = $pdo->query("SELECT * FROM penalty_charges WHERE penalty_date = '$penalty_date' AND `cus_mapping_id` = '$cus_mapping_id'");
                if ($checkPenalty->rowCount() == 0) {
                }
                $countForPenalty++;
            }

            $count++; //Count represents how many months are exceeded
        }
        //condition END

        //this collection query for taking the paid amount until the looping date ($current_date) , to calculate dynamically for due chart
        $qry = $pdo->query("SELECT SUM(due_amt_track) as due_amt_track FROM `collection` 
        WHERE `cus_mapping_id` = '$cus_mapping_id'
        AND 
        ( (YEAR(coll_date) = YEAR('$date') AND MONTH(coll_date) <= MONTH('$date')) 
        OR (YEAR(coll_date) < YEAR('$date')) )");
        if ($qry->rowCount() > 0) {
            $rowss = $qry->fetch();
            $tot_paid_tilldate = intVal($rowss['due_amt_track']);
        }
        if ($count > 0) {

            //if Due month exceeded due amount will be as pending with how many months are exceeded and subract pre closure amount if available
            $response['pending'] = max(0, round($toPayTillPrev - $tot_paid_tilldate));

            // If due month exceeded
            if (empty($loan_arr['scheme_name'])) {
                $result = $pdo->query("SELECT overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "'");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "'");
            }
            $row = $result->fetch();
            $penalty_per = number_format($row['overdue'] * $countForPenalty); //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

            // to get overall penalty paid till now to show pending penalty amount
            $result = $pdo->query("SELECT SUM(penalty_track) as penalty FROM `collection` WHERE `cus_mapping_id` = '$cus_mapping_id'");
            $row = $result->fetch();
            $total_penalty = ($row['penalty'] === null) ? 0 : $row['penalty'];

            // Calculate total penalty raised till now
            $result1 = $pdo->query("SELECT SUM(penalty) as penalty FROM `penalty_charges` WHERE cus_mapping_id = '$cus_mapping_id'");
            $row1 = $result1->fetch();
            $penalty = ($row1['penalty'] === null) ? 0 : $row1['penalty'];

            // Subtract penalty paid from total penalty
            $response['penalty'] = $penalty - $total_penalty;

            if ($response['pending']  > 0) {
                $response['payable']  =   max(0, $response['pending']  + $response['due_amnt']);
            }else{
                $response['payable']  = max(0, $toPayTillNow - $tot_paid_tilldate);  
            }

            if ($response['payable'] > $response['balance']) {
                //if payable is greater than balance then change it as balance amt coz dont collect more than balance
                //this case will occur when collection status becoms OD
                $response['payable'] = $response['balance'];
            }
        } else {
            //If still current month is not ended, then pending will be same due amt // pending will be 0 if due date not exceeded
            $response['pending'] = 0; // $response['due_amt'] - $response['total_paid'] - $response['pre_closure'] ;
            //If still current month is not ended, then penalty will be 0
            $response['penalty'] = 0;
            //If still current month is not ended, then payable will be due amt
            $response['payable'] = max(0, round($response['due_amnt'] - $tot_paid_tilldate));
        }
    } else
    if ($loan_arr['due_month'] == '2') {

        //If Due method is Weekly, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d', strtotime($date));

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1W'); // Create a one Week interval
        //condition start
        $count = 0;
        $loandate_tillnow = 0;
        $countForPenalty = 0;

        $dueCharge = $due_amnt;
        $weeksElapsed = floor($start_date_obj->diff($current_date_obj)->days / 7) + 1;
        $toPayTillPrev = ($weeksElapsed -1) * $dueCharge;
        $toPayTillNow = ($weeksElapsed) * $dueCharge;

        // Debugging logs

        $penalty = 0;
        $count = 0;

        while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) { // To find loan date count till now from start date.

            $penalty_checking_date  = $start_date_obj->format('Y-m-d'); // This format is for query.. month , year function accept only if (Y-m-d).
            $start_date_obj->add($interval);

            $checkcollection = $pdo->query("SELECT * FROM `collection` WHERE `cus_mapping_id` = '$cus_mapping_id' 
            AND (WEEK(coll_date) = WEEK('$penalty_checking_date') AND YEAR(coll_date) = YEAR('$penalty_checking_date'))");
            $collectioncount = $checkcollection->rowCount(); // Checking whether the collection are inserted on date or not by using penalty_raised_date.

            if (empty($loan_arr['scheme_name'])) {
                $result = $pdo->query("SELECT overdue_penalty AS overdue, penalty_type AS penal_type FROM loan_category_creation WHERE id = '" . $loan_arr['loan_category'] . "'");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent AS overdue, scheme_penalty_type AS penal_type FROM scheme WHERE id = '" . $loan_arr['scheme_name'] . "'");
            }
            $row = $result->fetch();
            $penalty_per = $row['overdue'];
            $penalty_type = $row['penal_type'];

            // Calculate penalty
            if ($penalty_type == 'percent') {
                $penalty = round(($dueCharge * $penalty_per) / 100);
            } else {
                $penalty = $penalty_per;
            }
            $count++;

            if ($totalPaidAmt < $toPayTillNow && $collectioncount == 0) {
                $checkPenalty = $pdo->query("SELECT * from penalty_charges where penalty_date = '$penalty_checking_date' and cus_mapping_id = '$cus_mapping_id'");
                if ($checkPenalty->rowCount() == 0) {
                    // Handle penalty charge here if needed
                }
                $countForPenalty++;
            }
        }
        //condition END

        //this collection query for taking the paid amount until the looping date ($current_date) , to calculate dynamically for due chart
        $qry = $pdo->query("SELECT sum(due_amt_track) as due_amt_track FROM `collection` 
        WHERE cus_mapping_id = '$cus_mapping_id' 
        AND (
            (YEAR(coll_date) = YEAR('$current_date') AND WEEK(coll_date) <= WEEK('$current_date')) 
            OR (YEAR(coll_date) < YEAR('$current_date'))
        )");
        if ($qry->rowCount() > 0) {
            $rowss = $qry->fetch();
            $tot_paid_tilldate = intval($rowss['due_amt_track']);
        }
        if ($count > 0) {
            //if Due month exceeded due amount will be as pending with how many months are exceeded and subract pre closure amount if available
            $response['pending'] = max(0, round($toPayTillPrev - $tot_paid_tilldate));

            // If due month exceeded
            if (empty($loan_arr['scheme_name'])) {
                $result = $pdo->query("SELECT overdue_penalty AS overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "'");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent AS overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "'");
            }
            $row = $result->fetch();
            $penalty_per = number_format($row['overdue'] * $countForPenalty);
            //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

            // to get overall penalty paid till now to show pending penalty amount
            $result = $pdo->query("SELECT SUM(penalty_track) as penalty FROM `collection` WHERE cus_mapping_id = '$cus_mapping_id'");
            $row = $result->fetch();
            $row['penalty'] = $row['penalty'] ?? 0;

            $result1 = $pdo->query("SELECT SUM(penalty) as penalty FROM `penalty_charges` WHERE cus_mapping_id = '$cus_mapping_id'");
            $row1 = $result1->fetch();
            $penalty = $row1['penalty'] ?? 0;

            // Calculate pending penalty
            $response['penalty'] = $penalty - $row['penalty'];

            if ($response['pending']  > 0) {
                $response['payable']  =   max(0, $response['pending']  + $response['due_amnt']);
            }else{
                $response['payable']  = max(0, $toPayTillNow - $tot_paid_tilldate);  
            }
   
            if ($response['payable'] > $response['balance']) {
                //if payable is greater than balance then change it as balance amt coz dont collect more than balance
                //this case will occur when collection status becoms OD
                $response['payable'] = max(0, $response['balance']);
            }
        } else {
            //If still current month is not ended, then pending will be same due amt // pending will be 0 if due date not exceeded
            $response['pending'] = 0; // $response['due_amt'] - $response['total_paid'] - $response['pre_closure'] ;
            //If still current month is not ended, then penalty will be 0
            $response['penalty'] = 0;
            //If still current month is not ended, then payable will be due amt
            $response['payable'] =  max(0, round($response['due_amnt'] - $tot_paid_tilldate - $preclose_tilldate));
        }
    }
    if ($response['pending'] < 0) {
        $response['pending'] = 0;
    }
    if ($response['payable'] < 0) {
        $response['payable'] = 0;
    }
    return $response;
}
?>