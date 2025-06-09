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
<table class="table custom-table" id='savingsListTable'>
    <thead>
        <tr>
            <th width='20'> S.No </th>
            <th> Date </th>
            <th>Credit / Debit</th>
            <th> Savings </th>
            <th> Total Savings </th>
        </tr>
    </thead>
    <tbody>

        <?php
        $cus_id = $_POST['cus_id'];
        $centre_id = $_POST['centre_id'];
        $run = $pdo->query("SELECT cs.id, cs.cus_id, cs.paid_date, cs.credit_debit, cs.savings_amount,
       ( SELECT GREATEST(
                    SUM(
                      CASE 
                        WHEN cs2.credit_debit = '1' THEN cs2.savings_amount
                        WHEN cs2.credit_debit = '2' THEN -cs2.savings_amount
                        ELSE 0
                      END
                    ), 0
                )
         FROM customer_savings cs2
         WHERE cs2.cus_id = cs.cus_id
           AND cs2.centre_id = cs.centre_id  
           AND (cs2.paid_date < cs.paid_date 
                OR (cs2.paid_date = cs.paid_date AND cs2.id <= cs.id))
       ) AS total_savings
FROM customer_savings cs
WHERE cs.cus_id = '$cus_id ' AND cs.centre_id = '$centre_id'
ORDER BY cs.paid_date, cs.id;
");

        $i = 1;
        $totalsavings = 0;
        while ($row = $run->fetch()) {
            $paid_date = $row['paid_date'];
            $savings_amount =$row['savings_amount']; 
            $total_savings_amount =$row['total_savings']; 
            $credit_debit = $row['credit_debit']; 
        ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $row['paid_date']!=''?date('d-m-Y',strtotime($row['paid_date'])):''; ?></td>
                <td><?php echo $row['credit_debit'] == 1 ? 'Credit' : ($row['credit_debit'] == 2 ? 'Debit' : ''); ?></td>
                <td><?php echo moneyFormatIndia($savings_amount); ?></td>
                <td><?php echo moneyFormatIndia(!empty($total_savings_amount) ? $total_savings_amount : 0); ?></td>
            </tr>

        <?php $i++;
        }
        ?>

</tbody>
</table>
