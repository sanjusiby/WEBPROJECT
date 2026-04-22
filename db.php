<?php
$servername = "localhost"; // Usually localhost
$username = "root"; // Default username for XAMPP
$password = ""; // Default password for XAMPP (usually empty)
$dbname = "internship_platform"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>