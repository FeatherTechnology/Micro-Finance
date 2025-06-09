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
<table class="table custom-table" id='savingsListTable'>
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
       
        if (isset($_POST['cus_id']) && $_POST['cus_id'] !='') {
        $cus_id = $_POST['cus_id'];
        $where = "WHERE cs.cus_id = '$cus_id' GROUP BY cs.centre_id, cs.cus_id" ;
        }
        else{
        $where = " GROUP BY cs.centre_id";
        }
        $run = $pdo->query("SELECT 
    cc.centre_name,
    cc.centre_no,
    cs.centre_id,
    cs.cus_id,
    MIN(cs.cus_name) AS cus_name,
    MIN(cs.aadhar_num) AS aadhar_num,
    SUM(CASE WHEN cs.credit_debit = 1 THEN savings_amount ELSE 0 END) AS total_credit,
    SUM(CASE WHEN cs.credit_debit = 2 THEN savings_amount ELSE 0 END) AS total_debit
FROM 
    customer_savings cs 
    LEFT JOIN centre_creation cc ON cc.centre_id = cs.centre_id 
$where 
HAVING 
    SUM(CASE WHEN cs.credit_debit = 1 THEN savings_amount ELSE 0 END) - 
    SUM(CASE WHEN cs.credit_debit = 2 THEN savings_amount ELSE 0 END) > 0 ");

        $i = 1;
        $totalsavings = 0;
        while ($row = $run->fetch()) {
            $total_credit = $row['total_credit'];
            $total_debit =$row['total_debit']; 
            $balance = $total_credit - $total_debit ;
        ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $row['centre_id'] ?></td>
                <td><?php echo $row['centre_name']  ?></td>
                <td><?php echo $row['centre_no']  ?></td>
                <td><?php echo moneyFormatIndia($balance); ?></td>
                <?php if($_POST['cus_id']!= ''){?>
                <td><?php echo "<button class='btn btn-primary Savings-chart' value='" . $row['cus_id'] . "' centre_id='" . $row['centre_id'] . "'> Savings Chart </button>  " ?></td>
                <?php } else { ?>                
                <td><?php echo "<button class='btn btn-primary centre_view' value='" . $row['centre_id'] . "'> View Centre </button>  " ?></td> <?php
                } ?>
            </tr>

        <?php $i++;
        }
        ?>

</tbody>
</table>

