<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$internship_id = $_GET['internship_id'] ?? null;

if ($internship_id) {
    $sql = "SELECT * FROM applications WHERE internship_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $internship_id);
    $stmt->execute();
    $applications = $stmt->get_result();
} else {
    echo "No internship selected.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applications for Internship</title>
</head>
<body>
    <h1>Applications for Internship ID: <?php echo htmlspecialchars($internship_id); ?></h1>
    <ul>
        <?php while ($application = $applications->fetch_assoc()): ?>
            <li>
                Applicant ID: <?php echo htmlspecialchars($application['internship_id']); ?> - Status: <?php echo htmlspecialchars($application['status']); ?>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>

<?php $conn->close(); ?>