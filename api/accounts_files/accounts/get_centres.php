<?php
require '../../../ajaxconfig.php';

$trans_list_arr = array();
$qry = $pdo->query("SELECT 
    cc.centre_id,
    cc.centre_name,
    cc.centre_no,
    IFNULL(cs.total_credit, 0) AS total_credit,
    IFNULL(cs.total_debit, 0) AS total_debit
FROM 
    centre_creation cc
LEFT JOIN (
    SELECT 
        centre_id,
        SUM(CASE WHEN credit_debit = 1 THEN savings_amount ELSE 0 END) AS total_credit,
        SUM(CASE WHEN credit_debit = 2 THEN savings_amount ELSE 0 END) AS total_debit
    FROM customer_savings
    GROUP BY centre_id
) cs ON cs.centre_id = cc.centre_id
LEFT JOIN loan_cus_mapping lcm 
    ON lcm.centre_id = cc.centre_id AND lcm.issue_status = 1
LEFT JOIN loan_entry_loan_calculation lelc 
    ON lelc.loan_id = lcm.loan_id
LEFT JOIN users u 
    ON FIND_IN_SET(cc.id, u.centre_name)
WHERE 
    FIND_IN_SET(cc.id, u.centre_name) > 0
    AND (lcm.issue_status = 1 OR cs.centre_id IS NOT NULL)
    AND (lelc.loan_status IN (4, 7, 8) OR lelc.loan_status IS NULL)
GROUP BY cc.centre_id ");
    
if ($qry->rowCount() > 0) {
    while ($result = $qry->fetch()) {
        $result['balance'] = moneyFormatIndia((int)$result['total_credit'] - $result['total_debit']);
        $result['action'] = "<button class='btn btn-primary centre_view' value='" . $result['centre_id'] . "'> View Centre </button> ";
        $trans_list_arr[] = $result;
    }
}
$pdo = null; //Close connection.
echo json_encode($trans_list_arr);

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

