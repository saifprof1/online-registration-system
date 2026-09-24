<?php

echo "Step 1: PHP is working.<br>";

mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_init();

echo "Step 2: mysqli initialized.<br>";

$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);

echo "Step 3: Starting MySQL connection...<br>";

$result = $conn->real_connect(
    "127.0.0.1",
    "root",
    "",
    "",
    3306
);

if (!$result) {

    echo "Step 4: Connection failed.<br>";
    echo "Error: " . $conn->connect_error . "<br>";

} else {

    echo "Step 4: MySQL server connected successfully.<br>";

    $result = $conn->query("SELECT VERSION()");

    if ($result) {

        $row = $result->fetch_row();

        echo "Step 5: MySQL Version: " . $row[0] . "<br>";

    } else {

        echo "Step 5: Query failed.<br>";

    }

    $conn->close();
}

echo "Step 6: Test completed.";

?>