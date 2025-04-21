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
            <th> Savings </th>
            <th> Total Savings </th>
        </tr>
    </thead>
    <tbody>

        <?php
        $cus_mapping_id = $_POST['cus_mapping_id'];
        $run = $pdo->query("SELECT * FROM `customer_savings` WHERE `cus_map_id`= '$cus_mapping_id' ORDER BY paid_date ");

        $i = 1;
        $totalsavings = 0;
        while ($row = $run->fetch()) {
            $paid_date = $row['paid_date'];
            $savings_amount =$row['savings_amount']; 
            $total_savings =$pdo->query("SELECT sum(savings_amount)as savings FROM `customer_savings` WHERE `cus_map_id`= '$cus_mapping_id ' and paid_date < '$paid_date'"); 
            $tot_savings = $total_savings->fetch()
        ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $row['paid_date']!=''?date('d-m-Y',strtotime($row['paid_date'])):''; ?></td>
                <td><?php echo moneyFormatIndia($savings_amount); ?></td>
                <td><?php echo moneyFormatIndia(!empty($tot_savings['savings']) ? $tot_savings['savings'] : 0); ?></td>
            </tr>

        <?php $i++;
        }
        ?>

</tbody>
</table>

<script type="text/javascript">
    $(function() {
        setdtable('#savingsListTable');
    });
</script>