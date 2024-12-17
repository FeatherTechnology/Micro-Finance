<?php
require '../../ajaxconfig.php';

$loan_id = $_POST['loan_id'];

$response = array(); // Final array to return
$response['success'] = false; // Default to false

// Fetch total and due amounts from the loan_entry_loan_calculation table
$loan_query = $pdo->query("SELECT * FROM `loan_entry_loan_calculation` WHERE loan_id = '$loan_id'");
if ($loan_query->rowCount() > 0) {
    $loan_row = $loan_query->fetch();

    // Total Amount Calculation (If total_amount_calc is not available, use principal_amount_calc)
    $response['total_amount_calc'] = !empty($loan_row['total_amount_calc']) ? $loan_row['total_amount_calc'] : $loan_row['principal_amount_calc'];

    // Due Amount Calculation (If due_amount_calc is not available, set it as 0)
    $response['due_amount_calc'] = !empty($loan_row['due_amount_calc']) ? $loan_row['due_amount_calc'] : 0;
} else {
    // If no loan found, return an error response
    $response['message'] = "Loan not found";
    echo json_encode($response);
    exit;
}

// Fetch paid amounts from the collection table
$collection_query = $pdo->query("SELECT * FROM `collection` WHERE loan_id = '$loan_id'");
$total_paid = 0; // Total paid amount initialization

if ($collection_query->rowCount() > 0) {
    while ($coll_row = $collection_query->fetch()) {
        // Sum the paid amounts from the collection table
        $total_paid += intval($coll_row['due_amt_track']); // Sum due amounts paid for this loan
    }

    $response['total_paid'] = $total_paid;

    // Calculate the balance as (total amount - total paid)
    $response['balance'] = $response['total_amount_calc'] - $response['total_paid'];
} else {
    // If no payments found, set total paid as 0 and balance as the total amount
    $response['total_paid'] = 0;
    $response['balance'] = $response['total_amount_calc'];
}

$response['success'] = true; // Set success to true after all data is fetched

// Return the final response as JSON
echo json_encode($response);
?>
