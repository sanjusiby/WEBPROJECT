<?php
include 'db.php';
// update path based on actual location

$type = $_POST['type'];
$id = intval($_POST['id']);
$reply = $_POST['reply'];

// Use appropriate table and field
if ($type === 'company') {
    $stmt = $conn->prepare("UPDATE cmessages SET reply = ? WHERE id = ?");
} else {
    $stmt = $conn->prepare("UPDATE messages SET reply = ? WHERE id = ?");
}

$stmt->bind_param("si", $reply, $id);
$stmt->execute();
$stmt->close();

header("Location: admin_dashboard.php");
exit();
?>
