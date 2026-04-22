<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$query = "SELECT * FROM admin WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Fetch internships with company name
$internships_result = $conn->query("
    SELECT internships.*, companies.company_name 
    FROM internships 
    JOIN companies ON internships.company_id = companies.id
");

// Fetch all companies
$companies_result = $conn->query("SELECT * FROM companies");


// Fetch students who applied with internship and company info
$all_students_result = $conn->query("
    SELECT users.id, users.name, users.email, users.created_at, internships.course_name, companies.company_name
    FROM applications
    JOIN users ON applications.user_id = users.id
    JOIN internships ON applications.internship_id = internships.id
    JOIN companies ON internships.company_id = companies.id
");

// Fetch student count per internship
$students_per_course_result = $conn->query("
    SELECT internships.course_name, COUNT(applications.user_id) as number_of_students
    FROM applications
    JOIN internships ON applications.internship_id = internships.id
    GROUP BY internships.course_name
");

// Handle approval/removal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $company_id = $_POST['company_id'];
    if ($_POST['action'] === 'approve') {
        $stmt = $conn->prepare("UPDATE companies SET is_approved = 1 WHERE id = ?");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
    } elseif ($_POST['action'] === 'remove') {
        $stmt = $conn->prepare("DELETE FROM companies WHERE id = ?");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
    }
    header("Location: admin_dashboard.php");
    exit();
}
$company_messages_result = $conn->query("SELECT * FROM cmessages ORDER BY created_at DESC");
$user_messages_result = $conn->query("SELECT * FROM messages ORDER BY timestamp DESC");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - InterHive</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Lato', sans-serif;
            margin: 0;
            display: flex;
            
            background: url('images/home.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .sidebar {
            width: 240px;
            background: rgb(52, 2, 9);
            color: #fff;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }
        .sidebar h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color:  #f9f9f9;
        }
        .sidebar a {
            display: block;
            color: #fff;
            padding: 12px;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 6px;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .sidebar a:hover {
            background: #444;
        }
        .main-content {
            flex: 1;
            padding: 30px;
            
        }
        .section {
            display: none;
        }
        .active-section {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            background: #fff;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }
        table th {
            background: #eee;
        }
        .button {
            padding: 6px 12px;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        h2 {
            margin-bottom: 20px;
            color:  rgb(238, 233, 234);
        }
        .sidebar h2 img {
            height: 45px;
            margin-right: 10px;
        }
        textarea {
    width: 100%;
    height: 60px;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 8px;
    border: 1px solid #ccc;
}

    </style>
    <script>
        function showSection(sectionId) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => {
                section.classList.remove('active-section');
            });
            document.getElementById(sectionId).classList.add('active-section');
        }

        window.onload = function() {
            showSection('internships'); // default section
        };
    </script>
</head>
<body>

<div class="sidebar">
<h2><img src="images/logo.png" alt="Logo"> InterHive</h2>
    <a href="#" onclick="showSection('internships')"><i class="fas fa-briefcase"></i> Internships</a>
    <a href="#" onclick="showSection('companies')"><i class="fas fa-building"></i> Companies</a>
    <a href="#" onclick="showSection('students')"><i class="fas fa-user-graduate"></i> Students</a>
    <a href="#" onclick="showSection('companyMessages')"><i class="fas fa-envelope"></i>  Messages</a>
    <a href="index.php"><i class="fas fa-home"></i> Home</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">

    <!-- Internships Section -->
    <div id="internships" class="section">
        <h2>All Internships</h2>
        <table>
            <tr><th>ID</th><th>Title</th><th>Description</th><th>Company</th></tr>
            <?php while ($internship = $internships_result->fetch_assoc()): ?>
            <tr>
                <td><?= $internship['id'] ?></td>
                <td><?= htmlspecialchars($internship['course_name']) ?></td>
                <td><?= htmlspecialchars($internship['course_description']) ?></td>
                <td><?= htmlspecialchars($internship['company_name']) ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Companies Section -->
    <div id="companies" class="section">
        <h2>Company Management</h2>
        <table>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Actions</th></tr>
            <?php while ($company = $companies_result->fetch_assoc()): ?>
            <tr>
                <td><?= $company['id'] ?></td>
                <td><?= htmlspecialchars($company['company_name']) ?></td>
                <td><?= htmlspecialchars($company['email']) ?></td>
                <td><?= $company['is_approved'] ? 'Approved' : 'Pending' ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="company_id" value="<?= $company['id'] ?>">
                        <button class="button" name="action" value="approve">Approve</button>
                        <button class="button" name="action" value="remove" style="background:red;">Remove</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <!-- Company Messages Section -->
    <div id="companyMessages" class="section">
    <h2>Company Messages</h2>
    <div style="max-height: 300px; overflow-y: auto;">
        <table border="1">
            <tr>
                <th>ID</th><th>Company Name</th><th>Email</th><th>Message</th>
                <th>Reply</th><th>Timestamp</th><th>Action</th>
            </tr>
            <?php while ($msg = $company_messages_result->fetch_assoc()): ?>
            <tr>
                <form method="POST" action="reply_handler.php">
                    <td><?= $msg['id'] ?></td>
                    <td><?= htmlspecialchars($msg['company_name']) ?></td>
                    <td><?= htmlspecialchars($msg['company_email']) ?></td>
                    <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                    <td>
                        <textarea name="reply"><?= htmlspecialchars($msg['reply']) ?></textarea>
                    </td>
                    <td><?= htmlspecialchars($msg['created_at']) ?></td>
                    <td>
                        <input type="hidden" name="type" value="company">
                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                        <button type="submit">Reply</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <h2>User Messages</h2>
    <div style="max-height: 300px; overflow-y: auto;">
        <table border="1">
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Subject</th>
                <th>Message</th><th>Reply</th><th>Timestamp</th><th>Action</th>
            </tr>
            <?php while ($msg = $user_messages_result->fetch_assoc()): ?>
            <tr>
                <form method="POST" action="reply_handler.php">
                    <td><?= $msg['id'] ?></td>
                    <td><?= htmlspecialchars($msg['name']) ?></td>
                    <td><?= htmlspecialchars($msg['email']) ?></td>
                    <td><?= htmlspecialchars($msg['subject']) ?></td>
                    <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                    <td>
                        <textarea name="reply"><?= htmlspecialchars($msg['reply']) ?></textarea>
                    </td>
                    <td><?= htmlspecialchars($msg['timestamp']) ?></td>
                    <td>
                        <input type="hidden" name="type" value="user">
                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                        <button type="submit">Reply</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

   



    <!-- Students Section -->
    <div id="students" class="section">
        <h2>All Students</h2>

        <h4>Registered Students</h4>
        <table>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Internship</th><th>Company</th><th>Registered At</th></tr>
            <?php while ($student = $all_students_result->fetch_assoc()): ?>
            <tr>
                <td><?= $student['id'] ?></td>
                <td><?= htmlspecialchars($student['name']) ?></td>
                <td><?= htmlspecialchars($student['email']) ?></td>
                <td><?= htmlspecialchars($student['course_name']) ?></td>
                <td><?= htmlspecialchars($student['company_name']) ?></td>
                <td><?= htmlspecialchars($student['created_at']) ?></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <h4>Students per Internship</h4>
        <table>
            <tr><th>Internship</th><th>No. of Students</th></tr>
            <?php while ($row = $students_per_course_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['course_name']) ?></td>
                <td><?= $row['number_of_students'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>

</body>
</html>
