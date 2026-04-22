<?php
session_start();
include 'db.php';

$error_message = ""; // Initialize error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Check for default admin credentials
    if ($email === 'admin@example.com' && $password === 'admin123' && $role === 'admin') {
        $_SESSION['user_id'] = 1; // Assuming the admin ID is 1
        $_SESSION['role'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    }

    // Determine the query based on the role
    if ($role === 'user') {
        $query = "SELECT * FROM users WHERE email = ?";
    } elseif ($role === 'company') {
        $query = "SELECT * FROM companies WHERE email = ?";
    } elseif ($role === 'admin') {
        $query = "SELECT * FROM admin WHERE email = ?";
    } else {
        $error_message = "Invalid role selected.";
    }

    // Use prepared statements
    if (empty($error_message)) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Check if the company is approved
            if ($role === 'company' && $row['is_approved'] == 0) {
                $error_message = "Company registration is not approved.";
            } else {
                if (password_verify($password, $row['password'])) {
                    session_regenerate_id(true); // Regenerate session ID
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['role'] = $role;

                    // Redirect based on role
                    if ($role === 'user') {
                        header("Location: user_dashboard.php");
                    } elseif ($role === 'company') {
                        header("Location: company_dashboard.php");
                    } elseif ($role === 'admin') {
                        header("Location: admin_dashboard.php");
                    }
                    exit();
                } else {
                    $error_message = "Invalid password.";
                }
            }
        } else {
            $error_message = "Invalid login credentials.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InterHive Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Lato';
            background: url('images/home.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: rgba(248, 241, 241, 0.4);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            width: 350px;
            max-width: 80%;
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
            text-align: center;
            color: rgb(52, 2, 9);
            margin-bottom: 20px;
        }
        .form-group {
            position: relative;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }
        label {
            font-size: 16px;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            height: 45px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            font-family: 'Lato';
        }
        .password-container {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            cursor: pointer;
            color: #555;
        }
        .login-button {
            background-color: rgb(52, 2, 9);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .register-links, .forgot-password {
            text-align: center;
            margin-top: 15px;
        }
        .register-links a, .forgot-password a {
            color: rgb(52, 2, 9);
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="header">
        <img src="images/logo.png" alt="InterHive Logo" class="logo">
        <h2>InterHive</h2>
    </div>
    <form method="POST" action="">
        <?php if (!empty($error_message)): ?>
            <div style="color: red; text-align: center; margin-bottom: 15px;">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="email"><i class="fas fa-envelope"></i> Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password"><i class="fas fa-lock"></i> Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <i class="fas fa-eye-slash toggle-password" id="togglePassword" onclick="togglePassword()"></i>
            </div>
        </div>
        <div class="form-group">
            <label for="role"><i class="fas fa-user-tag"></i> Role</label>
            <select name="role" id="role" required>
                <option value="user">User</option>
                <option value="company">Company</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="login-button">Login</button>
        <div class="register-links">Don't have an account? <a href="register_user.php">Sign up as User</a> | <a href="register_company.php">Sign up as Company</a></div>
        <div class="forgot-password"><a href="forgot_password.php">Forgot Password?</a></div>
    </form>
</div>

<script>
    function togglePassword() {
        var passwordInput = document.getElementById("password");
        var toggleIcon = document.getElementById("togglePassword");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        }
    }
</script>

</body>
</html>
