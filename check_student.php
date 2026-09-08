<?php

require_once "config/database.php";

if (isset($_GET['student_id'])) {

    $student_id = trim($_GET['student_id']);

    $stmt = $conn->prepare(
        "SELECT student_id FROM students WHERE student_id = ?"
    );

    $stmt->bind_param("s", $student_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "exists";
    } else {
        echo "available";
    }

    $stmt->close();
}

$conn->close();

?>