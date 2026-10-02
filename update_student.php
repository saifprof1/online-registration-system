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
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $date_of_birth = $_POST['date_of_birth'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $address = trim($_POST['address'] ?? '');

$department_id = $_POST['department_id'] ?? '';
$session_id = $_POST['session_id'] ?? '';
$batch_id = $_POST['batch_id'] ?? '';
$year_id = $_POST['year_id'] ?? '';
$semester_id = $_POST['semester_id'] ?? ''; 

        if (
    empty($student_id) ||
    empty($first_name) ||
    empty($last_name) ||
    empty($father_name) ||
    empty($mother_name) ||
    empty($phone) ||
    empty($department_id) ||
    empty($session_id) ||
    empty($batch_id) ||
    empty($year_id) ||
    empty($semester_id)
) {
    $error_message = "Please fill in all required fields.";
} else {

            $sql = "UPDATE students
        SET first_name = ?,
            middle_name = ?,
            last_name = ?,
            father_name = ?,
            mother_name = ?,
            phone = ?,
            email = ?,
            date_of_birth = ?,
            gender = ?,
            address = ?,
            department_id = ?,
            session_id = ?,
            batch_id = ?,
            year_id = ?,
            semester_id = ?
        WHERE student_id = ?";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {

                $error_message = "Update query preparation failed.";

            } else {

                $stmt->bind_param(
                        "ssssssssssiiiiis",
                        $first_name,
                        $middle_name,
                        $last_name,
                        $father_name,
                        $mother_name,
                        $phone,
                        $email,
                        $date_of_birth,
                        $gender,
                        $address,
                        $department_id,
                        $session_id,
                        $batch_id,
                        $year_id,
                        $semester_id,
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

<label>Phone:</label>
<input
    type="text"
    name="phone"
    value="<?php echo htmlspecialchars($student['phone']); ?>"
    required
>

<br><br>

<label>Email:</label>
<input
    type="email"
    name="email"
    value="<?php echo htmlspecialchars($student['email']); ?>"
>

<br><br>

<label>Date of Birth:</label>
<input
    type="date"
    name="date_of_birth"
    value="<?php echo htmlspecialchars($student['date_of_birth']); ?>"
>

<br><br>

<label>Gender:</label>

<label>
    <input
        type="radio"
        name="gender"
        value="Male"
        <?php echo ($student['gender'] === 'Male') ? 'checked' : ''; ?>
    >
    Male
</label>

<label>
    <input
        type="radio"
        name="gender"
        value="Female"
        <?php echo ($student['gender'] === 'Female') ? 'checked' : ''; ?>
    >
    Female
</label>

<label>
    <input
        type="radio"
        name="gender"
        value="Other"
        <?php echo ($student['gender'] === 'Other') ? 'checked' : ''; ?>
    >
    Other
</label>

<br><br>

<label>Address:</label>
<textarea
    name="address"
    rows="4"
    cols="40"
><?php echo htmlspecialchars($student['address']); ?></textarea>

<br><br>

<label>Department:</label>
<select name="department_id" required>
    <option value="">Select Department</option>

    <?php
    $department_result = $conn->query(
        "SELECT department_id, department_name FROM departments ORDER BY department_id"
    );

    while ($department = $department_result->fetch_assoc()):
    ?>
        <option
            value="<?php echo $department['department_id']; ?>"
            <?php echo ($student['department_id'] == $department['department_id']) ? 'selected' : ''; ?>
        >
            <?php echo htmlspecialchars($department['department_name']); ?>
        </option>
    <?php endwhile; ?>
</select>

<br><br>


<label>Session:</label>
<select name="session_id" required>
    <option value="">Select Session</option>

    <?php
    $session_result = $conn->query(
        "SELECT session_id, session_name FROM sessions ORDER BY session_id"
    );

    while ($session = $session_result->fetch_assoc()):
    ?>
        <option
            value="<?php echo $session['session_id']; ?>"
            <?php echo ($student['session_id'] == $session['session_id']) ? 'selected' : ''; ?>
        >
            <?php echo htmlspecialchars($session['session_name']); ?>
        </option>
    <?php endwhile; ?>
</select>

<br><br>


<label>Batch:</label>
<select name="batch_id" required>
    <option value="">Select Batch</option>

    <?php
    $batch_result = $conn->query(
        "SELECT batch_id, batch_name FROM batches ORDER BY batch_id"
    );

    while ($batch = $batch_result->fetch_assoc()):
    ?>
        <option
            value="<?php echo $batch['batch_id']; ?>"
            <?php echo ($student['batch_id'] == $batch['batch_id']) ? 'selected' : ''; ?>
        >
            <?php echo htmlspecialchars($batch['batch_name']); ?>
        </option>
    <?php endwhile; ?>
</select>

<br><br>


<label>Year:</label>
<select name="year_id" required>
    <option value="">Select Year</option>

    <?php
    $year_result = $conn->query(
        "SELECT year_id, year_name FROM years ORDER BY year_id"
    );

    while ($year = $year_result->fetch_assoc()):
    ?>
        <option
            value="<?php echo $year['year_id']; ?>"
            <?php echo ($student['year_id'] == $year['year_id']) ? 'selected' : ''; ?>
        >
            <?php echo htmlspecialchars($year['year_name']); ?>
        </option>
    <?php endwhile; ?>
</select>

<br><br>


<label>Semester:</label>
<select name="semester_id" required>
    <option value="">Select Semester</option>

    <?php
    $semester_result = $conn->query(
        "SELECT semester_id, semester_name FROM semesters ORDER BY semester_id"
    );

    while ($semester = $semester_result->fetch_assoc()):
    ?>
        <option
            value="<?php echo $semester['semester_id']; ?>"
            <?php echo ($student['semester_id'] == $semester['semester_id']) ? 'selected' : ''; ?>
        >
            <?php echo htmlspecialchars($semester['semester_name']); ?>
        </option>
    <?php endwhile; ?>
</select>

<br><br>

        <button type="submit" name="update_student">
            Update Student
        </button>

    </form>

<?php endif; ?>

</body>
</html>