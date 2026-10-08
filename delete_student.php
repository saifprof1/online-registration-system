<?php

require_once "config/database.php";

$student = null;
$error_message = "";
$success_message = "";


// =====================================================
// POST REQUEST
// =====================================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id =
        strtoupper(
            trim(
                $_POST['student_id'] ?? ''
            )
        );


    // =================================================
    // DELETE STUDENT
    // =================================================

    if (isset($_POST['delete_student'])) {

        if (empty($student_id)) {

            $error_message =
                "Student ID is required.";

        } else {

            // Get image path before deleting student

            $select_sql = "
                SELECT image_path
                FROM students
                WHERE student_id = ?
            ";

            $select_stmt =
                $conn->prepare($select_sql);

            if (!$select_stmt) {

                $error_message =
                    "Query preparation failed.";

            } else {

                $select_stmt->bind_param(
                    "s",
                    $student_id
                );

                $select_stmt->execute();

                $result =
                    $select_stmt->get_result();


                if ($result->num_rows === 1) {

                    $student_data =
                        $result->fetch_assoc();

                    $image_path =
                        $student_data['image_path'];


                    // =================================
                    // Delete club memberships
                    // =================================

                    $delete_club_sql = "
                        DELETE FROM student_clubs
                        WHERE student_id = ?
                    ";

                    $delete_club_stmt =
                        $conn->prepare(
                            $delete_club_sql
                        );

                    if ($delete_club_stmt) {

                        $delete_club_stmt->bind_param(
                            "s",
                            $student_id
                        );

                        $delete_club_stmt->execute();

                        $delete_club_stmt->close();
                    }


                    // =================================
                    // Delete student
                    // =================================

                    $delete_student_sql = "
                        DELETE FROM students
                        WHERE student_id = ?
                    ";

                    $delete_student_stmt =
                        $conn->prepare(
                            $delete_student_sql
                        );

                    if (!$delete_student_stmt) {

                        $error_message =
                            "Delete query preparation failed.";

                    } else {

                        $delete_student_stmt->bind_param(
                            "s",
                            $student_id
                        );


                        if (
                            $delete_student_stmt->execute()
                        ) {

                            // =============================
                            // Delete image file
                            // =============================

                            if (!empty($image_path)) {

                                $image_file =
                                    __DIR__ .
                                    "/" .
                                    $image_path;

                                if (
                                    file_exists($image_file) &&
                                    is_file($image_file)
                                ) {

                                    unlink($image_file);
                                }
                            }


                            $success_message =
                                "Student deleted successfully.";

                            $student = null;

                        } else {

                            $error_message =
                                "Student could not be deleted.";
                        }


                        $delete_student_stmt->close();
                    }

                } else {

                    $error_message =
                        "Student not found.";
                }


                $select_stmt->close();
            }
        }


    // =================================================
    // SEARCH STUDENT FROM POST
    // =================================================

    } else {

        if (empty($student_id)) {

            $error_message =
                "Please enter Student ID.";

        } else {

            $sql = "
                SELECT *
                FROM students
                WHERE student_id = ?
            ";

            $stmt =
                $conn->prepare($sql);

            if (!$stmt) {

                $error_message =
                    "Query preparation failed.";

            } else {

                $stmt->bind_param(
                    "s",
                    $student_id
                );

                $stmt->execute();

                $result =
                    $stmt->get_result();


                if ($result->num_rows === 1) {

                    $student =
                        $result->fetch_assoc();

                } else {

                    $error_message =
                        "Student not found.";
                }


                $stmt->close();
            }
        }
    }


// =====================================================
// GET REQUEST FROM ADMIN PAGE
// =====================================================

} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {

    $student_id =
        strtoupper(
            trim(
                $_GET['student_id'] ?? ''
            )
        );


    if (!empty($student_id)) {

        $sql = "
            SELECT *
            FROM students
            WHERE student_id = ?
        ";

        $stmt =
            $conn->prepare($sql);

        if (!$stmt) {

            $error_message =
                "Query preparation failed.";

        } else {

            $stmt->bind_param(
                "s",
                $student_id
            );

            $stmt->execute();

            $result =
                $stmt->get_result();


            if ($result->num_rows === 1) {

                $student =
                    $result->fetch_assoc();

            } else {

                $error_message =
                    "Student not found.";
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
    <title>Delete Student</title>
</head>

<body>

    <h2>Delete Student</h2>

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


    <!-- Search Student -->

    <form method="POST">

        <label for="student_id">
            Student ID:
        </label>

        <input
            type="text"
            name="student_id"
            id="student_id"
            placeholder="Enter Student ID"
            required
        >

        <button type="submit">
            Search Student
        </button>

    </form>


    <?php if ($student !== null): ?>

        <hr>

        <h3>Student Information</h3>

        <p>
            <strong>Student ID:</strong>
            <?php echo htmlspecialchars($student['student_id']); ?>
        </p>

        <p>
            <strong>Name:</strong>
            <?php
            echo htmlspecialchars(
                $student['first_name'] . " " .
                $student['middle_name'] . " " .
                $student['last_name']
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
            <strong>Date of Birth:</strong>
            <?php echo htmlspecialchars($student['date_of_birth']); ?>
        </p>

        <p>
            <strong>Gender:</strong>
            <?php echo htmlspecialchars($student['gender']); ?>
        </p>

        <p>
            <strong>Address:</strong>
            <?php echo htmlspecialchars($student['address']); ?>
        </p>

        <br>

        <form method="POST"
      onsubmit="return confirm('Are you sure you want to delete this student?');">

    <input
        type="hidden"
        name="student_id"
        value="<?php echo htmlspecialchars($student['student_id']); ?>"
    >

    <button
        type="submit"
        name="delete_student"
    >
        Delete Student
    </button>

</form>

    <?php endif; ?>

</body>

</html>