<?php
require '../../ajaxconfig.php';
@session_start();
class overallClass
{
    public function getCustomerStatus($pdo, $customer_map_id)
    {
        $customer_status = '';
        $qry = $pdo->query("SELECT lelc.total_amount_calc,lelc.total_customer, sum(c.total_paid_track + c.fine_charge_track + c.penalty_track)as total_paid, sum(fc.fine_charge + pc.penalty)as payable_charge 
                            from loan_cus_mapping cm
                            left join fine_charges fc On fc.cus_mapping_id = cm.id
                            left join collection c On c.cus_mapping_id =cm.id
                            left join loan_entry_loan_calculation lelc On lelc.loan_id =cm.loan_id
                            left join penalty_charges pc on pc.cus_mapping_id=cm.id WHERE cm.id='$customer_map_id'");
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
                    $customer_status = "Due Null";
                } else if ($paid_amount < $amount_per_cus) { 
                    $customer_status = "OD";
                }
            
        }
        $pdo = null;
        return $customer_status;
    }
}//Class End
