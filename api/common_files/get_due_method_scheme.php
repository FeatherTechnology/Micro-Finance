<?php
require "../../ajaxconfig.php";
$scheme_arr = array();
$id = $_POST['id'];
$profitType = $_POST['profitType'];
$qry = $pdo->query("SELECT s.id, s.scheme_name
FROM `loan_category_creation` lcc 
JOIN scheme s ON FIND_IN_SET(s.id, lcc.scheme_name)
WHERE lcc.id = '$id' AND FIND_IN_SET('$profitType', profit_type) 
GROUP BY s.id ");
if ($qry->rowCount() > 0) {
    $scheme_arr = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Connection Close.
echo json_encode($scheme_arr);