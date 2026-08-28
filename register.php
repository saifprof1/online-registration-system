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

        <button type="submit">Next</button>

    </form>

</body>
</html>