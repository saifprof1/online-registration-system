
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSTU Student Registration</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .home-card {
            background: white;
            width: 100%;
            max-width: 750px;
            padding: 40px 30px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        .university-logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        h1 {
            color: #1f4e78;
            font-size: 32px;
            line-height: 1.4;
            margin: 0 0 15px;
        }

        p {
            color: #666;
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .navigation {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .navigation a {
            display: inline-block;
            padding: 13px 22px;
            background: #1f4e78;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.2s ease;
        }

        .navigation a:hover {
            background: #163a5a;
        }

        @media (max-width: 500px) {
            .home-card {
                padding: 30px 20px;
            }

            .university-logo {
                width: 95px;
                height: 95px;
            }

            h1 {
                font-size: 25px;
            }

            .navigation {
                flex-direction: column;
            }

            .navigation a {
                width: 100%;
            }
        }

        .university-name {
    color: #555;
    font-size: 16px;
    font-weight: bold;
    margin: 0 0 8px;
}
    </style>
</head>

<body>
    <main class="home-card">
        <img
            src="assets/images/CSTU-LOGO.svg"
            alt="Chandpur Science and Technology University Logo"
            class="university-logo"
        >

        <p class="university-name">
    Chandpur Science and Technology University
</p>

        <h1>CSTU Student Registration</h1>

        <p>
            Welcome to the CSTU Student Registration System.
            Register as a student or access the administration panel.
        </p>

        <div class="navigation">
            <a href="register.php">Student Registration</a>
            <a href="admin.php">Admin Panel</a>
        </div>
    </main>
</body>
</html>