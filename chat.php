<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

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

// Search for users by username
$search_users = [];
if ($search_term) {
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE username LIKE ? AND id != ?");
    $search_param = "%{$search_term}%";
    $stmt->bind_param("si", $search_param, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $search_users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Fetch messages between the current user and the selected contact
$contact_id = $_GET['contact_id'] ?? null;
$messages = [];
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
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect and Chat</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav>
        <ul>
            <li class="nav-left"><a href="index.php"><img src="logo.png" alt="Logo" class="logo"></a></li>
            <li><a href="index.php">Home</a></li>
            <li><a href="#about">About Us</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#contact">Contact Us</a></li>
            <li class="nav-right"><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="chat-container">
        <div class="contact-list" id="contact-list">
            <h2>Connect with Others</h2>
            <form action="chat.php" method="get" class="name-search">
                <input type="text" name="search" placeholder="Search for users by username">
                <button type="submit">Search</button>
            </form>
            <div class="list-group">
                <?php if (!empty($search_term)): ?>
                    <h3>Search Results:</h3>
                    <?php if (empty($search_users)): ?>
                        <p>No users found.</p>
                    <?php else: ?>
                        <?php foreach ($search_users as $user): ?>
                            <a href="chat.php?contact_id=<?= $user['id'] ?>" class="list-group-item"><?= htmlspecialchars($user['username']) ?></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <h3>Recent Contacts:</h3>
                    <?php if (empty($users)): ?>
                        <p>No recent contacts.</p>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <a href="chat.php?contact_id=<?= $user['id'] ?>" class="list-group-item"><?= htmlspecialchars($user['username']) ?></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="chat-box">
            <?php if ($contact_id): ?>
                <h3>Chat with <?= htmlspecialchars($users[array_search($contact_id, array_column($users, 'id'))]['username'] ?? 'User') ?></h3>
                <div class="messages">
                    <?php foreach ($messages as $message): ?>
                        <div class="message <?= $message['sender_id'] == $user_id ? 'sent' : 'received' ?>">
                            <p><strong><?= $message['sender_id'] == $user_id ? 'Me' : 'Them' ?>:</strong> <?= htmlspecialchars($message['message']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form action="send_message.php" method="post" class="send-box">
                    <input type="hidden" name="receiver_id" value="<?= $contact_id ?>">
                    <input type="text" name="message" placeholder="Type a message" required>
                    <button type="submit" >Send</button>
                </form>
            <?php else: ?>
                <p>Select a contact to start chatting.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
