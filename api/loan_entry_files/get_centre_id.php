<?php
require('../../ajaxconfig.php');
@session_start();
$user_id = $_SESSION['user_id'];
// Query to fetch all centre_id from centre_creation
$qry = $pdo->query("SELECT cc.id, cc.centre_id FROM centre_creation cc JOIN users u ON FIND_IN_SET(cc.id, u.centre_name) where u.id='$user_id'");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$filtered_centres = array(); // Array to hold filtered centre ids

// Loop through each centre_id from centre_creation
foreach ($result as $row) {
    $centre_id = $row['centre_id'];

    // Query to check if the centre_id exists in loan_entry_loan_calculation table
    $loan_qry = $pdo->query("
        SELECT lelc.loan_id, lelc.loan_status, cs.closed_sub_status
        FROM loan_entry_loan_calculation lelc
        LEFT JOIN closed_loan cs ON lelc.centre_id = cs.centre_id
        WHERE lelc.centre_id = '$centre_id'
    ");

    // If the centre_id is not present in loan_entry_loan_calculation table, it should be shown
    if ($loan_qry->rowCount() == 0) {
        $filtered_centres[] = array(
            'id' => $row['id'],
            'centre_id' => $centre_id,
            'status' => 'can_show'
        );
    } else {
        // If centre_id is present in loan_entry_loan_calculation, check loan status and loan_id
        $can_show = false; // Default to not show the centre_id

        while ($loan_row = $loan_qry->fetch(PDO::FETCH_ASSOC)) {
            // If sub_status is 2, mark as not_show and break out of the loop
            if ($loan_row['closed_sub_status'] == 2) {
                $can_show = false;
                break; // No need to check further
            }

            // If loan_status is 5 or 6 and loan_id > 9, mark as can_show
            if (in_array($loan_row['loan_status'], [5, 6]) || $loan_row['loan_status'] >= 9) {
                $can_show = true;
                break; // No need to check further once the condition is met
            }

            // If loan_status is in [1, 2, 3, 4, 7, 8, 9], mark as not_show
            if (in_array($loan_row['loan_status'], [1, 2, 3, 4, 7, 8])) {
                $can_show = false;
                break; // No need to check further once the condition is met
            }
        }

        // If eligible, mark as 'can_show', otherwise 'not_show'
        $filtered_centres[] = array(
            'id' => $row['id'],
            'centre_id' => $centre_id,
            'status' => $can_show ? 'can_show' : 'not_show'
        );
    }
}

$pdo = null; // Close connection

// Output the filtered result as JSON
echo json_encode($filtered_centres);
?>
