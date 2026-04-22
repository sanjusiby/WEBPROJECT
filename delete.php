<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    echo "Unauthorized";
    exit();
}

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? '';

if ($type === 'posted') {
    $stmt = $conn->prepare("DELETE FROM internships WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "Internship deleted. <a href='dashboard.php?type=posted'>Back</a>";
} elseif ($type === 'shortlisted' || $type === 'applications') {
    $stmt = $conn->prepare("DELETE FROM applications WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "Application deleted. <a href='dashboard.php?type=$type'>Back</a>";
} else {
    echo "Invalid request.";
}
?>
