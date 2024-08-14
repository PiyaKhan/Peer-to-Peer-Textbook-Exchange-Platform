<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price_ebook = isset($_POST['price_ebook']) ? $_POST['price_ebook'] : null;
    $price_hardcopy = isset($_POST['price_hardcopy']) ? $_POST['price_hardcopy'] : null;
    $details = $_POST['details'];
    $ebook_file_path = null;

    // Handle eBook upload
    if (isset($_FILES['ebook']) && $_FILES['ebook']['error'] == 0) {
        $target_dir = "uploads/ebooks/";
        $target_file = $target_dir . basename($_FILES["ebook"]["name"]);
        if (move_uploaded_file($_FILES["ebook"]["tmp_name"], $target_file)) {
            $ebook_file_path = $target_file;
        }
    }

    // Insert book details into the database
    $sql = "INSERT INTO books (title, author, price_ebook, price_hardcopy, details, ebook_file_path) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssddss", $title, $author, $price_ebook, $price_hardcopy, $details, $ebook_file_path);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: view_books.php");
    exit();
}
?>
