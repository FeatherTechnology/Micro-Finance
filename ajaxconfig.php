<?php
$timeZoneQry = "SET time_zone = '+5:30' ";
$host = "localhost";
$db_user = "root";
$db_pass = "";
$dbname = "micro_finance";
$pdo = new PDO("mysql:host=$host; dbname=$dbname", $db_user, $db_pass);
$pdo->exec($timeZoneQry);