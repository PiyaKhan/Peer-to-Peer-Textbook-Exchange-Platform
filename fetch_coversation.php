<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch users the current user has messaged with
$stmt = $conn->prepare("
    SELECT DISTINCT 
        users.id, 
        users.username 
    FROM users 
    INNER JOIN messages 
        ON users.id = messages.sender_id 
        OR users.id = messages.receiver_id 
    WHERE (messages.sender_id = ? OR messages.receiver_id = ?)
        AND users.id != ?
");
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch messages between the current user and the selected contact
$contact_id = $_GET['contact_id'] ?? null;
if ($contact_id) {
    $stmt = $conn->prepare("
        SELECT * FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?)
        OR (sender_id = ? AND receiver_id = ?) 
        ORDER BY timestamp ASC
    ");
    $stmt->bind_param("iiii", $user_id, $contact_id, $contact_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $messages = [];
}

$conn->close();
echo json_encode(['users' => $users, 'messages' => $messages]);
?>
