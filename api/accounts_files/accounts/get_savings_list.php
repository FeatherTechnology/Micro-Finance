<?php
require "../../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$current_date = date('Y-m-d');

$cash_type = ["1" => 'Hand Cash', "2" => 'Bank Cash'];
$crdr = ["1" => 'Credit', "2" => 'Debit'];
$trans_list_arr = array();
$qry = $pdo->query("SELECT cus_id,id,cus_name , aadhar_num, SUM(CASE WHEN credit_debit = 1 THEN savings_amount ELSE 0 END) AS total_credit, SUM(CASE WHEN credit_debit = 2 THEN savings_amount ELSE 0 END) AS total_debit
    FROM customer_savings GROUP BY cus_id");
if ($qry->rowCount() > 0) {
    while ($result = $qry->fetch()) {
        $result['total_credit'] = (int)$result['total_credit'];
        $result['total_debit'] = (int)$result['total_debit'];
        $result['balance'] = moneyFormatIndia($result['total_credit'] - $result['total_debit']);
        $result['action'] = "<button class='btn btn-primary Savings-chart' value='" . $result['cus_id'] . "'> Savings Chart </button>";
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
