<?php

$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "online_registration";
$port = 3306;

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database,
    $port
);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

echo "Database connection successful!";

$conn->close();

?>