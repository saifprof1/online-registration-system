<?php

require_once "config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();

    $full_name = $_POST['full_name'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $student_id = $_POST['student_id'];
    $department_id = $_POST['department_id'];
    $session_id = $_POST['session_id'];
    $semester_id = $_POST['semester_id'];
    $registration_type = $_POST['registration_type'] ?? '';

if (empty($registration_type)) {
    die("Please select a registration type.");
}

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
        echo "Registration Error: " . $registration_stmt->error;
    }

    $registration_stmt->close();

} else {

    if ($stmt->errno == 1062) {
        echo "<h2>Registration Failed</h2>";
        echo "<p>This Student ID is already registered.</p>";
    } else {
        echo "Student Error: " . $stmt->error;
    }
}

    $stmt->close();
    $conn->close();
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

        <h2>Personal Information</h2>

        <label for="full_name">Full Name:</label><br>
        <input type="text" id="full_name" name="full_name" required>
        <br><br>

        <label for="father_name">Father's Name:</label><br>
        <input type="text" id="father_name" name="father_name" required>
        <br><br>

        <label for="mother_name">Mother's Name:</label><br>
        <input type="text" id="mother_name" name="mother_name" required>
        <br><br>

        <label for="date_of_birth">Date of Birth:</label><br>
        <input type="date" id="date_of_birth" name="date_of_birth">
        <br><br>

        <label>Gender:</label><br>

        <input type="radio" id="male" name="gender" value="Male">
        <label for="male">Male</label>

        <input type="radio" id="female" name="gender" value="Female">
        <label for="female">Female</label>

        <input type="radio" id="other" name="gender" value="Other">
        <label for="other">Other</label>

        <br><br>

        <label for="phone">Phone Number:</label><br>
        <input type="text" id="phone" name="phone" required>
        <br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email">
        <br><br>

        <label for="address">Address:</label><br>
        <textarea id="address" name="address" rows="4" cols="40"></textarea>

        <br><br>

                <h2>Academic Information</h2>

        <label for="student_id">Student ID:</label><br>
        <input type="text" id="student_id" name="student_id" required>
        <br><br>

        <label for="department_id">Department:</label><br>
        <select id="department_id" name="department_id" required>
            <option value="">-- Select Department --</option>
            <option value="1">ICT</option>
            <option value="2">CSE</option>
            <option value="3">DBA</option>
        </select>
        <br><br>

        <label for="session_id">Session:</label><br>
        <select id="session_id" name="session_id" required>
            <option value="">-- Select Session --</option>
            <option value="1">2021-22</option>
            <option value="2">2022-23</option>
            <option value="3">2023-24</option>
            <option value="4">2024-25</option>
            <option value="5">2025-26</option>
            <option value="6">2026-27</option>
        </select>
        <br><br>

        <label for="semester_id">Semester:</label><br>
        <select id="semester_id" name="semester_id" required>
            <option value="">-- Select Semester --</option>
            <option value="1">1st Semester</option>
            <option value="2">2nd Semester</option>
            <option value="3">3rd Semester</option>
            <option value="4">4th Semester</option>
            <option value="5">5th Semester</option>
            <option value="6">6th Semester</option>
            <option value="7">7th Semester</option>
            <option value="8">8th Semester</option>
        </select>
        <br><br>

        <h2>Registration Information</h2>

        <label for="registration_type">Registration Type:</label><br>
        <select id="registration_type" name="registration_type" required>
            <option value="">-- Select Type --</option>
            <option value="General">General</option>
            <option value="Event">Event</option>
            <option value="Workshop">Workshop</option>
        </select>
        <br><br>

        <button type="submit">Next</button>

    </form>

</body>
</html>