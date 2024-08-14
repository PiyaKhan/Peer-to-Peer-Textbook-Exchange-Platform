<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php'; // Ensure you have this file to connect to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookTitle = $_POST['bookTitle'];
    $author = $_POST['author'];
    $priceEbook = $_POST['priceEbook'];
    $priceHardcopy = $_POST['priceHardcopy'];
    $details = $_POST['details']; // New field for additional details

    // Handle file upload
    $picture = $_FILES['picture']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($picture);

    if (move_uploaded_file($_FILES['picture']['tmp_name'], $target_file)) {
        // File uploaded successfully
        $sql = "INSERT INTO books (user_id, title, author, picture, price_ebook, price_hardcopy, details) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("isssdds", $_SESSION['user_id'], $bookTitle, $author, $target_file, $priceEbook, $priceHardcopy, $details);
        if ($stmt->execute()) {
            header("Location: add_books.php?success=1");
        } else {
            header("Location: add_books.php?error=" . urlencode("Failed to add book."));
        }
        $stmt->close();
    } else {
        header("Location: add_books.php?error=" . urlencode("Failed to upload picture."));
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a Book</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<style>
    /* Add a Book Page Styles */
.add-book-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.add-book-group {
    margin-bottom: 20px;
}

.add-book-label {
    font-weight: bold;
    margin-bottom: 10px;
    color: #555;
    display: block;
}

.add-book-input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    font-size: 1rem;
}

.add-book-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

.add-book-file-input {
    padding: 0; /* Remove default padding */
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fff;
    font-size: 1rem;
}

.add-book-file-input::file-selector-button {
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 8px 16px;
    cursor: pointer;
}

.add-book-file-input::file-selector-button:hover {
    background-color: #0056b3;
}

</style>

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

    <div class="add-book-container mt-5">
    <h2 class="add-book-title">Add a Book</h2>
    <form action="add_books.php" method="post" enctype="multipart/form-data" class="add-book-form">
        <div class="add-book-group mb-3">
            <label for="bookTitle" class="add-book-label">Book Title</label>
            <input type="text" class="form-control add-book-input" id="bookTitle" name="bookTitle" required>
        </div>
        <div class="add-book-group mb-3">
            <label for="author" class="add-book-label">Author</label>
            <input type="text" class="form-control add-book-input" id="author" name="author" required>
        </div>
        <div class="add-book-group mb-3">
            <label for="picture" class="add-book-label">Picture</label>
            <input type="file" class="form-control-file add-book-input" id="picture" name="picture" required>
        </div>
        <div class="add-book-group mb-3">
            <label for="priceEbook" class="add-book-label">eBook Price</label>
            <input type="number" step="0.01" class="form-control add-book-input" id="priceEbook" name="priceEbook" required>
        </div>
        <div class="add-book-group mb-3">
            <label for="priceHardcopy" class="add-book-label">Hard Copy Price</label>
            <input type="number" step="0.01" class="form-control add-book-input" id="priceHardcopy" name="priceHardcopy" required>
        </div>
        <div class="add-book-group mb-3">
            <label for="details" class="add-book-label">Book Details</label>
            <textarea class="form-control add-book-input" id="details" name="details" rows="3" placeholder="Enter additional details about the book" required></textarea>
        </div>
        <div class="add-book-group mb-3">
            <label for="ebook" class="add-book-label">eBook File (optional):</label>
            <input type="file" id="ebook" name="ebook" accept=".pdf,.epub" class="add-book-input"><br>
        </div>
        <button type="submit" class="btn btn-primary add-book-button">Add Book</button>
    </form>
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
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Book added successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Clear query parameters after alert
                    window.history.replaceState({}, document.title, window.location.pathname);
                });
            } 
        });
    </script>
</body>
</html>
