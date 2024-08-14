<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM books";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <nav>
        <ul>
            <li class="nav-left"><a href="index.php"><img src="logo.png" alt="Logo" class="logo"></a></li>
            <li><a href="index.php">Home</a></li>
            <li><a href="#about">About Us</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#contact">Contact Us</a></li>
            <li><a href="cart.php">View Cart</a></li>
            <li class="nav-right"><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <h2>My Listed Books</h2>
    <div class="container mt-4">
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $bookId = htmlspecialchars($row["id"]);
                    $ebookPrice = htmlspecialchars($row["price_ebook"]);
                    $hardcopyPrice = htmlspecialchars($row["price_hardcopy"]);

                    echo '<div class="col-md-4 mb-4">';
                    echo '<div class="book-container p-3 border h-100">';
                    echo "<h4>" . htmlspecialchars($row["title"]) . "</h4>";
                    echo "<p><strong>Author:</strong> " . htmlspecialchars($row["author"]) . "</p>";
                    echo '<img src="' . htmlspecialchars($row["picture"]) . '" alt="Book Picture" class="img-fluid mb-3" style="max-height: 200px;margin-left:60px;">';
                    echo "<p><strong>Price (eBook):</strong> $" . $ebookPrice . "</p>";
                    echo "<p><strong>Price (Hard Copy):</strong> $" . $hardcopyPrice . "</p>";
                    echo '<a href="book_details.php?id=' . $bookId . '&type=ebook" class="btn btn-primary w-100 mb-2">Buy eBook</a>';
                    echo '<a href="book_details.php?id=' . $bookId . '&type=hardcopy" class="btn btn-secondary w-100">Buy Hard Copy</a>';
                    echo "</div>";
                    echo '</div>';
                }
            } else {
                echo "<p>No books available</p>";
            }
            // $stmt->close();
            // $conn->close();
            ?>
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
        function addToCart(bookId, type) {
            // Assume quantity is 1 for simplicity; adjust as needed
            let quantity = 1;
            window.location.href = `add_to_cart.php?id=${bookId}&type=${type}&quantity=${quantity}`;
        }
    </script>

</body>
</html>
