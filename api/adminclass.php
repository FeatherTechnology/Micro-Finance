<?php
require '../../ajaxconfig.php';
@session_start();
class overallClass
{
    public function getCustomerStatus($pdo, $customer_map_id)
    {
        $customer_status = '';
   
        $qry = $pdo->query("SELECT
    COALESCE(lelc.total_amount_calc, 0) AS total_amount_calc,
    lelc.due_period,
    cm.due_amount,
    COALESCE(lelc.total_customer, 0) AS total_customer,
    -- Using subquery to avoid double-counting in total_paid
    (SELECT SUM(COALESCE(c.total_paid_track, 0)) 
     FROM collection c 
     WHERE c.cus_mapping_id = cm.id) AS total_paid,
    -- Summing distinct fine_charge and penalty to avoid duplication
    COALESCE(SUM(DISTINCT fc.fine_charge), 0) AS fine_charge,
    COALESCE(SUM(DISTINCT pc.penalty), 0) AS penalty,
    COALESCE(SUM(DISTINCT fc.fine_charge), 0) + COALESCE(SUM(DISTINCT pc.penalty), 0) AS payable_charge
FROM
    loan_cus_mapping cm
LEFT JOIN fine_charges fc ON
    fc.cus_mapping_id = cm.id
LEFT JOIN loan_entry_loan_calculation lelc ON
    lelc.loan_id = cm.loan_id
LEFT JOIN penalty_charges pc ON
    cm.id = pc.cus_mapping_id
WHERE
    cm.id = '$customer_map_id'
GROUP BY
    lelc.total_amount_calc, lelc.due_period, cm.due_amount, lelc.total_customer;'
");
        if ($qry->rowCount() > 0) {
            $res = $qry->fetch(PDO::FETCH_ASSOC);

            $total_loan_amount = $res['due_amount'] * $res['due_period'];
            $amount_per_cus = round($total_loan_amount);
            $total_payable = $amount_per_cus + $res['payable_charge'];
            $paid_amount = $res['total_paid'];
            if ($total_payable == $paid_amount) {
                $customer_status = "Closed";
            } elseif ($paid_amount >= $amount_per_cus) {
                $customer_status = "Due Nil";
            } else if ($paid_amount < $amount_per_cus) {
                $customer_status = "OD";
            }
        }
        $pdo = null;
        return $customer_status;
    }
}//Class End
