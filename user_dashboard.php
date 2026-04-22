<?php
session_start();
include 'db.php';

// Ensure the user is logged in and is a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$internships = $saved_internships = $applications = [];

// Function to simplify fetching data from DB
function fetchData($conn, $query, $types = "", $params = []) {
    $stmt = $conn->prepare($query);
    if ($types) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch all internships
$internships = fetchData($conn, "
    SELECT i.*, c.company_name 
    FROM internships i 
    JOIN companies c ON i.company_id = c.id
")->fetch_all(MYSQLI_ASSOC);

// Fetch saved internships for the user
$saved_internships = fetchData($conn, "
    SELECT i.*, c.company_name 
    FROM saved_internships s 
    JOIN internships i ON s.internship_id = i.id 
    JOIN companies c ON i.company_id = c.id 
    WHERE s.user_id = ?
", "i", [$user_id])->fetch_all(MYSQLI_ASSOC);

// Fetch applications submitted by user (excluding rejected)
$applications = fetchData($conn, "
    SELECT a.*, i.course_name, i.location, i.course_description, i.requirements, i.course_duration, i.start_date 
    FROM applications a 
    JOIN internships i ON a.internship_id = i.id 
    WHERE a.user_id = ? AND a.status != 'rejected'
", "i", [$user_id])->fetch_all(MYSQLI_ASSOC);

// Handle all form submissions
function handlePost($conn, $user_id) {
    if (isset($_POST['apply_internship'])) {
        $id = $_POST['internship_id'];
        $check = fetchData($conn, "SELECT * FROM applications WHERE user_id = ? AND internship_id = ?", "ii", [$user_id, $id]);
        if ($check->num_rows > 0) {
            $_SESSION['alert'] = "Application already submitted";
        } else {
            $stmt = $conn->prepare("INSERT INTO applications (user_id, internship_id, status) VALUES (?, ?, 'pending')");
            $stmt->bind_param("ii", $user_id, $id);
            if ($stmt->execute()) {
                $_SESSION['alert'] = "Application submitted successfully!";
            }
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['save_internship'])) {
        $id = $_POST['internship_id'];
        $check = fetchData($conn, "SELECT * FROM saved_internships WHERE user_id = ? AND internship_id = ?", "ii", [$user_id, $id]);
        if ($check->num_rows > 0) {
            $_SESSION['alert'] = "Already saved";
        } else {
            $stmt = $conn->prepare("INSERT INTO saved_internships (user_id, internship_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $id);
            if ($stmt->execute()) {
                $_SESSION['alert'] = "Internship saved successfully!";
            }
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['remove_internship'])) {
        $id = $_POST['internship_id'];
        $stmt = $conn->prepare("DELETE FROM saved_internships WHERE user_id = ? AND internship_id = ?");
        $stmt->bind_param("ii", $user_id, $id);
        if ($stmt->execute()) {
            $_SESSION['alert'] = "Internship removed from saved list!";
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['phone'] = $phone;

        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
        if ($stmt->execute()) {
            $_SESSION['alert'] = "Profile updated successfully!";
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['submit_feedback'])) {
        $id = $_POST['application_id'];
        $feedback = $_POST['feedback'];
        $stmt = $conn->prepare("UPDATE applications SET feedback=? WHERE id=?");
        $stmt->bind_param("si", $feedback, $id);
        if ($stmt->execute()) {
            $_SESSION['alert'] = "Feedback submitted successfully!";
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Execute form handling if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handlePost($conn, $user_id);
}

// Logout handler
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styleuser.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Lato', sans-serif;
            margin: 0;
            display: flex;
            background: url('images/home.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .sidebar {
            width: 220px;
            background-color: rgb(52, 2, 9);
            height: 100vh;
            padding: 30px 20px;
            box-shadow: 2px 0 10px #f9f9f9;
            color: white;
            position: fixed;
        }
        .sidebar h2 {
            font-size: 24px;
            margin-bottom: 40px;
            text-align: center;
            color: white;
        }
        .sidebar a, .sidebar button {
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: none;
            color: white;
            padding: 12px;
            margin: 10px 0;
            width: 100%;
            text-align: left;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
        }
        .sidebar a:hover, .sidebar button:hover {
            background: rgb(238, 233, 234);
            border-radius: 4px;
            color: black;
        }
        .main {
            margin-left: 240px;
            padding: 30px;
            flex: 1;
            color: white;
        }
        .internship-cards, .my-applications {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .internship-card {
            background: rgba(255,255,255,0.85);
            border-radius: 10px;
            padding: 20px;
            width: 280px;
            color: black;
            height: auto;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .internship-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .internship-card img {
            width: 100%;
            height: 180px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .button {
            background: rgb(52, 2, 9);
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .button:hover {
            background: rgb(71, 3, 13);
        }
        a.quick-details {
            display: inline-block;
            color: rgb(52, 2, 9);
            font-size: 14px;
            margin-top: 10px;
            text-decoration: underline;
            cursor: pointer;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            color: black;
        }
        .close {
            color: red;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
    </style>
   <script>
    function toggleSection(id) {
        document.querySelectorAll('.toggle-section').forEach(el => {
            el.style.display = el.id === id ? 'block' : 'none';
        });
    }

    // This line ensures "Available Internships" shows by default when page loads
    window.onload = function() {
        toggleSection('available-internships');
    };

    function openModal(title, description, requirements, duration, location, start, importance, company) {
        document.getElementById('modal-title').innerText = title;
        document.getElementById('modal-body').innerHTML = `
            <p><strong>Description:</strong> ${description}</p>
            <p><strong>Requirements:</strong> ${requirements}</p>
            <p><strong>Duration:</strong> ${duration}</p>
            <p><strong>Start Date:</strong> ${start}</p>
            <p><strong>Location:</strong> ${location}</p>`;
        document.getElementById('popupModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('popupModal').style.display = 'none';
    }
</script>

</head>
<body>

<div class="sidebar">
    <h2><img src="images/logo.png" alt="Logo" style="height:30px;"> InterHive</h2>
    <button onclick="toggleSection('available-internships')"><i class="fas fa-briefcase"></i> Internships</button>
    <button onclick="toggleSection('my-applications')"><i class="fas fa-file-alt"></i> Applications</button>
    <button onclick="toggleSection('saved-internships')"><i class="fas fa-bookmark"></i> Saved</button>
    <button onclick="toggleSection('edit-profile')"><i class="fas fa-user-edit"></i> Edit Profile</button>
    <a href="index.php"><i class="fas fa-home"></i> Home</a>
    <a href="?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
    <div id="available-internships" class="toggle-section">
        <h2>Available Internships</h2>
        <div class="internship-cards">
            <?php foreach ($internships as $i): ?>
                <div class="internship-card">
                    <img src="<?= htmlspecialchars($i['logo']) ?>" alt="Logo">
                    <h3><?= htmlspecialchars($i['course_name']) ?></h3>
                    <p><b>Company:</b> <?= htmlspecialchars($i['company_name']) ?></p>
                    <form method="POST">
                        <input type="hidden" name="internship_id" value="<?= $i['id'] ?>">
                        <a class="quick-details" onclick="openModal(
    `<?= addslashes($i['course_name']) ?>`,
    `<?= addslashes($i['course_description']) ?>`,
    `<?= addslashes($i['requirements']) ?>`,
    `<?= addslashes($i['course_duration']) ?>`,
    `<?= addslashes($i['location']) ?>`,
    `<?= addslashes($i['start_date']) ?>`
)">View Details</a></br></br>

                        <button name="apply_internship" class="button">Apply</button>
                        <button name="save_internship" class="button">Save</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="my-applications" class="toggle-section" style="display:none;">
        <h2>My Applications</h2>
        <div class="my-applications">
            <?php foreach ($applications as $app): ?>
                <div class="internship-card">
                    <h3><?= htmlspecialchars($app['course_name']) ?></h3>
                    <p><b>Location:</b> <?= htmlspecialchars($app['location']) ?></p>
                    <p><b>Status:</b> <?= htmlspecialchars($app['status']) ?></p>
                    <input type="hidden" name="internship_id" value="<?= $i['id'] ?>">
                        <a class="quick-details" onclick="openModal(
                            `<?= addslashes($i['course_name']) ?>`,
                            `<?= addslashes($i['course_description']) ?>`,
                            `<?= addslashes($i['requirements']) ?>`,
                            `<?= addslashes($i['course_duration']) ?>`,
                            `<?= addslashes($i['location']) ?>`,
                            `<?= addslashes($i['start_date']) ?>`,
                        )">View Details</a><br><br>
                    
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="saved-internships" class="toggle-section" style="display:none;">
        <h2>Saved Internships</h2>
        <div class="internship-cards">
            <?php foreach ($saved_internships as $saved): ?>
                <div class="internship-card">
                    <h3><?= htmlspecialchars($saved['course_name']) ?></h3>
                    <p><b>Company:</b> <?= htmlspecialchars($saved['company_name']) ?></p>
                    <input type="hidden" name="internship_id" value="<?= $i['id'] ?>">
                        <a class="quick-details" onclick="openModal(
                            `<?= addslashes($i['course_name']) ?>`,
                            `<?= addslashes($i['course_description']) ?>`,
                            `<?= addslashes($i['requirements']) ?>`,
                            `<?= addslashes($i['course_duration']) ?>`,
                            `<?= addslashes($i['location']) ?>`,
                            `<?= addslashes($i['start_date']) ?>`,
                        )">View Details</a><br><br>
                    <form method="POST">
                        <input type="hidden" name="internship_id" value="<?= $saved['id'] ?>">
                        <button name="remove_internship" class="button">Remove</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <style>
    .edit-profile-container {
        max-width: 500px;
        margin: 40px auto;
        padding: 30px;
        background: rgba(206, 201, 201, 0.83); /* Transparent container */
        border: 1px solid #ccc;
        border-radius: 15px;
        box-shadow: 0 8px 16px rgb(24, 24, 24);
        backdrop-filter: blur(6px); /* Frosted glass effect */
        color: #fff;
    }

    .edit-profile-container h2 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 24px;
        color: #ffffff;
    }

    .edit-profile-container form {
        display: flex;
        flex-direction: column;
    }

    .edit-profile-container input {
        padding: 12px 15px;
        margin-bottom: 15px;
        border: none;
        border-radius: 8px;
        background-color: rgba(11, 11, 11, 0.2);
        color: #fff;
    }

    .edit-profile-container input::placeholder {
        color: #ddd;
    }

    .edit-profile-container button {
        padding: 12px;
        border: none;
        border-radius: 8px;
        background-color: rgb(52, 2, 9);
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .edit-profile-container button:hover {
        background-color: rgb(82, 4, 16);
    }

    .toggle-section {
        display: none;
    }
    .modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.6);
}

.modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border-radius: 10px;
    width: 50%;
    color: black;
}

.close {
    color: red;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    float: right;
}
</style>

<div id="edit-profile" class="toggle-section">
    <div class="edit-profile-container">
        <h2>Edit Profile</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Name" value="<?= $_SESSION['name'] ?? '' ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?= $_SESSION['email'] ?? '' ?>" readonly>
            <input type="text" name="phone" placeholder="Phone" value="<?= $_SESSION['phone'] ?? '' ?>" required>
            <button name="update_profile">Update</button>
        </form>
    </div>
</div>
<!-- Modal -->
<div id="popupModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3 id="modal-title"></h3>
        <div id="modal-body"></div>
    </div>
</div>
<div id="messageModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="messageText"></p>
        </div>
    </div>

    <!-- Include JavaScript for Modal -->
    <script>
    function showMessage(message) {
        document.getElementById('messageText').innerText = message;
        document.getElementById('messageModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('messageModal').style.display = 'none';
    }
    </script>

    <!-- Include PHP Session Message Trigger -->
    <?php
    if (isset($_SESSION['alert'])) {
        echo "<script>showMessage('" . addslashes($_SESSION['alert']) . "');</script>";
        unset($_SESSION['alert']);
    }
    ?>
</body>
</html>
