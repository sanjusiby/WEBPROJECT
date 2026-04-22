<?php
session_start();
include 'db.php';

// Check if the user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['user_id']; // Get company ID from session

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $course_description = $_POST['course_description'];
    $location = $_POST['location'];
    $course_duration = $_POST['course_duration'];
    $requirements = $_POST['requirements'];
    $start_date = $_POST['start_date'];

    // Handle file upload
    $logo = "";
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
        $upload_dir = "internship_up/";
        $logo = $upload_dir . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
    }

    // Insert the new course into the database
    $sql = "INSERT INTO internships (course_name, course_description, location, course_duration, requirements, start_date, logo, company_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $course_name, $course_description, $location, $course_duration, $requirements, $start_date, $logo, $company_id);

    if ($stmt->execute()) {
        echo "<script>alert('New course added successfully!'); window.location.href='company_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding course: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/home2.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .navbar {
            width: 100%;
            background-color: rgb(67, 3, 12);
            padding: 18px 30px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .navbar .brand {
            display: flex;
            align-items: center;
        }

        .navbar .brand img {
            height: 40px;
            margin-right: 12px;
        }

        .navbar .brand span {
            color: white;
            font-size: 22px;
            font-weight: 600;
        }

        .navbar .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
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

        .form-container {
            max-width: 500px;
            background: rgba(255, 255, 255, 0.85);
            margin: 120px auto 40px;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 0px 20px rgba(0,0,0,0.15);
        }

        h2 {
            color: rgb(67, 3, 12);
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: 500;
        }

        input[type="text"], input[type="date"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 12px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        button {
            background: rgb(67, 3, 12);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        button:hover {
            background: #4e0210;
        }

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

        @media (max-width: 600px) {
            .form-container {
                width: 90%;
                margin: 100px auto;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar .nav-links {
                width: 100%;
                justify-content: flex-start;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="brand">
            <img src="images/logo.png" alt="Logo">
            <span>InterHive</span>
        </div>
        <div class="nav-links">
            <a href="company_dashboard.php">Dashboard</a>
            <a href="index.php">Home</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="form-container">
        <h2>Add New Course</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="course_name">Course Name:</label>
            <input type="text" id="course_name" name="course_name" required>

            <label for="course_description">Course Description:</label>
            <textarea id="course_description" name="course_description" rows="4" required></textarea>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>

            <label for="course_duration">Course Duration:</label>
            <input type="text" id="course_duration" name="course_duration" required>

            <label for="requirements">Requirements:</label>
            <textarea id="requirements" name="requirements" rows="4" required></textarea>

            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>

            <label for="logo">Upload Course Logo:</label>
            <input type="file" id="logo" name="logo" accept="image/*">

            <button type="submit">Add Course</button>
        </form>
    </div>

    <footer>
        &copy; 2025 InterHive. All rights reserved.
    </footer>

</body>
</html>

<?php
$conn->close();
?>
