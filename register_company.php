<?php
require 'db.php';

function alert($message) {
    echo "<script>alert('$message');</script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        alert('Invalid email format');
    } elseif ($password !== $confirm_password) {
        alert('Passwords do not match');
    } else {
        // Handle file upload
        $file = $_FILES['company_file'];
        $file_name = basename($file['name']);
        $target = "cm_uploads/" . $file_name;
        $file_type = strtolower(pathinfo($target, PATHINFO_EXTENSION));

        // Check file type
        $allowed_types = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        if (!in_array($file_type, $allowed_types)) {
            alert('Only PDF, DOC, DOCX, JPG, JPEG, and PNG files are allowed');
        } elseif ($file['size'] > 2 * 1024 * 1024) { // Limit file size to 2MB
            alert('File size must be less than 2MB');
        } else {
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert company data with is_approved set to 0
                $query = "INSERT INTO companies (company_name, email, password, file_upload, is_approved) VALUES (?, ?, ?, ?, 0)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssss", $name, $email, $hashed_password, $file_name);

                if ($stmt->execute()) {
                    alert('Company registered successfully!'); 
                    echo "<script>window.location='login.php';</script>";
                } else {
                    alert('Error: ' . htmlspecialchars($stmt->error));
                }

                $stmt->close();
            } else {
                alert('Failed to upload file');
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InterHive Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/home.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            width: 360px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            text-align: center;
            backdrop-filter: blur(8px);
        }
        .form-group {
            margin-bottom: 12px;
            text-align: left;
        }
        .form-group label {
            font-size: 13px;
            font-weight: bold;
        }
        .input-container {
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 6px;
            padding: 6px;
            border: 1px solid #ccc;
            position: relative;
        }
        .input-container input {
            width: 100%;
            border: none;
            outline: none;
            font-size: 14px;
            background: transparent;
            padding: 5px;
        }
        .eye-icon {
            position: absolute;
            right: 10px;
            cursor: pointer;
            color: rgb(52, 2, 9);
        }
        button {
            background-color: rgb(52, 2, 9);
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            padding: 10px;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: rgb(80, 3, 12);
        }
        a {
            color: rgb(52, 2, 9);
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .logo {
            width: 40px;
            height: auto;
        }
        .main-heading {
            font-size: 22px;
            margin: 0;
        }
        p {
            font-size: 13px;
            font-family: 'Roboto', sans-serif;
        }

        p a {
            color: rgb(52, 2, 9);
            text-decoration: none;
            font-weight: normal;
        }
    </style>
</head>
<body>
<div class="form-container">
    <div class="header">
        <img src="images/logo.png" alt="InterHive Logo" class="logo">
        <h2 class="main-heading">InterHive</h2>
    </div>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Company Name</label>
            <div class="input-container">
                <i class="fas fa-building"></i>
                <input type="text" id="name" name="name" required>
            </div>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <div class="input-container">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" required>
            </div>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-container">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" required>
                <i class="fas fa-eye eye-icon" onclick="togglePassword('password')"></i>
            </div>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <div class="input-container">
                <i class="fas fa-lock"></i>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <i class="fas fa-eye eye-icon" onclick="togglePassword('confirm_password')"></i>
            </div>
        </div>
        <div class="form-group">
            <label for="company_file">Upload File</label>
            <div class="input-container">
                <i class="fas fa-file-upload"></i>
                <input type="file" id="company_file" name="company_file" required>
            </div>
        </div>
        <button type="submit">Register</button>
    </form>
    <p>Already registered? <a href="login.php">Continue to Login</a></p>
</div>

<script>
    function togglePassword(fieldId) {
        var field = document.getElementById(fieldId);
        var eyeIcon = field.nextElementSibling;
        if (field.type === "password") {
            field.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            field.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }
    }
</script>
</body>
</html>
