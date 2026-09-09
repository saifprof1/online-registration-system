<?php

require_once "config/database.php";
mysqli_report(MYSQLI_REPORT_OFF);

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (
        empty($_POST['full_name']) ||
        empty($_POST['father_name']) ||
        empty($_POST['mother_name']) ||
        empty($_POST['phone']) ||
        empty($_POST['student_id']) ||
        empty($_POST['department_id']) ||
        empty($_POST['session_id']) ||
        empty($_POST['semester_id']) ||
        empty($_POST['registration_type'])
    ) {
        $error_message = "Please fill in all required fields.";
    }

    $phone = trim($_POST['phone'] ?? '');

    if (empty($error_message) &&
        !preg_match('/^01[3-9][0-9]{8}$/', $phone)) {
        $error_message = "Please enter a valid Bangladesh mobile number.";
    }

    $email = trim($_POST['email'] ?? '');

    if (empty($error_message) &&
        !empty($email) &&
        !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    }

    $student_id = strtoupper(trim($_POST['student_id'] ?? ''));

    if (empty($error_message) &&
        !preg_match('/^B[0-9]{9}$/', $student_id)) {
        $error_message = "Invalid Student ID. Format Example: B230102013";
    }

    $full_name = trim($_POST['full_name'] ?? '');
    $father_name = trim($_POST['father_name'] ?? '');
    $mother_name = trim($_POST['mother_name'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = trim($_POST['address'] ?? '');

    $department_id = $_POST['department_id'] ?? '';
    $session_id = $_POST['session_id'] ?? '';
    $semester_id = $_POST['semester_id'] ?? '';
    $registration_type = $_POST['registration_type'] ?? '';

    if (empty($error_message)) {

        $conn->begin_transaction();

        $sql = "INSERT INTO students
                (student_id, full_name, father_name, mother_name,
                 date_of_birth, gender, phone, email, address,
                 department_id, session_id, semester_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssssssssiiii",
            $student_id,
            $full_name,
            $father_name,
            $mother_name,
            $date_of_birth,
            $gender,
            $phone,
            $email,
            $address,
            $department_id,
            $session_id,
            $semester_id
        );

        if ($stmt->execute()) {

            $registration_sql = "INSERT INTO registrations
                                 (student_id, registration_type)
                                 VALUES (?, ?)";

            $registration_stmt = $conn->prepare($registration_sql);

            $registration_stmt->bind_param(
                "ss",
                $student_id,
                $registration_type
            );

            if ($registration_stmt->execute()) {

                $conn->commit();

                echo "<h2>Registration successful!</h2>";

            } else {

                $conn->rollback();

                echo "<h2>Registration Failed</h2>";
                echo "<p>Registration could not be completed.</p>";
            }

            $registration_stmt->close();

        } else {

            $conn->rollback();

            if ($stmt->errno == 1062) {

                $error_message = "This Student ID is already registered.";

            } else {

                $error_message = "Student registration could not be completed.";
            }
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Registration</title>
</head>

<body>

    <h1>Online Student Registration</h1>

    <form action="" method="POST">

        <?php if (!empty($error_message)): ?>

    <p style="color: red;">
        <?php echo htmlspecialchars($error_message); ?>
    </p>

          <?php endif; ?>

        <h2>Personal Information</h2>

        <label for="full_name">Full Name:</label><br>
        <input type="text" id="full_name" name="full_name"
       value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
       required>
        <br><br>

        <label for="father_name">Father's Name:</label><br>
        <input type="text" id="father_name" name="father_name"
       value="<?php echo htmlspecialchars($_POST['father_name'] ?? ''); ?>"
       required>
        <br><br>

        <label for="mother_name">Mother's Name:</label><br>
        <input type="text" id="mother_name" name="mother_name"
       value="<?php echo htmlspecialchars($_POST['mother_name'] ?? ''); ?>"
       required>
        <br><br>

        <label for="date_of_birth">Date of Birth:</label><br>
        <input type="date"
       id="date_of_birth"
       name="date_of_birth"
       value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>">
        <br><br>

        <label>Gender:</label><br>

        <input type="radio"
       id="male"
       name="gender"
       value="Male"
       <?php echo (($_POST['gender'] ?? '') == 'Male') ? 'checked' : ''; ?>>
        <label for="male">Male</label>

        <input type="radio"
       id="female"
       name="gender"
       value="Female"
       <?php echo (($_POST['gender'] ?? '') == 'Female') ? 'checked' : ''; ?>>
        <label for="female">Female</label>

        <input type="radio"
       id="other"
       name="gender"
       value="Other"
       <?php echo (($_POST['gender'] ?? '') == 'Other') ? 'checked' : ''; ?>>
        <label for="other">Other</label>

        <br><br>

        <label for="phone">Phone Number:</label><br>
        <input type="text" id="phone" name="phone"
       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
       required>
        <br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email"
       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        <br><br>

        <label for="address">Address:</label><br>
        <textarea id="address" name="address" rows="4" cols="40"><?php
        echo htmlspecialchars($_POST['address'] ?? '');
        ?></textarea> 

        <br><br>

                <h2>Academic Information</h2>

        <label for="student_id">Student ID:</label><br>

    <input type="text"
       id="student_id"
       name="student_id"
       value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>"
       required>

<span id="student_id_status"></span>

<br><br>

        <label for="department_id">Department:</label><br>
        <select id="department_id" name="department_id" required>
    <option value="">-- Select Department --</option>

    <?php
    $department_query = "SELECT department_id, department_name
                         FROM departments
                         ORDER BY department_name";

    $department_result = $conn->query($department_query);

    while ($department = $department_result->fetch_assoc()) {
        echo "<option value='" . $department['department_id'] . "'"
     . (($department['department_id'] == ($_POST['department_id'] ?? '')) ? ' selected' : '')
     . ">"
     . htmlspecialchars($department['department_name'])
     . "</option>";
    }
    ?>

</select>
        <br><br>

        <label for="session_id">Session:</label><br>
        <select id="session_id" name="session_id" required>
    <option value="">-- Select Session --</option>

    <?php
    $session_query = "SELECT session_id, session_name
                      FROM sessions
                      ORDER BY session_name";

    $session_result = $conn->query($session_query);

    while ($session = $session_result->fetch_assoc()) {
        echo "<option value='" . $session['session_id'] . "'"
     . (($session['session_id'] == ($_POST['session_id'] ?? '')) ? ' selected' : '')
     . ">"
     . htmlspecialchars($session['session_name'])
     . "</option>";
    }
    ?>

</select>
        <br><br>

        <label for="semester_id">Semester:</label><br>
        <select id="semester_id" name="semester_id" required>
    <option value="">-- Select Semester --</option>

    <?php
    $semester_query = "SELECT semester_id, semester_name
                       FROM semesters
                       ORDER BY semester_id";

    $semester_result = $conn->query($semester_query);

    while ($semester = $semester_result->fetch_assoc()) {
        echo "<option value='" . $semester['semester_id'] . "'"
     . (($semester['semester_id'] == ($_POST['semester_id'] ?? '')) ? ' selected' : '')
     . ">"
     . htmlspecialchars($semester['semester_name'])
     . "</option>";
    }
    ?>

</select>
        <br><br>

        <h2>Registration Information</h2>

        <label for="registration_type">Registration Type:</label><br>
        <select id="registration_type" name="registration_type" required>
    <option value="">-- Select Registration Type --</option>

    <?php
    $registration_types = [
        "General",
        "Event",
        "Workshop"
    ];

    foreach ($registration_types as $type) {
        echo "<option value='" . htmlspecialchars($type) . "'"
     . (($type == ($_POST['registration_type'] ?? '')) ? ' selected' : '')
     . ">"
     . htmlspecialchars($type)
     . "</option>";
    }
    ?>

</select>
        <br><br>

        <button type="submit">Next</button>

    </form>

    <script>
document.getElementById("student_id").addEventListener("blur", function () {

    const studentId = this.value.trim();
    const status = document.getElementById("student_id_status");

    if (studentId === "") {
        status.innerHTML = "";
        return;
    }

    fetch("check_student.php?student_id=" + encodeURIComponent(studentId))
        .then(response => response.text())
        .then(data => {

            if (data === "exists") {
                status.innerHTML = " ⚠️ Student ID Already Registered";
            } 

        });

});
</script>

</body>
</html>