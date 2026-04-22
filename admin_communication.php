<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['user_id'];
$admin_id = 1; // Change if admin user_id differs

// Handle message send
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $message = trim($_POST['message']);
    $sender_role = 'company';

    // Fetch company name and email from DB
    $stmtCompany = $conn->prepare("SELECT company_name, email FROM companies WHERE id = ?");
    $stmtCompany->bind_param("i", $company_id);
    $stmtCompany->execute();
    $stmtCompany->bind_result($company_name, $company_email);
    $stmtCompany->fetch();
    $stmtCompany->close();

    // Insert into cmessages table
    $stmt = $conn->prepare("INSERT INTO cmessages (sender_id, sender_role, message, company_name, company_email, recevier_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $company_id, $sender_role, $message, $company_name, $company_email, $admin_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch previous messages
$stmt = $conn->prepare("SELECT message, created_at FROM cmessages WHERE sender_id = ? AND recevier_id = ? ORDER BY created_at DESC");
$stmt->bind_param("ii", $company_id, $admin_id);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Communication - InterHive</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Lato', Tahoma, Geneva, Verdana, sans-serif;
            background: url('images/home.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .navbar {
            background: rgb(67, 3, 12);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }

        .navbar-left {
            display: flex;
            align-items: center;
        }

        .navbar-left img {
            height: 40px;
            margin-right: 12px;
        }

        .navbar h1 {
            margin: 0;
            font-size: 22px;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 700px;
            margin: 60px auto;
            background: rgba(255, 255, 255, 0.92);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        h2, h3 {
            text-align: center;
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        textarea {
            width: 100%;
            height: 100px;
            padding: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: vertical;
            font-size: 14px;
        }

        button {
            margin-top: 10px;
            padding: 12px 20px;
            background: rgb(67, 3, 12);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background: #4d020a;
        }

        ul {
            margin-top: 30px;
            list-style: none;
            padding: 0;
        }

        li {
            background: #f9f9f9;
            padding: 15px 20px;
            margin-bottom: 15px;
            border-left: 5px solid rgb(67, 3, 12);
            border-radius: 8px;
        }

        .timestamp {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
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

        @media (max-width: 768px) {
            .container {
                margin: 30px 15px;
                padding: 20px;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar a {
                margin: 10px 0 0;
            }

            .navbar-left {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar-left img {
                margin-bottom: 8px;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="navbar-left">
        <img src="images/logo.png" alt="Logo">
        <h1>InterHive</h1>
    </div>
    <div>
        <a href="company_dashboard.php">Dashboard</a>
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Contact Admin</h2>
    <form method="post" onsubmit="return showMessage();">
    <textarea name="message" placeholder="Type your message here..." required></textarea><br>
    <button type="submit">Send Message</button>
</form>


    <h3>Your Previous Messages</h3>
    <ul>
        <?php while ($row = $messages->fetch_assoc()): ?>
            <li>
                <?= nl2br(htmlspecialchars($row['message'])) ?>
                <div class="timestamp"><?= date("d M Y, H:i", strtotime($row['created_at'])) ?></div>
            </li>
        <?php endwhile; ?>
    </ul>
</div>
<script>
function showMessage() {
    alert("The reply will be sent by the admin to your registered email.");
    return true;
}
</script>


<footer>
    &copy; 2025 InterHive. All rights reserved.
</footer>

</body>
</html>

<?php $conn->close(); ?>
