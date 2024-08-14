<?php
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List a Book</title>
    <link rel="stylesheet" href="styles.css">
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

    <h2>List a Book</h2>
    <form action="add_books.php" method="post">
        <label for="bookName">Book Name:</label>
        <input type="text" id="bookName" name="bookName" required><br>
        <label for="bookAuthor">Author:</label>
        <input type="text" id="bookAuthor" name="bookAuthor" required><br>
        <button type="submit">Add Book</button>
    </form>
</body>
</html>
