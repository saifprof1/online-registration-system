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

    <title>Admin Backend - Student Registration System</title>

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
            width: 90%;
            max-width: 1100px;
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

        .search-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-form input {
            flex: 1;
            min-width: 250px;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        button {
            padding: 11px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
        }

        .search-button {
            background: #1f4e78;
            color: white;
        }

        .view-button {
            background: #198754;
            color: white;
        }

        .update-button {
            background: #f0ad4e;
            color: white;
        }

        .delete-button {
            background: #dc3545;
            color: white;
        }

        button:hover {
            opacity: 0.9;
        }

        .student-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 20px;
        }

        .info-box {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
        }

        .actions {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }

        .message-error {
            color: #dc3545;
            background: #f8d7da;
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
        }

        @media (max-width: 700px) {

            .student-info {
                grid-template-columns: 1fr;
            }

            .search-form {
                flex-direction: column;
            }

            .search-form input {
                width: 100%;
            }

            .actions {
                flex-direction: column;
            }

        }

    </style>

</head>

<body>

    <div class="container">

        <!-- Header -->

        <div class="header">

            <h1>Admin Backend</h1>

            <p>Online Student Registration System</p>

        </div>


        <!-- Search Card -->

        <div class="card">

            <h2>Search Student</h2>

            <form method="GET" class="search-form">

                <input
                    type="text"
                    name="student_id"
                    placeholder="Enter Student ID"
                    value="<?php echo htmlspecialchars($_GET['student_id'] ?? ''); ?>"
                    required
                >

                <button
                    type="submit"
                    class="search-button"
                >
                    Search
                </button>

            </form>


            <!-- View All Students -->

            <br>

            <a href="view_students.php">

                <button
                    type="button"
                    class="view-button"
                >
                    View All Students
                </button>

            </a>

        </div>


        <!-- Error Message -->

        <?php if (!empty($error_message)): ?>

            <div class="message-error">

                <?php
                echo htmlspecialchars($error_message);
                ?>

            </div>

        <?php endif; ?>


        <!-- Student Information -->

        <?php if ($student !== null): ?>

            <div class="card">

                <h2>Student Information</h2>


                <div class="student-info">

                    <div class="info-box">

                        <strong>Student ID</strong><br>

                        <?php
                        echo htmlspecialchars(
                            $student['student_id']
                        );
                        ?>

                    </div>


                    <div class="info-box">

                        <strong>Name</strong><br>

                        <?php

                        echo htmlspecialchars(
                            trim(
                                $student['first_name'] . " " .
                                $student['middle_name'] . " " .
                                $student['last_name']
                            )
                        );

                        ?>

                    </div>


                    <div class="info-box">

                        <strong>Father Name</strong><br>

                        <?php
                        echo htmlspecialchars(
                            $student['father_name']
                        );
                        ?>

                    </div>


                    <div class="info-box">

                        <strong>Mother Name</strong><br>

                        <?php
                        echo htmlspecialchars(
                            $student['mother_name']
                        );
                        ?>

                    </div>


                    <div class="info-box">

                        <strong>Phone</strong><br>

                        <?php
                        echo htmlspecialchars(
                            $student['phone']
                        );
                        ?>

                    </div>


                    <div class="info-box">

                        <strong>Email</strong><br>

                        <?php
                        echo htmlspecialchars(
                            $student['email']
                        );
                        ?>

                    </div>


                    <div class="info-box">

                        <strong>Gender</strong><br>

                        <?php
                        echo htmlspecialchars(
                            $student['gender']
                        );
                        ?>

                    </div>

                </div>


                <!-- Action Buttons -->

                <div class="actions">

                    <a
                        href="update_student.php?student_id=<?php
                            echo urlencode(
                                $student['student_id']
                            );
                        ?>"
                    >

                        <button
                            type="button"
                            class="update-button"
                        >
                            Update
                        </button>

                    </a>


                    <a
                        href="delete_student.php?student_id=<?php
                            echo urlencode(
                                $student['student_id']
                            );
                        ?>"
                    >

                        <button
                            type="button"
                            class="delete-button"
                        >
                            Delete
                        </button>

                    </a>

                </div>

            </div>

        <?php endif; ?>

    </div>

</body>

</html>