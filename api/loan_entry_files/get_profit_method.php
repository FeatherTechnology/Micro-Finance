<?php
require "../../ajaxconfig.php";

$scheme_arr = array();
$loanCatId = $_POST['loanCatId'];
$profit_arr = [1 => 'Calculation', 2 => 'Scheme'];

$qry = $pdo->query("SELECT lcc.id, lcc.profit_type FROM `loan_category_creation` lcc WHERE lcc.id = '$loanCatId'");

if ($qry->rowCount() > 0) {
    $scheme_arr = $qry->fetch(PDO::FETCH_ASSOC);
    
    // Handle cases where profit_type contains multiple values (e.g., '1,2')
    $profit_types = explode(',', $scheme_arr['profit_type']);
    $mapped_profit_types = array_map(function($type) use ($profit_arr) {
        return $profit_arr[(int)$type] ?? '';
    }, $profit_types);

    // Join the mapped profit types back into a comma-separated string
    $scheme_arr['profit_type'] = implode(', ', $mapped_profit_types);
}

$pdo = null; // Close connection
echo json_encode($scheme_arr);

