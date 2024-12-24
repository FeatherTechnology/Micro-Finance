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
<table class="table custom-table" id='penaltyListTable'>
    <thead>
        <tr>
            <th width='20'> S.No </th>
            <th> Penalty Date </th>
            <th> Penalty </th>
            <th> Paid Date </th>
            <th> Paid Amount </th>
            <th> Balance Amount </th>
        </tr>
    </thead>
    <tbody>

        <?php
        $cus_mapping_id = $_POST['cus_mapping_id'];
        $run = $pdo->query("SELECT * FROM `penalty_charges` WHERE `cus_mapping_id`= '$cus_mapping_id' ORDER BY created_date ");

        $i = 1;
        $penalt = 0;
        $paid = 0;
        $waiver = 0;
        while ($row = $run->fetch()) {
            $penaltys = ($row['penalty']) ? $row['penalty'] : '0';
            $penalt = $penalt + $penaltys; 
            $paid_amount = ($row['paid_amnt']) ? $row['paid_amnt'] : '0';
            $paid = $paid + $paid_amount;
            $bal_amnt = $penalt - $paid;
        ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $row['penalty_date']!=''?date('d-m-Y',strtotime($row['penalty_date'])):''; ?></td>
                <td><?php echo $penaltys; ?></td>
                <td><?php echo $row['paid_date']!=''?date('d-m-Y',strtotime($row['paid_date'])):''; ?></td>
                <td><?php echo $paid_amount; ?></td>
                <td><?php echo $bal_amnt; ?></td>
            </tr>

        <?php $i++;
        }

        $sumPenaltyAmnt = $pdo->query("SELECT sum(penalty) as penalte,sum(paid_amnt) as paidAmnt FROM `penalty_charges` WHERE `cus_mapping_id`= '$cus_mapping_id' ");
        $sumAmnt = $sumPenaltyAmnt->fetch();
        $penalty = $sumAmnt['penalte'];
        $paid_amt = $sumAmnt['paidAmnt'];

        ?>

</tbody>
<tr>
    <td></td>
    <td></td>
    <td><b><?php echo moneyFormatIndia($penalty); ?></b></td>
    <td></td>
    <td><b><?php echo moneyFormatIndia($paid_amt); ?></b></td>
    <td></td>
</tr>
</table>

<script type="text/javascript">
    $(function() {
        setdtable('#penaltyListTable');
    });
</script>