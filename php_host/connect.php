<?php
$host = "mysql-db";
$dbname = "herb_db";
$username = "root";
$password = "root";
$port = "3306";
$pdo = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>