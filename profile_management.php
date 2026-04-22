<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = $_POST['company_name'];
    $email = $_POST['email'];

    // Handle file upload
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['file_upload']['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . time() . "_" . $file_name;

        if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_file)) {
            $sql = "UPDATE companies SET company_name=?, email=?, file_upload=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $company_name, $email, $target_file, $company_id);
        }
    } else {
        $sql = "UPDATE companies SET company_name=?, email=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $company_name, $email, $company_id);
    }

    if ($stmt->execute()) {
        echo "<script>
                alert('Profile updated successfully');
                window.location.href = 'company_dashboard.php';
              </script>";
        exit();
    }
}

$sql = "SELECT * FROM companies WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Management - InterHive</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Lato', sans-serif;
            background: url('images/home.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar styles */
        .navbar {
    width: 100%;
    background-color: rgb(67, 3, 12);
    padding: 18px 30px;
    display: flex;
    flex-wrap: wrap; /* ADD THIS */
    align-items: center;
    justify-content: space-between;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 500;
    height: auto; /* Changed from fixed height */
}

.navbar .brand {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.navbar .brand img {
    height: 40px;
    margin-right: 12px;
}

.navbar .brand span {
    color: white;
    font-size: 18px;
    font-weight: 600;
}

.navbar .nav-links {
    display: flex;
    flex-wrap: wrap; /* Allows wrapping */
    gap: 10px;
}

.navbar a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    transition: background-color 0.3s ease;
    padding: 8px 12px;
    border-radius: 6px;
    white-space: nowrap;
}

.navbar a:hover {
    background-color: rgba(255, 255, 255, 0.2);
}


        /* Profile container styles */
        .profile-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            flex: 1;
            padding: 100px 20px 40px;
        }

        .profile-container {
            background: rgba(255, 255, 255, 0.88);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgb(67, 3, 12);
            width: 100%;
            max-width: 360px;
        }

        h2 {
            text-align: center;
            margin-bottom: 22px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"] {
            width: 100%;
            padding: 9px 11px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 11px;
            background-color: rgb(67, 3, 12);
            color: #fff;
            font-size: 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: rgb(90, 5, 20);
        }

        .uploaded-preview {
            margin-top: -10px;
            margin-bottom: 20px;
        }

        .uploaded-preview img {
            max-width: 100px;
            border-radius: 8px;
            margin-top: 10px;
        }

        /* Footer */
        footer {
            background-color: rgb(67, 3, 12);
            color: white;
            text-align: center;
            padding: 15px 10px;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: 40px;
        }
        

        @media screen and (max-width: 500px) {
            .profile-container {
                padding: 20px;
                max-width: 95%;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
                height: auto;
            }

            .navbar .brand {
                margin-bottom: 7px;
            }

            .navbar a {
                margin: 3px 0 3px 0;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="brand">
        <img src="images/logo.png" alt="Logo">
        <span>InterHive</span>
    </div>
    <div>
        <a href="company_dashboard.php">Dashboard</a>
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- Main Content -->
<div class="profile-wrapper">
    <div class="profile-container">
        <h2>Manage Company Profile</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="company_name">Company Name:</label>
            <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($company['company_name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($company['email']); ?>" required>

            <label for="file_upload">Upload File :</label>
            <input type="file" id="file_upload" name="file_upload">

            <?php if (!empty($company['file_upload'])): ?>
                <div class="uploaded-preview">
                    <strong>Current File:</strong><br>
                    <img src="<?php echo htmlspecialchars($company['file_upload']); ?>" alt="Uploaded File">
                </div>
            <?php endif; ?>

            <input type="submit" value="Update Profile">
        </form>
    </div>
</div>

<!-- Footer -->
<footer>
        &copy; 2025 InterHive. All rights reserved.
    </footer>



</body>
</html>

<?php $conn->close(); ?>
