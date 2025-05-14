<?php
require '../../ajaxconfig.php';

$loan_id = $_POST['loan_id'];

$qry = $pdo->query("SELECT cc.centre_id,cc.centre_no,cc.centre_name,cc.mobile1, anc.areaname, bc.branch_name,lc.loan_category,lelc.loan_id, lelc.loan_amount_calc, lelc.principal_amount_calc, lelc.intrest_amount_calc, lelc.total_amount_calc, lelc.due_amount_calc, lelc.document_charge_cal, lelc.processing_fees_cal, lelc.net_cash_calc, lelc.due_start, lelc.due_end, lelc.due_period, lelc.profit_type, lelc.due_month, lelc.scheme_day_calc,lelc.loan_date,lelc.total_customer , lcc.procrssing_fees_type , lelc.doc_charge , lelc.interest_rate , lelc.processing_fees , lelc.benefit_method
FROM loan_entry_loan_calculation lelc 
 LEFT JOIN centre_creation cc ON lelc.centre_id = cc.centre_id
 LEFT JOIN area_name_creation anc ON cc.area = anc.id
LEFT JOIN branch_creation bc ON cc.branch = bc.id
LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
WHERE lelc.loan_id ='$loan_id' ");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as &$row) {
        if (!empty($row['loan_date'])) {
            $loan_date = $row['loan_date'];
            $formatted_loan_date = date('d/m/Y', strtotime($loan_date));
            $row['loan_date'] = $formatted_loan_date;
        }
    }
}
$pdo = null; //Close connection.

echo json_encode($result);
