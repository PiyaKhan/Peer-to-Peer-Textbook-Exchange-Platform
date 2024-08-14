<?php
session_start();
require_once 'config.php';

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: view_books.php");
    exit();
}

$book_id = intval($_GET['id']);
$type = $_GET['type'];

$sql = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$book) {
    header("Location: view_books.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
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

    <div class="book-details-container mt-5">
        <h2>Book Details</h2>
        <div class="book-details">
            <div class="book-image">
                <img src="<?= htmlspecialchars($book['picture']); ?>" alt="Book Picture">
            </div>
            <div class="details">
                <h5><?= htmlspecialchars($book['title']); ?></h5>
                <p><strong>Author:</strong> <?= htmlspecialchars($book['author']); ?></p>
                <p class="price"><strong>eBook Price:</strong> $<?= htmlspecialchars($book['price_ebook']); ?></p>
                <p class="price"><strong>Hard Copy Price:</strong> $<?= htmlspecialchars($book['price_hardcopy']); ?></p>
                <p><strong>Details:</strong> <?= htmlspecialchars($book['details']); ?></p>
                <p><strong>Type:</strong> <?= htmlspecialchars($type); ?></p>
                <div class="input-group mb-3">
                    <button class="btn btn-outline-secondary" type="button" onclick="decreaseQuantity()">-</button>
                    <input type="text" class="form-control text-center" id="quantity" value="1" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="increaseQuantity()">+</button>
                </div>
                <button class="btn btn-success" onclick="buyNow()">Buy Now</button>
            </div>
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

    <script>
        function decreaseQuantity() {
            let quantity = document.getElementById('quantity').value;
            if (quantity > 1) {
                document.getElementById('quantity').value = --quantity;
            }
        }

        function increaseQuantity() {
            let quantity = document.getElementById('quantity').value;
            document.getElementById('quantity').value = ++quantity;
        }

        function buyNow() {
            let quantity = document.getElementById('quantity').value;
            let bookId = <?= $book_id; ?>;
            let type = "<?= $type; ?>";
            window.location.href = `cart.php?id=${bookId}&type=${type}&quantity=${quantity}`;
        }
    </script>
</body>
</html>
