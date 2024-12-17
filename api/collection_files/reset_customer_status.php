<?php
require '../../ajaxconfig.php';
@session_start();

class CollectStsClass
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function updateCollectStatus($loan_id)
    {
        $currentMonth = date('m');
        $currentYear = date('Y');
        $currentDate = new DateTime(); // Current date for comparison

        // Query to fetch all customers mapped to the loan
        $qry = "SELECT lcm.id as cp_id, lcm.loan_id, lcm.issue_status, lelc.due_start, lelc.due_end, lelc.due_month, lelc.scheme_day_calc
                FROM loan_cus_mapping lcm
                LEFT JOIN loan_entry_loan_calculation lelc ON lcm.loan_id = lelc.loan_id
                WHERE lcm.loan_id = '$loan_id' ";

        $statement = $this->pdo->query($qry);
        $customers = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Default status is 'Payable'
        $overallStatus = 'Payable';

        foreach ($customers as $customer) {
            // Extract required details
            $dueMethod = $customer['due_month'];
            $dueStartDate = new DateTime($customer['due_start']);
            $dueEndDate = new DateTime($customer['due_end']);
            $schemeDay = $customer['scheme_day_calc']; // 0 = Sunday, 1 = Monday, etc.

            // Check the paid customer count for this loan
            $paidCustomersCount = $this->pdo->query("SELECT COUNT(cus_mapping_id) as paid_count 
                FROM collection 
                WHERE loan_id = '$loan_id' 
                AND coll_status = 'Paid' ")->fetch(PDO::FETCH_ASSOC)['paid_count'];

            if ($dueMethod == 1) { // Monthly due method
                if ($currentDate > $dueEndDate) {
                    $overallStatus = 'OD'; // Overdue if past due-end date
                } elseif ($currentMonth != $dueStartDate->format('m')) {
                    $overallStatus = 'Pending'; // Pending if due-end hasn't passed but not in the current month
                } 
                // else if ($paidCustomersCount == 0) {
                //     $overallStatus = 'Pending'; // Pending if no customers have paid within the month
                // } 
                else {
                    $overallStatus = 'Payable'; // Payable if customers are paying within the period
                }
            } elseif ($dueMethod == 2) { // Scheme day method
                $dueDate = clone $dueStartDate;
                // Set due date to the next occurrence of the scheme day (e.g., Monday)
                while ($dueDate->format('w') != $schemeDay) {
                    $dueDate->modify('+1 day');
                }
                if ($currentDate > $dueEndDate) {
                    $overallStatus = 'OD'; // Overdue if past due-end date
                } elseif ($currentDate > $dueDate) {
                    $overallStatus = 'Pending'; // Pending if scheme day hasn't come yet
                } 
                // else if ($paidCustomersCount == 0) {
                //     $overallStatus = 'Pending'; // Pending if no customers have paid within the scheme period
                // } 
                else {
                    $overallStatus = 'Payable'; // Payable if customers are paying on scheme day
                }
            }
        }

        return $overallStatus;
    }
}
?>
