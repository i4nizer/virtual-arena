<?php
// connections credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "virtual_arena";
$dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";

// optional set of attributes to set
$options = array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false
);

// connection string
$pdo = new PDO($dsn, $username, $password, $options);