<?php
require '../../ajaxconfig.php';
$coll_id = $_POST["coll_id"];

$qry = $pdo->query("SELECT * FROM `collection` WHERE id='" . strip_tags($coll_id) . "'");
$row = $qry->fetch();

extract($row); // Extracts the array values into variables

$qry = $pdo->query("SELECT lelc.loan_id, lc.loan_category,lelc.centre_id,cc.centre_name,cuc.cus_id,cuc.first_name,anc.areaname
FROM loan_cus_mapping lcm
LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
LEFT JOIN customer_creation cuc ON lcm.cus_id = cuc.id
LEFT JOIN centre_creation cc ON cc.centre_id = lelc.centre_id
 LEFT JOIN area_name_creation anc ON cc.area = anc.id
LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
WHERE lcm.id = '$cus_mapping_id'");
$row = $qry->fetch();
$loan_category = $row['loan_category'];
$loan_id = $row['loan_id'];
$centre_id = $row['centre_id'];
$centre_name = $row['centre_name'];
$cus_id = $row['cus_id'];
$cus_name = $row['first_name'];
$area = $row['areaname'];

$due_amt_track = intVal($due_amt_track != '' ? $due_amt_track : 0);
$fine_charge_track = intVal($fine_charge_track != '' ? $fine_charge_track : 0);
$net_received = $due_amt_track + $fine_charge_track;
$due_balance = ($due_amnt - $due_amt_track) < 0 ? 0 : $due_amnt - $due_amt_track;
$loan_balance = getBalance($pdo, $cus_mapping_id, $coll_date, $loan_id);
?>
<style>
    @media print {
        * {
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }

        @page {
            margin: 0;
            /* Remove default print margin */
        }

        body {
            margin: 0;
            padding: 0;
        }

        #dettable {
            margin: 0;
            padding: 0;
            width: 58mm;
            /* Width of thermal printer roll */
            font-size: 8px;
            line-height: 1.2;
            text-align: left;
        }

        .overlap-group {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .captions,
        .data {
            width: 50%;
            word-wrap: break-word;
            text-align: left;
        }

        .mar-logo {
            width: 100px;
            margin: 0 auto;
            /* Center align logo */
            display: block;
        }
    }
</style>


<div class="frame" id="dettable" style="background-color: #ffffff; font-size: 8px; display: flex;flex-direction: column; align-items: flex-start;">
    <div style="display: flex; justify-content: center;padding-bottom:10px;"><img class="micro-logo" alt="Micro-Finance" src="img/logo.jpg" style="width: 125px; height: 125px;" /></div>
    <div class="overlap-group" style="display: flex; justify-content: center; gap: 10px;">

        <div class="captions" style="display: flex; flex-direction: column; align-items: flex-end;">
            <div>Centre ID :</div>
            <div>Centre Name :</div>
            <div>Customer ID :</div>
            <b><div>Customer Name :</div></b>
            <div>Loan Category :</div>
            <div>Loan ID :</div>
            <div>Date :</div>
            <div>Time :</div>
            <div>Area :</div>
            <div>Due Amount :</div>
            <div>Fine :</div>
           <b> <div>Net Received :</div></b> 
           <b>  <div>Due Balance :</div>   </b>
           <b> <div>Loan Balance :</div>   </b>
        </div>
        <div class="data" style="display: flex; flex-direction: column; align-items: flex-start;">
            <div><?php echo $centre_id; ?></div>
            <div><?php echo $centre_name; ?></div>
            <div><?php echo $cus_id; ?></div>
            <b> <div><?php echo $cus_name; ?></div> </b>
            <div><?php echo $loan_category; ?></div>
            <div><?php echo $loan_id; ?></div>
            <div><?php echo date('d-m-Y', strtotime($coll_date)); ?></div>
            <div><?php echo date('H:i A', strtotime($created_on)); ?></div>
            <div><?php echo $area; ?></div>
            <div><?php echo moneyFormatIndia($due_amt_track); ?></div>
            <div><?php echo moneyFormatIndia($fine_charge_track); ?></div>
            <b>  <div><?php echo moneyFormatIndia($net_received); ?></div>  </b> 
            <b> <div><?php echo moneyFormatIndia($due_balance); ?></div>  </b>
            <b>  <div><?php echo moneyFormatIndia($loan_balance); ?></div>  </b>
        </div>
    </div>
</div>



<button type="button" name="printpurchase" onclick="poprint()" id="printpurchase" class="btn btn-primary">Print</button>

<script type="text/javascript">
    function poprint() {
        var Bill = document.getElementById("dettable").innerHTML;
        var printWindow = window.open('', '_blank', 'height=1000;weight=1000;');
        printWindow.document.write(`<html><head></head><body>${Bill}</body></html>`);
        printWindow.document.close();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 1500);
    }
    document.getElementById("printpurchase").click();
</script>

<?php
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

function getBalance($pdo, $cus_mapping_id, $coll_date, $loan_id)
{
    $result = $pdo->query("SELECT lcm.due_amount,lelc.due_period,lelc.* FROM loan_cus_mapping lcm JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id WHERE lcm.id = '$cus_mapping_id' ");
    if ($result->rowCount() > 0) {
        $row = $result->fetch();
        $loan_arr = $row;
        $response['total_amt'] =   floatval($loan_arr['due_amount']) * $loan_arr['due_period'];
    }
    $coll_arr = array();
    $result = $pdo->query("SELECT * FROM `collection` WHERE cus_mapping_id ='" . $cus_mapping_id . "' and date(coll_date) <= date('" . $coll_date . "') ");
    if ($result->rowCount() > 0) {
        while ($row = $result->fetch()) {
            $coll_arr[] = $row;
        }
        $total_paid = 0;
        foreach ($coll_arr as $tot) {
            $total_paid += intVal($tot['due_amt_track']); //only calculate due amount not total paid value, because it will have penalty and coll charge also
        }
        //total paid amount will be all records again request id should be summed
        $response['total_paid'] =  $total_paid;
        $response['balance'] = $response['total_amt'] - $response['total_paid'];
    } else {
        $response['balance'] = $response['total_amt'];
    }

    return $response['balance'];
}
?>