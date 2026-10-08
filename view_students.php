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

$sql = "SELECT * FROM students";

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
                <th>Image</th>
                <th>Address</th>
                <th>Department</th>
                <th>Session</th>
                <th>Batch</th>
                <th>Year</th>
                <th>Semester</th>
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
            <td><?php echo htmlspecialchars($row['image_path']); ?></td>
            <td><?php echo htmlspecialchars($row['address']); ?></td>
            <td><?php echo htmlspecialchars($row['department_id']); ?></td>
            <td><?php echo htmlspecialchars($row['session_id']); ?></td>
            <td><?php echo htmlspecialchars($row['batch_id']); ?></td>
            <td><?php echo htmlspecialchars($row['year_id']); ?></td>
            <td><?php echo htmlspecialchars($row['semester_id']); ?></td>
            <td>
    <a
        href="update_student.php?student_id=<?php echo urlencode($student['student_id']); ?>"
    >
        Update
    </a>

    <a
        href="delete_student.php?student_id=<?php echo urlencode($student['student_id']); ?>"
    >
        Delete
    </a>
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