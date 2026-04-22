<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$internship_id = $_GET['internship_id'] ?? null;
$internship = null;

if ($internship_id) {
    $sql = "SELECT * FROM internships WHERE id = ? AND company_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $internship_id, $_SESSION['user_id']);
    $stmt->execute();
    $internship = $stmt->get_result()->fetch_assoc();
}

if (isset($_POST['update_internship'])) {
    $course_name = $_POST['course_name'];
    $location = $_POST['location'];
    $course_duration = $_POST['course_duration'];

    $sql = "UPDATE internships SET course_name=?, location=?, course_duration=? WHERE id=? AND company_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $course_name, $location, $course_duration, $internship_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        header("Location: company_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Internship</title>
</head>
<body>
    <h1>Edit Internship</h1>
    <form method="POST">
        <label>Course Name:</label>
        <input type="text" name="course_name" value="<?php echo htmlspecialchars($internship['course_name']); ?>" required>
        <label>Location:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($internship['location']); ?>" required>
        <label>Course Duration:</label>
        <input type="number" name="course_duration" value="<?php echo htmlspecialchars($internship['course_duration']); ?>" required>
        <button type="submit" name="update_internship">Update Internship</button>
    </form>
</body>
</html>

<?php $conn->close(); ?>