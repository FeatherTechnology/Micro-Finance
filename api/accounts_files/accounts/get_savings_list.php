<?php
require "../../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$current_date = date('Y-m-d');

$cash_type = ["1" => 'Hand Cash', "2" => 'Bank Cash'];
$crdr = ["1" => 'Credit', "2" => 'Debit'];
$trans_list_arr = array();
$qry = $pdo->query("SELECT id, `invoice_id`, `coll_mode`, `aadhar_number`, `cus_name`, `description`, `amount`, `cat_type` FROM `savings` WHERE `insert_login_id`='$user_id' ");
if ($qry->rowCount() > 0) {
    while ($result = $qry->fetch()) {
        
        $result['coll_mode'] = $cash_type[$result['coll_mode']];
        
        $result['cat_type'] = $crdr[$result['cat_type']];

        $result['amount'] = moneyFormatIndia($result['amount']);
        $result['action'] = "<span class='icon-trash-2 savingsDeleteBtn' value='" . $result['id'] . "'></span>";
        $trans_list_arr[] = $result;
    }
}

echo json_encode($trans_list_arr);

//Format number in Indian Format
function moneyFormatIndia($num1)
{
    if ($num1 < 0) {
        $num = str_replace("-", "", $num1);
    } else {
        $num = $num1;
    }
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

    if ($num1 < 0 && $num1 != '') {
        $thecash = "-" . $thecash;
    }

    return $thecash;
}
