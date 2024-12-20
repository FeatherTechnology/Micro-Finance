<?php
require "../../../ajaxconfig.php";

$loan_issue_list_arr = array();
$cash_type = $_POST['cash_type'];
$bank_id = $_POST['bank_id'];

if ($cash_type == '1') {
    $cndtn = "coll_mode = '1' ";
    $cndtn1 = "payment_mode = '1' ";
} elseif ($cash_type == '2') {
    $cndtn = "coll_mode != '1' AND bank_id = '$bank_id' ";
    $cndtn1 = "payment_mode != '1' ";
}
//collection_mode = 1 - cash; 2 to 5 - bank;
$qry = $pdo->query("SELECT b.name, c.linename, no_of_loans, issueAmnt
FROM other_transaction a 
JOIN users b ON a.user_name = b.id 
JOIN line_name_creation c ON b.line = c.id 
LEFT JOIN ( SELECT insert_login_id, COUNT(id) AS no_of_loans, SUM(issue_amnt) AS issueAmnt FROM loan_issue WHERE $cndtn1 AND DATE(issue_date) = CURDATE() GROUP BY insert_login_id ) li ON li.insert_login_id = b.id
WHERE a.type = '2' AND a.trans_cat = '7' AND $cndtn
GROUP BY b.name, c.linename, no_of_loans, issueAmnt; ");
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



// "SELECT b.name, c.linename, no_of_loans, overallIssueAmnt, issueAmnt, SUM(a.amount) AS amount, cr_amnt
// FROM other_transaction a 
// JOIN users b ON a.user_name = b.id 
// JOIN line_name_creation c ON b.line = c.id 
// LEFT JOIN (
//     SELECT insert_login_id, SUM(issue_amnt) AS overallIssueAmnt 
//     FROM loan_issue WHERE $cndtn1 GROUP BY insert_login_id
// ) oli ON oli.insert_login_id = b.id 
// LEFT JOIN (
//     SELECT insert_login_id, COUNT(id) AS no_of_loans, SUM(issue_amnt) AS issueAmnt 
//     FROM loan_issue WHERE $cndtn1 AND DATE(issue_date) = CURDATE() GROUP BY insert_login_id
// ) li ON li.insert_login_id = b.id 
// LEFT JOIN(
// 	SELECT user_name, SUM(amount) AS cr_amnt
//     FROM other_transaction 
//     WHERE type = '1' AND trans_cat = '7' AND $cndtn
// ) cr ON cr.user_name = b.id
// WHERE a.type = '2' AND a.trans_cat = '7' AND $cndtn
// GROUP BY b.name, c.linename, no_of_loans, issueAmnt; "


// SELECT 
//     b.name, 
//     c.linename, 
//     COALESCE(li.no_of_loans, 0) AS no_of_loans,
//     COALESCE(li.issueAmnt, 0) AS issueAmnt,
//     (SUM(a.amount) - COALESCE(li.issueAmnt, 0)) AS balance_in_hand
// FROM 
//     other_transaction a 
// JOIN 
//     users b ON a.user_name = b.id 
// JOIN 
//     line_name_creation c ON b.line = c.id 
// LEFT JOIN (
//     SELECT 
//         insert_login_id, 
//         COUNT(id) AS no_of_loans, 
//         SUM(issue_amnt) AS issueAmnt 
//     FROM 
//         loan_issue 
//     WHERE 
//         $cndtn1 
//         AND DATE(issue_date) = CURDATE() 
//     GROUP BY 
//         insert_login_id
// ) li ON li.insert_login_id = b.id 
// WHERE 
//     a.type = '2' 
//     AND a.trans_cat = '7' 
//     AND $cndtn
//     AND a.created_on = (SELECT MAX(created_on) FROM other_transaction WHERE type = '2' AND trans_cat = '7' AND $cndtn)
// GROUP BY 
//     b.name, 
//     c.linename, 
//     li.no_of_loans, 
//     li.issueAmnt;


// "SELECT b.name, c.linename, no_of_loans, overallIssueAmnt, issueAmnt, SUM(a.amount) AS amount, cr_amnt 
// FROM other_transaction a 
// JOIN users b ON a.user_name = b.id 
// JOIN line_name_creation c ON b.line = c.id 
// LEFT JOIN ( SELECT insert_login_id, SUM(issue_amnt) AS overallIssueAmnt FROM loan_issue WHERE $cndtn1 AND DATE(created_on) >= (SELECT DATE(created_on) FROM other_transaction WHERE type = '2' AND trans_cat = '7' AND $cndtn ORDER BY id ASC LIMIT 1 ) GROUP BY insert_login_id ) oli ON oli.insert_login_id = b.id
// LEFT JOIN ( SELECT insert_login_id, COUNT(id) AS no_of_loans, SUM(issue_amnt) AS issueAmnt FROM loan_issue WHERE $cndtn1 AND DATE(issue_date) = CURDATE() GROUP BY insert_login_id ) li ON li.insert_login_id = b.id 
// LEFT JOIN( SELECT user_name, SUM(amount) AS cr_amnt FROM other_transaction WHERE type = '1' AND trans_cat = '7' AND $cndtn ) cr ON cr.user_name = b.id 
// WHERE a.type = '2' AND a.trans_cat = '7' AND $cndtn
// GROUP BY b.name, c.linename, no_of_loans, issueAmnt; "