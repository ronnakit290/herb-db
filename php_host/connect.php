<?php
$host = "mysql";
$dbname = "herb_db";
$username = "herb_user";
$password = "herb_password";
$port = "3306";
$pdo = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>