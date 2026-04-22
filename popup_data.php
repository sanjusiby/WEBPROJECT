<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    echo "<p>Unauthorized access</p>";
    exit();
}

$company_id = $_SESSION['user_id'];
$type = $_GET['type'] ?? '';

function renderTable($headers, $rows, $type) {
    echo "<table border='1' cellpadding='8' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
    echo "<thead><tr>";
    foreach ($headers as $header) {
        echo "<th>" . htmlspecialchars($header) . "</th>";
    }
    echo "<th>Actions</th>";
    echo "</tr></thead><tbody>";
    foreach ($rows as $row) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }

        $id = $row[0];

        echo "<td>
                <a href='edit.php?type=$type&id=$id'>Edit</a> |
                <a href='delete.php?type=$type&id=$id' onclick=\"return confirm('Are you sure you want to delete this?')\">Delete</a>
              </td>";
        echo "</tr>";
    }
    echo count($rows) == 0 ? "<tr><td colspan='" . (count($headers) + 1) . "'>No data available.</td></tr>" : "";
    echo "</tbody></table>";
}

if ($type == 'posted') {
    $stmt = $conn->prepare("SELECT id, course_name FROM internships WHERE company_id = ?");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = [$row['id'], $row['course_name']];
    }
    echo "<h2>Posted Internships</h2>";
    renderTable(['ID', 'Internship Name'], $rows, 'posted');

} elseif ($type == 'shortlisted') {
    $stmt = $conn->prepare("SELECT a.id, u.name, a.status, a.created_at 
                            FROM applications a 
                            JOIN internships i ON a.internship_id = i.id 
                            JOIN users u ON a.user_id = u.id
                            WHERE a.status = 'accepted' AND i.company_id = ?");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = [$row['id'], $row['name'], $row['status'], $row['created_at']];
    }
    echo "<h2>Shortlisted Candidates</h2>";
    renderTable(['Application ID', 'Name', 'Status', 'Date'], $rows, 'shortlisted');

} elseif ($type == 'applications') {
    $stmt = $conn->prepare("SELECT a.id, u.name, a.status, a.created_at 
                            FROM applications a 
                            JOIN internships i ON a.internship_id = i.id 
                            JOIN users u ON a.user_id = u.id
                            WHERE i.company_id = ?");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = [$row['id'], $row['name'], $row['status'], $row['created_at']];
    }
    echo "<h2>All Applications</h2>";
    renderTable(['Application ID', 'Name', 'Status', 'Date'], $rows, 'applications');

} else {
    echo "<p>Invalid request.</p>";
}
$conn->close();
?>
