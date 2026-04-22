<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    echo "<script>alert('Unauthorized access'); window.location.href='company_dashboard.php';</script>";
    exit();
}

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? 0;

// ---------- Handle POST (Update Logic) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($type === 'posted') {
        $course_name = $_POST['course_name'];
        $course_description = $_POST['course_description'];
        $location = $_POST['location'];
        $course_duration = $_POST['course_duration'];
        $requirements = $_POST['requirements'];
        $start_date = $_POST['start_date'];
        $logo = $_POST['logo'];

        $stmt = $conn->prepare("UPDATE internships SET course_name=?, course_description=?, location=?, course_duration=?, requirements=?, start_date=?, logo=? WHERE id=?");
        $stmt->bind_param("sssssssi", $course_name, $course_description, $location, $course_duration, $requirements, $start_date, $logo, $id);
        $stmt->execute();

    } elseif (in_array($type, ['applications', 'shortlisted'])) {
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE applications SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
    }

    echo "<script>alert('Updated successfully'); window.location.href='company_dashboard.php?type=$type';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit</title>
    <style>
        /* Your previously used CSS styles here (from the styled forms above) */
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .form-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #444;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .btn-group {
            display: flex;
            justify-content: space-between;
        }

        button {
            padding: 12px 24px;
            font-size: 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #0056cc;
        }

        button[type="reset"] {
            background-color: #e0e0e0;
            color: #333;
        }

        button[type="reset"]:hover {
            background-color: #cfcfcf;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <?php
        // ---------- Handle Form Display ----------
        if ($type === 'posted') {
            $stmt = $conn->prepare("SELECT course_name, course_description, location, course_duration, requirements, start_date, logo FROM internships WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 0) {
                echo "<script>alert('Internship not found'); window.location.href='company_dashboard.php?type=posted';</script>";
                exit();
            }

            $stmt->bind_result($course_name, $course_description, $location, $course_duration, $requirements, $start_date, $logo);
            $stmt->fetch();
            ?>
            <h2>Edit Internship</h2>
            <form method="post">
                <label>Internship Name:</label>
                <input type="text" name="course_name" value="<?= htmlspecialchars($course_name) ?>" required>

                <label>Description:</label>
                <textarea name="course_description" required><?= htmlspecialchars($course_description) ?></textarea>

                <label>Location:</label>
                <input type="text" name="location" value="<?= htmlspecialchars($location) ?>" required>

                <label>Duration:</label>
                <input type="text" name="course_duration" value="<?= htmlspecialchars($course_duration) ?>" required>

                <label>Requirements:</label>
                <input type="text" name="requirements" value="<?= htmlspecialchars($requirements) ?>" required>

                <label>Start Date:</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>

                <label>Logo URL:</label>
                <input type="text" name="logo" value="<?= htmlspecialchars($logo) ?>" required>

                <div class="btn-group">
                    <button type="submit">Update</button>
                    <button type="reset">Reset</button>
                </div>
            </form>
            <?php
            $stmt->close();
        } elseif (in_array($type, ['applications', 'shortlisted'])) {
            $stmt = $conn->prepare("SELECT status FROM applications WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 0) {
                echo "<script>alert('Application not found'); window.location.href='company_dashboard.php?type=$type';</script>";
                exit();
            }

            $stmt->bind_result($status);
            $stmt->fetch();
            ?>
            <h2>Update Application Status</h2>
            <form method="post">
                <label>Status:</label>
                <select name="status" required>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="accepted" <?= $status === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>

                <div class="btn-group">
                    <button type="submit">Update</button>
                    <button type="reset">Reset</button>
                </div>
            </form>
            <?php
            $stmt->close();
        } else {
            echo "<script>alert('Invalid request'); window.location.href='company_dashboard.php';</script>";
        }
        ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>
