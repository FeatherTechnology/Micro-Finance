<?php
require "../../../ajaxconfig.php";

$loan_issue_list_arr = array();
$cash_type = $_POST['cash_type'];
$bank_id = $_POST['bank_id'];

if ($cash_type == '1') {
    $cndtn = "coll_mode = '1' ";
    $cndtn1 = "issue_type = '1' ";
} elseif ($cash_type == '2') {
    $cndtn = "coll_mode != '1' AND bank_id = '$bank_id' ";
    $cndtn1 = "issue_type != '1' ";
}
//collection_mode = 1 - cash; 2 to 5 - bank;
$current_date = date('Y-m-d');
$qry = $pdo->query("SELECT b.name,c.branch_name,COUNT(li.id) AS no_of_loans, SUM(li.issue_amount) AS issueAmnt FROM loan_issue li JOIN users b ON li.insert_login_id = b.id JOIN branch_creation c ON b.branch = c.id WHERE $cndtn1 AND DATE(li.issue_date) = '$current_date' GROUP BY li.insert_login_id ");
if ($qry->rowCount() > 0) {
    while ($data = $qry->fetch(PDO::FETCH_ASSOC)) {
        // $amnt = ($data['amount']) ? $data['amount'] : 0;
        // $cramnt = ($data['cr_amnt']) ? $data['cr_amnt'] : 0;
        // $bal = $amnt - $data['overallIssueAmnt'] - $cramnt;
        $data['no_of_loans'] = ($data['no_of_loans']) ? $data['no_of_loans'] : 0;
        $data['issueAmnt'] = ($data['issueAmnt']) ? moneyFormatIndia($data['issueAmnt']) : 0;
        // $data['balance'] = moneyFormatIndia($bal);
        $loan_issue_list_arr[] = $data;
    }
}
$pdo = null; //Close connection.
echo json_encode($loan_issue_list_arr);

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
