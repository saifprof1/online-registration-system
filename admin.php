<?php

require_once "config/database.php";

$student = null;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $student_id = strtoupper(trim($_GET['student_id'] ?? ''));

    if (!empty($student_id)) {

        $sql = "
            SELECT *
            FROM students
            WHERE student_id = ?
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {

            $error_message = "Query preparation failed.";

        } else {

            $stmt->bind_param("s", $student_id);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows === 1) {

                $student = $result->fetch_assoc();

            } else {

                $error_message = "Student not found.";
            }

            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Backend</title>

</head>

<body>

    <h1>Admin Backend</h1>

    <hr>

    <h2>Search Student</h2>

    <form method="GET">

        <label for="student_id">
            Student ID:
        </label>

        <input
            type="text"
            name="student_id"
            id="student_id"
            placeholder="Enter Student ID"
            value="<?php echo htmlspecialchars($_GET['student_id'] ?? ''); ?>"
            required
        >

        <button type="submit">
            Search
        </button>

    </form>

    <br><br>

<a href="view_students.php">
    <button type="button">
        View All Students
    </button>
</a>

<br><br>


    <?php if (!empty($error_message)): ?>

        <p style="color: red;">
            <?php echo htmlspecialchars($error_message); ?>
        </p>

    <?php endif; ?>


    <?php if ($student !== null): ?>

        <hr>

        <h2>Student Information</h2>

        <p>
            <strong>Student ID:</strong>
            <?php echo htmlspecialchars($student['student_id']); ?>
        </p>

        <p>
            <strong>Name:</strong>
            <?php

            echo htmlspecialchars(
                trim(
                    $student['first_name'] . " " .
                    $student['middle_name'] . " " .
                    $student['last_name']
                )
            );

            ?>
        </p>

        <p>
            <strong>Father Name:</strong>
            <?php echo htmlspecialchars($student['father_name']); ?>
        </p>

        <p>
            <strong>Mother Name:</strong>
            <?php echo htmlspecialchars($student['mother_name']); ?>
        </p>

        <p>
            <strong>Phone:</strong>
            <?php echo htmlspecialchars($student['phone']); ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?php echo htmlspecialchars($student['email']); ?>
        </p>

        <p>
            <strong>Gender:</strong>
            <?php echo htmlspecialchars($student['gender']); ?>
        </p>


        <br>


        <!-- Action Buttons -->


        <a
            href="update_student.php?student_id=<?php
                echo urlencode($student['student_id']);
            ?>"
        >
            <button type="button">
                Update
            </button>
        </a>


        <a
            href="delete_student.php?student_id=<?php
                echo urlencode($student['student_id']);
            ?>"
        >
            <button type="button">
                Delete
            </button>
        </a>


    <?php endif; ?>

</body>

</html>