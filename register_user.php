<?php
session_start();
include 'db.php'; // Include your database connection

// Initialize variables
$error_message = "";
$success_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $check_email_query = "SELECT * FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_email_query);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error_message = "Email already registered.";
        } else {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);

            // Execute the statement
            if ($stmt->execute()) {
                $success_message = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error_message = "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InterHive Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: url('images/home.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: rgba(255, 255, 255, 0.4);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 350px;
            text-align: center;
            backdrop-filter: blur(5px);
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .logo {
            width: 50px;
            height: auto;
        }

        h2 {
            font-weight: 700;
            color: rgb(52, 2, 9);
            margin-bottom: 20px;
        }

        .input-group {
            text-align: left;
            margin-bottom: 15px;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            color: black;
            display: block;
            margin-bottom: 5px;
        }

        .input-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-container input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            padding-left: 35px;
            padding-right: 40px;
        }

        .input-container i {
            position: absolute;
            color: #666;
        }

        .input-container .fa-lock, 
        .input-container .fa-envelope, 
        .input-container .fa-phone, 
        .input-container .fa-user {
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .password-toggle {
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        button {
            background-color: rgb(52, 2, 9);
            color: white;
            border: none;
            padding: 12px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            border-radius: 10px;
        }

        p {
            font-size: 13px;
            font-family: 'Roboto', sans-serif;
        }

        p a {
            color: rgb(52, 2, 9);
            text-decoration: none;
        }

        button:hover {
            background-color: #3b0d12;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="images/logo.png" alt="InterHive Logo" class="logo">
            <h2>InterHive</h2>
        </div>

        <!-- Display Messages Here -->
        <?php if (!empty($error_message)) : ?>
            <div class="alert alert-error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)) : ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <form action="register_user.php" method="POST">
            <div class="input-group">
                <label>Full Name</label>
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" name="name" required>
                </div>
            </div>
            
            <div class="input-group">
                <label>Email</label>
                <div class="input-container">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" required>
                </div>
            </div>
            
            <div class="input-group">
                <label>Phone Number</label>
                <div class="input-container">
                    <i class="fas fa-phone"></i>
                    <input type="tel" name="phone" required>
                </div>
            </div>
            
            <div class="input-group">
                <label>Password</label>
                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password', this)"></i>
                </div>
            </div>

            <div class="input-group">
                <label>Confirm Password</label>
                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password', this)"></i>
                </div>
            </div>

            <button type="submit">Register</button>
            
            <p>Already registered? <a href="login.php">Continue to Login</a></p>
        </form>
    </div>

    <script>
    function togglePassword(fieldId, icon) {
        var input = document.getElementById(fieldId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
    </script>
</body>
</html>
