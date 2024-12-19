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
<table class="table custom-table" id='collectionChargeListTable'>
    <thead>
        <tr>
            <th> S.No </th>
            <th> Date </th>
            <th> Fine </th>
            <th> Purpose </th>
            <th> Paid Date </th>
            <th> Paid Amount</th>
            <th> Balance Amount</th>
        </tr>
    </thead>
    <tbody>

        <?php
        $cus_mapping_id = $_POST['cus_mapping_id'];
        $run = $pdo->query("SELECT * FROM `fine_charges` WHERE `cus_mapping_id`= '$cus_mapping_id' ");

        $i = 1;
        $charge = 0;
        $paid = 0;
        $waiver = 0;
        $bal_amnt = 0;
        while ($row = $run->fetch()) {
            $collCharges = ($row['fine_charge']) ? $row['fine_charge'] : '0';
            $charge = $charge + $collCharges; 
            $paidAmount = ($row['paid_amnt']) ? $row['paid_amnt'] : '0';
            $paid = $paid + $paidAmount;
            $bal_amnt = $charge - $paid;
        ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php if(isset($row['fine_date'])) echo date('d-m-Y',strtotime($row['fine_date'])); ?></td>
                <td><?php echo $collCharges; ?></td>
                <td><?php echo $row['fine_purpose']; ?></td>
                <td><?php if(isset($row['paid_date'])) echo date('d-m-Y',strtotime($row['paid_date'])); ?></td>
                <td><?php echo $paidAmount; ?></td>
                <td><?php echo $bal_amnt; ?></td>
            </tr>

        <?php $i++;
        } 
        $sumchargesAmnt = $pdo->query("SELECT sum(fine_charge) as charges,sum(paid_amnt) as paidAmnt FROM `fine_charges` WHERE `cus_mapping_id`= '$cus_mapping_id' ");
        $sumAmnt = $sumchargesAmnt->fetch();
        $charges = $sumAmnt['charges'];
        $paid_amt = $sumAmnt['paidAmnt'];
        ?>
    </tbody>
    <tr>
        <td></td>
        <td></td>
        <td><b><?php echo $charges; ?></b></td>
        <td></td>
        <td></td>
        <td><b><?php echo $paid_amt; ?></b></td>
        <td><b><?php echo $bal_amnt; ?></b></td>
    </tr>
</table>

<script type="text/javascript">
    $(function() {
        setdtable('#collectionChargeListTable');
    });
</script>