<?php

require_once "config/database.php";

function calculateAge($date_of_birth)
{
    if (empty($date_of_birth)) {
        return "";
    }

    $birthDate = new DateTime($date_of_birth);
    $today = new DateTime();

    $age = $today->diff($birthDate)->y;

    return $age;
}

$sql = "SELECT
            students.*,
            departments.department_name,
            sessions.session_name,
            batches.batch_name
        FROM students
        LEFT JOIN departments
            ON students.department_id = departments.department_id
        LEFT JOIN sessions
            ON students.session_id = sessions.session_id
        LEFT JOIN batches
            ON students.batch_id = batches.batch_id";

$result = $conn->query($sql);

if (!$result) {
    die("Failed to retrieve student data.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    color: #333;
}

.container {
    width: 95%;
    max-width: 1400px;
    margin: 40px auto;
}

.header {
    background: #1f4e78;
    color: white;
    padding: 25px;
    border-radius: 10px 10px 0 0;
    text-align: center;
}

.header h1 {
    margin: 0 0 8px;
}

.header p {
    margin: 0;
}

.card {
    background: white;
    padding: 25px;
    margin-top: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1200px;
}

th {
    background: #1f4e78;
    color: white;
    padding: 12px;
    text-align: left;
    white-space: nowrap;
}

td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    vertical-align: middle;
}

tr:hover {
    background: #f8f9fa;
}

.student-image {
    width: 65px;
    height: 65px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #ddd;
    display: block;
}

.no-image {
    color: #888;
}

.action-buttons {
    display: flex;
    gap: 8px;
    align-items: center;
    white-space: nowrap;
}

.update-button,
.delete-button {
    min-width: 75px;
    text-align: center;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
}

.update-button:hover,
.delete-button:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}

.update-button,
.delete-button {
    display: inline-block;
    padding: 7px 12px;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 13px;
}

.update-button {
    background: #f0ad4e;
}

.delete-button {
    background: #dc3545;
}

.update-button:hover,
.delete-button:hover {
    opacity: 0.9;
}

.back-button {
    display: inline-block;
    margin-bottom: 15px;
    padding: 10px 18px;
    background: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 6px;
}

.back-button:hover {
    opacity: 0.9;
}

@media (max-width: 700px) {

    .container {
        width: 98%;
        margin: 15px auto;
    }

    .card {
        padding: 15px;
    }
}
</style>
</head>

<body>

    <h2>Student Information</h2>

    <table border="1" cellpadding="8" cellspacing="0">

        <thead>
            <tr>
                <th>Student ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Father Name</th>
                <th>Mother Name</th>
                <th>Date of Birth</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Address</th>
                <th>Department</th>
                <th>Session</th>
                <th>Batch</th>
                <th>Year</th>
                <th>Semester</th>
                <th>Image</th>
                <th>Action</th>
                
            </tr>
        </thead>

        <tbody>

<?php if ($result->num_rows > 0): ?>

    <?php while ($row = $result->fetch_assoc()): ?>

        <tr>
            <td><?php echo htmlspecialchars($row['student_id']); ?></td>
            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
            <td><?php echo htmlspecialchars($row['middle_name']); ?></td>
            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
            <td><?php echo htmlspecialchars($row['father_name']); ?></td>
            <td><?php echo htmlspecialchars($row['mother_name']); ?></td>
            <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>
            <td><?php echo calculateAge($row['date_of_birth']); ?></td>
            <td><?php echo htmlspecialchars($row['gender']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['address']); ?></td>
            <td><?php echo htmlspecialchars($row['department_name'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($row['session_name'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($row['batch_name'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($row['year_id']); ?></td>
            <td><?php echo htmlspecialchars($row['semester_id']); ?></td>
            <td>
    <?php if (!empty($row['image_path'])): ?>

        <img
            src="<?php echo htmlspecialchars($row['image_path']); ?>"
            alt="Student Image"
            class="student-image"
        >

    <?php else: ?>

        <span class="no-image">
            No image
        </span>

    <?php endif; ?>
</td>
            <td>
    <div class="action-buttons">

        <a
            href="update_student.php?student_id=<?php echo urlencode($row['student_id']); ?>"
            class="update-button"
        >
            Update
        </a>

        <a
            href="delete_student.php?student_id=<?php echo urlencode($row['student_id']); ?>"
            class="delete-button"
        >
            Delete
        </a>

    </div>
</td>
        </tr>

    <?php endwhile; ?>

<?php else: ?>

    <tr>
        <td colspan="18">No student records found.</td>
    </tr>

<?php endif; ?>

</tbody>

    </table>

</body>
</html>