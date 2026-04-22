<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';
$db = 'internship_platform';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Check database connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);

    // Step 1: Check if user exists
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Step 2: Generate a new random password (6 characters)
        $new_password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);  // Hash the password

        // Step 3: Update password in the database
        $update_sql = "UPDATE users SET password=? WHERE email=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $hashed_password, $email);

        if ($update_stmt->execute()) {
            // Password updated successfully
            echo "<script>
                    alert('Your new password is: $new_password. Please change it after logging in.');
                    window.location.href='login.php';
                  </script>";
        } else {
            // Error updating password
            die("Error updating password: " . $update_stmt->error);
        }

        $update_stmt->close();
    } else {
        // No user found with the provided email
        echo "<script>alert('No user found with that email.'); window.location.href='forgot_password.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password ?</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Lato';
            background-image: url('images/home.jpg');
            background-size: cover;
            background-position: center;
            text-align: center;
            padding: 80px;
            color: white;
        }
        .container {
            background: rgba(248, 241, 241, 0.3);
            padding: 20px;
            border-radius: 15px;
            width: 350px;
            margin: auto;
            backdrop-filter: blur(4px);
            font-family: 'Lato';
        }
        h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        input[type="email"], input[type="password"], button {
        width: 95%; /* Ensures both fields have the same width */
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 10px;
        }
        button {
            background: rgb(52, 2, 9);
            color: white;
            font-size: 16px;
            cursor: pointer;
            font-family: 'Lato';
        }
        button:hover {
            background: rgb(35, 1, 6);
            border-radius: 25px;
        }
        .login-link {
            color: #f1f1f1;
            text-decoration: none;
            font-size: 16px;
        }
        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Forgot Password?</h2>
    <p>Enter your email to reset your password.</p>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Reset Password</button>
    </form>
    <p>Remember your password? <a href="login.php" class="login-link">Login </a></p>
</div>

</body>
</html>
