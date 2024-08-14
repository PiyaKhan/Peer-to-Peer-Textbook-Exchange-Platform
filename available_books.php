<?php
// session_start();
require_once 'config.php';

// Fetch books from the database
$sql = "SELECT * FROM books";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Books</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="available-books">
        <!-- <h2>Available Books</h2> -->
        <div class="book-showcase">
            <div class="book-list" id="bookList">
                <?php if ($result->num_rows > 0) : ?>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <div class="book-item">
                            <img src="<?= htmlspecialchars($row['picture']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                            <h5><?= htmlspecialchars($row['title']) ?></h5>
                            <p><?= htmlspecialchars($row['author']) ?></p>
                            <p class="price">$<?= htmlspecialchars($row['price_ebook']) ?></p>
                            <button class="btn btn-primary" onclick="buyNow(<?= $row['id'] ?>)">Buy Now</button>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p>No books available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function buyNow(bookId) {
            window.location.href = `book_details.php?id=${bookId}&type=ebook`;
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
