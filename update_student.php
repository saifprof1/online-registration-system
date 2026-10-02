<?php

require_once "config/database.php";

$student = null;
$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['update_student'])) {

        $student_id = strtoupper(trim($_POST['student_id'] ?? ''));
        $first_name = trim($_POST['first_name'] ?? '');
        $middle_name = trim($_POST['middle_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $father_name = trim($_POST['father_name'] ?? '');
        $mother_name = trim($_POST['mother_name'] ?? ''); 

        if (
    empty($student_id) ||
    empty($first_name) ||
    empty($last_name) ||
    empty($father_name) ||
    empty($mother_name)
) {

            $error_message = "First Name and Last Name are required.";

        } else {

            $sql = "UPDATE students
        SET first_name = ?,
            middle_name = ?,
            last_name = ?,
            father_name = ?,
            mother_name = ?
        WHERE student_id = ?";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {

                $error_message = "Update query preparation failed.";

            } else {

                $stmt->bind_param(
                        "ssssss",
                    $first_name,
                    $middle_name,
                    $last_name,
                    $father_name,
                    $mother_name,
                    $student_id
                );

                if ($stmt->execute()) {

                    $success_message =
                        "Student information updated successfully.";

                    $select_sql =
                        "SELECT * FROM students WHERE student_id = ?";

                    $select_stmt = $conn->prepare($select_sql);

                    if ($select_stmt) {

                        $select_stmt->bind_param("s", $student_id);
                        $select_stmt->execute();

                        $result = $select_stmt->get_result();

                        if ($result->num_rows === 1) {
                            $student = $result->fetch_assoc();
                        }

                        $select_stmt->close();
                    }

                } else {

                    $error_message =
                        "Student information could not be updated.";
                }

                $stmt->close();
            }
        }

    } else {

        $student_id = strtoupper(trim($_POST['student_id'] ?? ''));

        if (empty($student_id)) {

            $error_message = "Please enter Student ID.";

        } else {

            $sql = "SELECT * FROM students WHERE student_id = ?";

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
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student</title>
</head>

<body>

    <h2>Update Student Information</h2>

    <?php if (!empty($error_message)): ?>

        <p style="color: red;">
            <?php echo htmlspecialchars($error_message); ?>
        </p>

    <?php endif; ?>

    <?php if (!empty($success_message)): ?>

    <p style="color: green;">
        <?php echo htmlspecialchars($success_message); ?>
    </p>

<?php endif; ?>

    <form method="POST">

        <label for="student_id">Student ID:</label>

        <input
            type="text"
            id="student_id"
            name="student_id"
            placeholder="Enter Student ID"
            required
        >

        <button type="submit">Search Student</button>

    </form>

    <?php if ($student !== null): ?>

    <hr>

    <h3>Student Information</h3>

    <form method="POST">

        <label>Student ID:</label>
        <input
            type="text"
            name="student_id"
            value="<?php echo htmlspecialchars($student['student_id']); ?>"
            readonly
        >

        <br><br>

        <label>First Name:</label>
        <input
            type="text"
            name="first_name"
            value="<?php echo htmlspecialchars($student['first_name']); ?>"
            required
        >

        <br><br>

        <label>Middle Name:</label>
        <input
            type="text"
            name="middle_name"
            value="<?php echo htmlspecialchars($student['middle_name']); ?>"
        >

        <br><br>

        <label>Last Name:</label>
        <input
            type="text"
            name="last_name"
            value="<?php echo htmlspecialchars($student['last_name']); ?>"
            required
        >

        <br><br>

        <label>Father Name:</label>
<input
    type="text"
    name="father_name"
    value="<?php echo htmlspecialchars($student['father_name']); ?>"
    required
>

<br><br>

<label>Mother Name:</label>
<input
    type="text"
    name="mother_name"
    value="<?php echo htmlspecialchars($student['mother_name']); ?>"
    required
>

<br><br>

        <button type="submit" name="update_student">
            Update Student
        </button>

    </form>

<?php endif; ?>

</body>
</html>