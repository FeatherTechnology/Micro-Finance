<?php
$timeZoneQry = "SET time_zone = '+5:30' ";
$host = "192.168.1.3";
$db_user = "db_user";
$db_pass = "dbpassword@123";
$dbname = "micro_finance";
$pdo = new PDO("mysql:host=$host; dbname=$dbname", $db_user, $db_pass);
$pdo->exec($timeZoneQry);


date_default_timezone_set('Asia/Kolkata');
