<?php
session_start();
require 'config.php';

if (isset($_GET['id']) && isset($_GET['quantity']) && isset($_GET['type'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = intval($_GET['id']);
    $quantity = intval($_GET['quantity']);
    $book_type = $_GET['type'];

    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND book_id = ? AND book_type = ?");
    $stmt->bind_param("iis", $user_id, $book_id, $book_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update quantity if the book is already in the cart
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND book_id = ? AND book_type = ?");
        $stmt->bind_param("iiis", $quantity, $user_id, $book_id, $book_type);
    } else {
        // Insert new book into the cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity, book_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $user_id, $book_id, $quantity, $book_type);
    }
    $stmt->execute();
    $stmt->close();
}

header("Location: cart.php");
exit();
?>
