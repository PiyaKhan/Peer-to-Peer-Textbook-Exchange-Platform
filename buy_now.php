<?php
session_start();
require_once 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the cart is empty
if (!isset($_SESSION['cart'][$user_id]) || empty($_SESSION['cart'][$user_id])) {
    echo "Your cart is empty.";
    exit();
}

$cart_items = $_SESSION['cart'][$user_id];
$total = 0;

$conn->begin_transaction();

try {
    // Insert a new order into the orders table
    $sql = "INSERT INTO orders (user_id, total_price) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Get the inserted order ID
    $stmt->close();

    // Prepare the statement to insert order items
    $sql = "INSERT INTO order_items (order_id, book_id, type, quantity, price) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    foreach ($cart_items as $key => $quantity) {
        list($book_id, $type) = explode('_', $key);
        
        // Fetch the price based on the book type
        $book = $books[$book_id];
        $price = ($type === 'ebook') ? $book['price_ebook'] : $book['price_hardcopy'];
        $total_price = $price * $quantity;
        $total += $total_price;

        // Insert the order item
        $stmt->bind_param("iisid", $order_id, $book_id, $type, $quantity, $price);
        $stmt->execute();
    }
    $stmt->close();

    // Update the total price in the orders table
    $sql = "UPDATE orders SET total_price = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $total, $order_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    die("Failed to save order: " . $e->getMessage());
}

// Clear the cart after purchase
unset($_SESSION['cart'][$user_id]);

// Redirect to the billing page
header("Location: billing.php?order_id=" . $order_id);
exit();
?>
