<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_id = $_POST['company_id'];
    $query = "UPDATE companies SET status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}
?>