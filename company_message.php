<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO cmessages (sender_id, sender_role, message) VALUES (?, 'company', ?)");
        $stmt->bind_param("is", $company_id, $message);
        $stmt->execute();
    }
}

$stmt = $conn->prepare("SELECT * FROM cmessages WHERE sender_id = ? AND sender_role = 'company' ORDER BY created_at DESC");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Company - Contact Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #eef3f8;
            padding: 40px;
        }
        .container {
            background: #fff;
            max-width: 700px;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        textarea {
            width: 100%;
            height: 100px;
            padding: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: vertical;
            font-size: 14px;
        }
        button {
            margin-top: 10px;
            padding: 12px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background: #0056cc;
        }
        .message-block {
            margin-top: 30px;
        }
        .msg {
            background: #f9f9f9;
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 5px solid #007bff;
        }
        .reply {
            background: #e9f5e9;
            padding: 12px 20px;
            margin-top: 8px;
            border-left: 5px solid #28a745;
            border-radius: 8px;
        }
        .date {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Contact Admin</h2>
    <form method="POST">
        <textarea name="message" placeholder="Type your message to the admin..." required></textarea>
        <button type="submit">Send Message</button>
    </form>

    <div class="message-block">
        <h3>Your Messages & Admin Replies</h3>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="msg">
                <strong>You:</strong> <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                <div class="date"><?php echo $row['created_at']; ?></div>
                <?php if (!empty($row['reply'])): ?>
                    <div class="reply">
                        <strong>Admin Reply:</strong><br>
                        <?php echo nl2br(htmlspecialchars($row['reply'])); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
