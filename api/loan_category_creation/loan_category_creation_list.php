<?php
require '../../ajaxconfig.php';

$loanCatCreation_list_arr = array();
$status_arr = [0 => 'Disable', 1 => 'Enable'];

// Fetch loan category creation data
$qry = $pdo->query("SELECT lcc.id, lc.loan_category, lcc.loan_limit, lcc.status 
                    FROM loan_category_creation lcc 
                    LEFT JOIN loan_category lc ON lcc.loan_category = lc.id");

if ($qry->rowCount() > 0) {
    while ($loanCatCreationInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        // Assign the human-readable status
        $loanCatCreationInfo['status'] = $status_arr[$loanCatCreationInfo['status']];

        // Add action buttons with appropriate ID
        $loanCatCreationInfo['action'] = "<span class='icon-border_color loanCatCreationActionBtn' value='" . $loanCatCreationInfo['id'] . "'></span> 
                                          <span class='icon-trash-2 loanCatCreationDeleteBtn' value='" . $loanCatCreationInfo['id'] . "'></span>";

        // Append to the result array
        $loanCatCreation_list_arr[] = $loanCatCreationInfo;
    }
}

// Close the PDO connection
$pdo = null;

// Return the result as JSON
echo json_encode($loanCatCreation_list_arr);
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