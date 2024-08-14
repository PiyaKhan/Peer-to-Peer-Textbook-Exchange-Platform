<?php
session_start();
require 'config.php';

$user_id = $_SESSION['user_id'];
$cart_items = [];
$total_price = 0.0;

// Handle adding/updating items in the cart
if (isset($_GET['id']) && isset($_GET['quantity']) && isset($_GET['type'])) {
    $book_id = intval($_GET['id']);
    $quantity = intval($_GET['quantity']);
    $type = $_GET['type'];

    // Check if the book is already in the cart
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND book_id = ?");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity if the book is already in the cart
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND book_id = ?");
        $stmt->bind_param("iii", $quantity, $user_id, $book_id);
    } else {
        // Insert new book into the cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity, book_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $user_id, $book_id, $quantity, $type);
    }
    $stmt->execute();
    $stmt->close();
}

// Fetch cart items with both prices
$stmt = $conn->prepare("
    SELECT b.id, b.title, b.picture, b.price_hardcopy, b.price_ebook, c.quantity, c.book_type
    FROM cart c
    JOIN books b ON c.book_id = b.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    if ($row['book_type'] === 'hardcopy') {
        $total_price += $row['price_hardcopy'] * $row['quantity'];
    } elseif ($row['book_type'] === 'ebook') {
        $total_price += $row['price_ebook'] * $row['quantity'];
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .cart-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .cart-items ul {
            list-style: none;
            padding: 0;
        }
        .cart-items li {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .cart-items img {
            max-width: 100px;
            margin-right: 20px;
            border-radius: 5px;
        }
        .cart-item-details {
            flex-grow: 1;
        }
        .cart-item-title {
            font-weight: bold;
        }
        .cart-item-price {
            font-size: 1.2em;
        }
        .btn-proceed {
            display: block;
            width: 100%;
            padding: 10px;
            text-align: center;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
        }
        .quantity-controls input {
            width: 50px;
            text-align: center;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li class="nav-left"><a href="index.php"><img src="logo.png" alt="Logo" class="logo"></a></li>
            <li><a href="index.php">Home</a></li>
            <li><a href="View_books.php">View Books</a></li>
            <li class="nav-right"><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="cart-container mt-5">
        <h2>Your Cart</h2>
        <div class="cart-items">
            <ul>
                <?php foreach ($cart_items as $item): ?>
                    <li>
                        <img src="<?= htmlspecialchars($item['picture']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                        <div class="cart-item-details">
                            <div class="cart-item-title"><?= htmlspecialchars($item['title']) ?></div>
                            <div class="cart-item-price">
                                <?php if ($item['book_type'] === 'hardcopy'): ?>
                                    Hardcopy - $<?= number_format($item['price_hardcopy'], 2) ?>
                                <?php elseif ($item['book_type'] === 'ebook'): ?>
                                    eBook - $<?= number_format($item['price_ebook'], 2) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="quantity-controls">
                            <form action="cart.php" method="post">
                                <input type="hidden" name="book_id" value="<?= $item['id']; ?>">
                                <input type="hidden" name="action" value="decrease">
                                <button type="submit" class="btn btn-outline-secondary btn-sm">-</button>
                            </form>
                            <input type="text" value="<?= htmlspecialchars($item['quantity']); ?>" readonly>
                            <form action="cart.php" method="post">
                                <input type="hidden" name="book_id" value="<?= $item['id']; ?>">
                                <input type="hidden" name="action" value="increase">
                                <button type="submit" class="btn btn-outline-secondary btn-sm">+</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($cart_items)): ?>
                    <li>Your cart is empty.</li>
                <?php endif; ?>
            </ul>
            <h3>Total: $<?= number_format($total_price, 2) ?></h3>
            <a href="proceed_to_pay.php" class="btn btn-success btn-proceed">Proceed to Pay</a>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-container">
            <p>&copy; 2024 Textbook Exchange. All rights reserved.</p>
            <ul class="footer-links">
                <li><a href="#about">About Us</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#contact">Contact Us</a></li>
            </ul>
        </div>
    </footer>

</body>
</html>
