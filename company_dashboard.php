<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['user_id'];

// Get company info
$sql = "SELECT company_name FROM companies WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();

// Stats
$posted = $conn->prepare("SELECT COUNT(*) AS total FROM internships WHERE company_id = ?");
$posted->bind_param("i", $company_id);
$posted->execute();
$posted_count = $posted->get_result()->fetch_assoc()['total'];

$shortlisted = $conn->prepare("SELECT COUNT(*) AS total FROM applications WHERE status = 'accepted' AND internship_id IN (SELECT id FROM internships WHERE company_id = ?)");
$shortlisted->bind_param("i", $company_id);
$shortlisted->execute();
$shortlisted_count = $shortlisted->get_result()->fetch_assoc()['total'];

$applications = $conn->prepare("SELECT COUNT(*) AS total FROM applications WHERE internship_id IN (SELECT id FROM internships WHERE company_id = ?)");
$applications->bind_param("i", $company_id);
$applications->execute();
$applications_count = $applications->get_result()->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company Dashboard - InterHive</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: url('images/home2.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .sidebar {
            width: 250px;
            background: rgb(67, 3, 12);
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
        }

        .sidebar .header {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .header img {
            width: 40px;
            height: auto;
        }

        .sidebar .header h2 {
            font-size: 20px;
            margin: 0;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 0;
            margin-top: 40px;
        }

        .sidebar nav ul li {
            margin: 20px 0;
        }

        .sidebar nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        .sidebar nav ul li a i {
            margin-right: 10px;
        }

        .dashboard {
            margin-left: 270px;
            padding: 40px 20px;
            flex-grow: 1;
        }

        .dashboard h1 {
            margin-bottom: 30px;
            color: #333;
        }

        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            flex: 1;
            min-width: 250px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: scale(1.02);
        }

        .stat-card h3 {
            margin-bottom: 10px;
            color: rgb(67, 3, 12);
        }

        .stat-card p {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        .popup {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .popup-content {
            background: white;
            padding: 30px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            border-radius: 10px;
            position: relative;
        }

        .popup-content h2 {
            margin-top: 0;
        }

        .close-btn {
            position: absolute;
            top: 10px; right: 15px;
            font-size: 20px;
            cursor: pointer;
        }

        footer {
            margin-left: 270px;
            background: #222;
            color: #ccc;
            text-align: center;
            padding: 15px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 10px 20px;
            }

            .sidebar nav ul {
                display: flex;
                flex-direction: row;
                gap: 20px;
                margin-top: 0;
            }

            .dashboard {
                margin-left: 0;
                padding: 20px;
            }

            footer {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="header">
        <img src="images/logo.png" alt="Logo">
        <h2>InterHive</h2>
    </div>
    <nav>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="profile_management.php"><i class="fas fa-user"></i> Profile Management</a></li>
            <li><a href="post_internship.php"><i class="fas fa-briefcase"></i> Post Internship</a></li>
            <li><a href="admin_communication.php"><i class="fas fa-envelope"></i> Admin Communication</a></li>
            <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
</div>

<div class="dashboard">
    <h1>Welcome, <?php echo htmlspecialchars($company['company_name']); ?></h1>

    <div class="stats-container">
        <div class="stat-card" onclick="showPopup('posted')">
            <h3>Posted Internships</h3>
            <p><?php echo $posted_count; ?></p>
        </div>
        <div class="stat-card" onclick="showPopup('shortlisted')">
            <h3>Shortlisted</h3>
            <p><?php echo $shortlisted_count; ?></p>
        </div>
        <div class="stat-card" onclick="showPopup('applications')">
            <h3>Total Applications</h3>
            <p><?php echo $applications_count; ?></p>
        </div>
    </div>
</div>

<div class="popup" id="popup">
    <div class="popup-content" id="popupContent">
        <span class="close-btn" onclick="closePopup()">&times;</span>
        <div id="popupBody">Loading...</div>
    </div>
</div>

<footer>
    &copy; 2025 InterHive. All rights reserved.
</footer>

<script>
    function showPopup(type) {
        const popup = document.getElementById("popup");
        const popupBody = document.getElementById("popupBody");
        popup.style.display = "flex";
        popupBody.innerHTML = "Loading...";
        fetch(`popup_data.php?type=${type}`)
            .then(res => res.text())
            .then(data => popupBody.innerHTML = data)
            .catch(() => popupBody.innerHTML = "Failed to load data.");
    }

    function closePopup() {
        document.getElementById("popup").style.display = "none";
    }
</script>

</body>
</html>
