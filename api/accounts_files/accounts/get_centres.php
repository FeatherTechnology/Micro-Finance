<?php
require '../../../ajaxconfig.php';

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
<table class="table custom-table" id='centreListTable'>
    <thead>
        <tr>
            <th width='20'> S.No </th>
            <th> Centre ID </th>
            <th>Centre Name</th>
            <th> Centre Number</th>
            <th> Total Savings</th>
            <th> Action</th>
        </tr>
    </thead>
    <tbody>

        <?php
        $run = $pdo->query("SELECT 
    cc.centre_id,
    cc.centre_name,
    cc.centre_no,
    IFNULL(SUM(CASE WHEN cs.credit_debit = 1 THEN cs.savings_amount ELSE 0 END), 0) AS total_credit,
    IFNULL(SUM(CASE WHEN cs.credit_debit = 2 THEN cs.savings_amount ELSE 0 END), 0) AS total_debit
FROM 
    centre_creation cc
LEFT JOIN 
    loan_cus_mapping lcm ON lcm.centre_id = cc.centre_id AND lcm.issue_status = 1
LEFT JOIN 
    customer_savings cs ON cs.centre_id = cc.centre_id
LEFT JOIN 
    users u ON FIND_IN_SET(cc.id, u.centre_name)
LEFT JOIN 
    loan_entry_loan_calculation lelc ON lelc.loan_id = lcm.loan_id
WHERE 
    FIND_IN_SET(cc.id, u.centre_name) > 0
    AND (lcm.issue_status = 1 OR cs.id IS NOT NULL)
    AND (lelc.loan_status IN (4, 7, 8) OR lelc.loan_status IS NULL)
GROUP BY 
    cc.centre_id ");

        $i = 1;
        $totalsavings = 0;
        while ($row = $run->fetch()) {
            $total_credit =(int)$row['total_credit'];
            $total_debit =(int)$row['total_debit']; 
            $balance = $total_credit - $total_debit ;
        ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $row['centre_id'] ?></td>
                <td><?php echo $row['centre_name']  ?></td>
                <td><?php echo $row['centre_no']  ?></td>
                <td><?php echo moneyFormatIndia($balance); ?></td>               
                <td><?php echo "<button class='btn btn-primary centre_view' value='" . $row['centre_id'] . "'> View Centre </button>  " ?></td> 
            </tr>

        <?php $i++;
        }
        ?>

</tbody>
</table>

