<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['user_id'];

// Handle accept/reject
if (isset($_POST['action'], $_POST['id'])) {
    $status = $_POST['action'] === 'accept' ? 'Accepted' : 'Rejected';
    $app_id = $_POST['id'];

    $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ? AND company_id = ?");
    $stmt->bind_param("sii", $status, $app_id, $company_id);
    $stmt->execute();
}

// Fetch applications
$sql = "SELECT a.id AS application_id, u.name, u.email, i.course_name, a.status
        FROM applications a
        JOIN users u ON a.user_id = u.id
        JOIN internships i ON a.internship_id = i.id
        WHERE i.company_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$applications = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management - InterHive</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>User Management</h2>
    <table border="1" cellpadding="10">
        <tr>
            <th>Student Name</th>
            <th>Email</th>
            <th>Internship Title</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $applications->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['course_name']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <?php if ($row['status'] === 'Pending'): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" name="action" value="accept">Accept</button>
                        <button type="submit" name="action" value="reject">Reject</button>
                    </form>
                <?php else: ?>
                    <?= $row['status'] ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>
