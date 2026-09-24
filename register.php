<?php

session_start();

require_once "config/database.php";
mysqli_report(MYSQLI_REPORT_OFF);

$error_message = "";

if (empty($_SESSION['captcha_code'])) {
    $_SESSION['captcha_code'] = substr(
        str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"),
        0,
        6
    );
}

$captcha_code = $_SESSION['captcha_code'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (
        empty($_POST['first_name']) ||
        empty($_POST['last_name']) ||
        empty($_POST['father_name']) ||
        empty($_POST['mother_name']) ||
        empty($_POST['phone']) ||
        empty($_POST['student_id']) ||
        empty($_POST['department_id']) ||
        empty($_POST['session_id']) ||
        empty($_POST['batch_id']) ||
        empty($_POST['year_id']) ||
        empty($_POST['semester_id']) ||
        empty($_POST['registration_type'])
    ) {
        $error_message = "Please fill in all required fields.";
    }

    $entered_captcha = trim($_POST['captcha'] ?? '');

    if (empty($error_message) && $entered_captcha === '') {
        $error_message = "Please enter the captcha code.";
    }

    if (
        empty($error_message) &&
        $entered_captcha !== $_SESSION['captcha_code']
    ) {
        $error_message = "Invalid captcha code.";
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

    $image_path = '';

if (!isset($_FILES['student_image'])) {

    $error_message = "Please upload a student image.";

} elseif ($_FILES['student_image']['error'] !== UPLOAD_ERR_OK) {

    switch ($_FILES['student_image']['error']) {

        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $error_message = "Image size is too large.";
            break;

        case UPLOAD_ERR_NO_FILE:
            $error_message = "Please upload a student image.";
            break;

        default:
            $error_message = "Image upload failed.";
            break;
    }

} else {

    $image_tmp = $_FILES['student_image']['tmp_name'];
    $image_size = $_FILES['student_image']['size'];

    if ($image_size > 2 * 1024 * 1024) {

        $error_message = "Image size must be less than 2 MB.";

    } else {

        $allowed_types = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        $image_type = mime_content_type($image_tmp);
        $image_info = getimagesize($image_tmp);

if ($image_info === false) {

    $error_message = "The uploaded file is not a valid image.";

}

        if (!in_array($image_type, $allowed_types, true)) {

            $error_message = "Only JPG, PNG, and WebP images are allowed.";
        }
    }
}

    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $father_name = trim($_POST['father_name'] ?? '');
    $mother_name = trim($_POST['mother_name'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $department_id = $_POST['department_id'] ?? '';
    $session_id = $_POST['session_id'] ?? '';
    $batch_id = $_POST['batch_id'] ?? '';
    $year_id = $_POST['year_id'] ?? '';
    $semester_id = $_POST['semester_id'] ?? '';
    $club_ids = $_POST['club_ids'] ?? [];
    $registration_type = $_POST['registration_type'] ?? '';

    if (empty($error_message)) {
    $extension_map = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    $image_extension = $extension_map[$image_type];

    $image_filename = $student_id . '_' . time() . '.' . $image_extension;

    $image_path = "uploads/students/" . $image_filename;

    $upload_path = __DIR__ . "/" . $image_path;

    if (!move_uploaded_file(
        $_FILES['student_image']['tmp_name'],
        $upload_path
    )) {

        $error_message = "Failed to upload student image.";

    } else {

        $conn->begin_transaction();

        try {

            $sql = "INSERT INTO students
                    (student_id, first_name, middle_name, last_name,
                     father_name, mother_name, date_of_birth, gender,
                     phone, email, image_path, address,
                     department_id, session_id, batch_id, year_id, semester_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Student query preparation failed.");
            }

            $stmt->bind_param(
                "ssssssssssssiiiii",
                $student_id,
                $first_name,
                $middle_name,
                $last_name,
                $father_name,
                $mother_name,
                $date_of_birth,
                $gender,
                $phone,
                $email,
                $image_path,
                $address,
                $department_id,
                $session_id,
                $batch_id,
                $year_id,
                $semester_id
            );

            if (!$stmt->execute()) {

                if ($stmt->errno == 1062) {
                    throw new Exception("This Student ID is already registered.");
                }

                throw new Exception("Student registration could not be completed.");
            }

            $registration_sql = "INSERT INTO registrations
                                 (student_id, registration_type)
                                 VALUES (?, ?)";

            $registration_stmt = $conn->prepare($registration_sql);

            if (!$registration_stmt) {
                throw new Exception("Registration query preparation failed.");
            }

            $registration_stmt->bind_param(
                "ss",
                $student_id,
                $registration_type
            );

            if (!$registration_stmt->execute()) {
                throw new Exception("Registration could not be completed.");
            }

            if (!empty($club_ids)) {

                $club_stmt = $conn->prepare(
                    "INSERT INTO student_clubs (student_id, club_id)
                     VALUES (?, ?)"
                );

                if (!$club_stmt) {
                    throw new Exception("Club query preparation failed.");
                }

                $club_stmt->bind_param(
                    "si",
                    $student_id,
                    $club_id
                );

                foreach ($club_ids as $club_id) {

                    $club_id = (int) $club_id;

                    if (!$club_stmt->execute()) {
                        throw new Exception("Club membership insertion failed.");
                    }
                }

                $club_stmt->close();
            }

            $conn->commit();

            unset($_SESSION['captcha_code']);

            echo '<div class="success-box">';

            echo "<h2>Registration Successful!</h2>";

            echo "<p><strong>Student ID:</strong> "
                 . htmlspecialchars($student_id)
                 . "</p>";

            echo "<p><strong>Registration Type:</strong> "
                 . htmlspecialchars($registration_type)
                 . "</p>";

            echo '<p><a href="register.php">Back to Registration</a></p>';

            echo '</div>';

            $registration_stmt->close();
            $stmt->close();

        } catch (Exception $e) {

            $conn->rollback();

            if (file_exists($upload_path)) {
                unlink($upload_path);
            }

            $error_message = $e->getMessage();

            if (isset($registration_stmt) && $registration_stmt) {
                $registration_stmt->close();
            }

            if (isset($stmt) && $stmt) {
                $stmt->close();
            }
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
    <style>

.success-box {
    background-color: #e8f5e9;
    border: 1px solid #4caf50;
    padding: 20px;
    margin: 20px 0;
    border-radius: 8px;
}

.success-box h2 {
    color: #2e7d32;
    margin-top: 0;
}

.success-box p {
    margin: 8px 0;
}

.success-box a {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 15px;
    text-decoration: none;
    background-color: #2e7d32;
    color: white;
    border-radius: 5px;
}

</style>

    <title>Student Registration</title>
</head>

<body>

    <h1>Online Student Registration</h1>

    <form method="POST" enctype="multipart/form-data">

        <?php if (!empty($error_message)): ?>

    <p style="color: red;">
        <?php echo htmlspecialchars($error_message); ?>
    </p>

          <?php endif; ?>

        <h2>Personal Information</h2>

        <label for="first_name">First Name:</label>
<input type="text"
       id="first_name"
       name="first_name"
       value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
       required>

<br><br>

<label for="middle_name">Middle Name:</label>
<input type="text"
       id="middle_name"
       name="middle_name"
       value="<?php echo htmlspecialchars($_POST['middle_name'] ?? ''); ?>">

<br><br>

<label for="last_name">Last Name:</label>
<input type="text"
       id="last_name"
       name="last_name"
       value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
       required>
        <br><br>

        <label for="father_name">Father's Name:</label>
        <input type="text" id="father_name" name="father_name"
       value="<?php echo htmlspecialchars($_POST['father_name'] ?? ''); ?>"
       required>
        <br><br>

        <label for="mother_name">Mother's Name:</label>
        <input type="text" id="mother_name" name="mother_name"
       value="<?php echo htmlspecialchars($_POST['mother_name'] ?? ''); ?>"
       required>
        <br><br>

        <label for="date_of_birth">Date of Birth:</label>
        <input type="date"
       id="date_of_birth"
       name="date_of_birth"
       value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>">
        <br><br>

        <label>Gender:</label>

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

        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone"
       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
       required>
        <br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email"
       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        <br><br>

        <label for="address">Address:</label>
        <textarea id="address" name="address" rows="4" cols="40"><?php
        echo htmlspecialchars($_POST['address'] ?? '');
        ?></textarea> 

        <br><br>

                <h2>Academic Information</h2>

        <label for="student_id">Student ID:</label>

    <input type="text"
       id="student_id"
       name="student_id"
       value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>"
       required>

<span id="student_id_status"></span>

<br><br>

        <label for="department_id">Department:</label>
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

        <label for="session_id">Session:</label>
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

        <label for="batch_id">Batch:</label>
<select id="batch_id" name="batch_id" required>
    <option value="">-- Select Batch --</option>

    <?php
    $batch_query = "SELECT batch_id, batch_name FROM batches ORDER BY batch_id";
    $batch_result = $conn->query($batch_query);

    while ($batch = $batch_result->fetch_assoc()) {
        echo "<option value='" . $batch['batch_id'] . "'"
            . (($batch['batch_id'] == ($_POST['batch_id'] ?? '')) ? ' selected' : '')
            . ">"
            . htmlspecialchars($batch['batch_name'])
            . "</option>";
    }
    ?>
</select>

<br><br>

<label for="year_id">Year:</label>
<select id="year_id" name="year_id" required>
    <option value="">-- Select Year --</option>

    <?php
    $year_query = "SELECT year_id, year_name FROM years ORDER BY year_id";
    $year_result = $conn->query($year_query);

    while ($year = $year_result->fetch_assoc()) {
        echo "<option value='" . $year['year_id'] . "'"
            . (($year['year_id'] == ($_POST['year_id'] ?? '')) ? ' selected' : '')
            . ">"
            . htmlspecialchars($year['year_name'])
            . "</option>";
    }
    ?>
</select>

<br><br>

        <label for="semester_id">Semester:</label>
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

        <label>Club Membership:</label>
<br>

<?php
$club_query = "SELECT club_id, club_name FROM clubs ORDER BY club_id";
$club_result = $conn->query($club_query);

while ($club = $club_result->fetch_assoc()) {

    $checked = '';

    if (
        isset($_POST['club_ids']) &&
        in_array($club['club_id'], $_POST['club_ids'])
    ) {
        $checked = 'checked';
    }

    echo '<label>';
    echo '<input type="checkbox" '
        . 'name="club_ids[]" '
        . 'value="' . $club['club_id'] . '" '
        . $checked . '>';
    echo ' ' . htmlspecialchars($club['club_name']);
    echo '</label><br>';
}
?>

<br><br>

<div class="form-group">
    <label>Captcha:</label>

    <div style="
        width: 220px;
        height: 60px;
        background-color: #eeeeee;
        border: 2px solid #000000;
        border-radius: 8px;
        margin: 10px 0;
        display: flex;
        align-items: center;
        justify-content: center;
    ">
        <span style="
    color: #222222;
    font-size: 30px;
    font-weight: bold;
    letter-spacing: 8px;
    font-family: 'Courier New', monospace;
    font-style: italic;
    transform: rotate(-3deg);
    display: inline-block;
    user-select: none;
">
    <?php echo htmlspecialchars($captcha_code); ?>
</span>
    </div>

    <input
        type="text"
        id="captcha"
        name="captcha"
        placeholder="Enter Captcha code"
        autocomplete="off"
        required
    >
</div>

<br><br>

<label for="student_image">Student Image:</label>
<input type="file"
       id="student_image"
       name="student_image"
       accept="image/jpeg,image/png,image/webp"
       required>

<br><br>

        <h2>Registration Information</h2>

        <label for="registration_type">Registration Type:</label>
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
    <br><br>

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
            status.style.color = "red";
        } 
        else if (data === "available") {
            status.innerHTML = " ✅ Student ID Available";
            status.style.color = "green";
        }

    });

});
</script>

</body>
</html>