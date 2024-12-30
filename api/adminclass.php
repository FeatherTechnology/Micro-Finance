<?php
require '../../ajaxconfig.php';
@session_start();
class overallClass
{
    public function getCustomerStatus($pdo, $customer_map_id)
    {
        $customer_status = '';
//         $qry = $pdo->query("SELECT COALESCE(lelc.total_amount_calc, 0) AS total_amount_calc,
//     COALESCE(lelc.total_customer, 0) AS total_customer,SUM(COALESCE(c.total_paid_track, 0) + COALESCE(c.fine_charge_track, 0) + COALESCE(c.penalty_track, 0)) AS total_paid,
//     SUM(COALESCE(f.fine_charge, 0) + COALESCE(p.penalty, 0)) AS payable_charge
//                           FROM
//     `collection` c
// LEFT JOIN fine_charges f ON
//     f.cus_mapping_id = c.cus_mapping_id
// LEFT JOIN penalty_charges p ON
//     p.cus_mapping_id = c.cus_mapping_id
// LEFT JOIN loan_entry_loan_calculation lelc  ON
//    lelc.loan_id = c.loan_id WHERE c.cus_mapping_id='$customer_map_id'");

$qry = $pdo->query("SELECT
c.cus_mapping_id,
SUM(c.total_paid_track) AS total_paid_track,
SUM(c.fine_charge_track) AS total_fine_charge_track,
SUM(c.penalty_track) AS total_penalty_track,
(SUM(c.total_paid_track) + SUM(c.fine_charge_track) + SUM(c.penalty_track)) AS total_paid,
IFNULL(f.total_fine_charge, 0) AS total_fine_charge,
IFNULL(p.total_penalty, 0) AS total_penalty,
(SUM(c.total_paid_track) + IFNULL(f.total_fine_charge, 0) + IFNULL(p.total_penalty, 0)) AS payable_charge,
lelc.total_amount_calc,lelc.total_customer
FROM
collection c
LEFT JOIN
(SELECT cus_mapping_id, SUM(fine_charge) AS total_fine_charge FROM fine_charges GROUP BY cus_mapping_id) f
ON f.cus_mapping_id = c.cus_mapping_id
LEFT JOIN
(SELECT cus_mapping_id, SUM(penalty) AS total_penalty FROM penalty_charges GROUP BY cus_mapping_id) p
ON p.cus_mapping_id = c.cus_mapping_id
LEFT JOIN
loan_entry_loan_calculation lelc
ON lelc.loan_id = c.loan_id
WHERE
c.cus_mapping_id = '$customer_map_id'
GROUP BY
c.cus_mapping_id");
        if ($qry->rowCount() > 0) {
            $res = $qry->fetch(PDO::FETCH_ASSOC);

            $total_loan_amount = $res['total_amount_calc'];
            $total_customer = $res['total_customer'];
            $amount_per_cus = ($total_loan_amount / $total_customer);
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
