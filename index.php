<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';

// Initialize total_items variable
$total_items = 0;

// Check if the user is logged in and calculate the total items in the cart
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT SUM(quantity) as total_items FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($total_items);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Textbook Exchange</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body style="background-color:#d8d0aa">

<nav>
    <ul>
        <li class="nav-left"><a href="index.php"><img src="logo.png" alt="Logo" class="logo"></a></li>
        <li><a href="index.php">Home</a></li>
        <li><a href="#about">About Us</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#contact">Contact Us</a></li>
        <li><a href="cart.php">View Cart (<?= $total_items ?>)</a></li>
        <li class="nav-right"><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<header class="banner">
    <h1>Welcome to Textbook Exchange, <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</h1>        
    <p>Exchange textbooks with your peers easily and efficiently.</p>
</header>

<section id="about" class="about-us">
    <div class="about-container">
        <img src="images/about-us.jpg" alt="About Us" class="about-image">
        <div class="about-text">
            <h2>About Us</h2>
            <p>Welcome to our Textbook Exchange platform, where students can easily trade textbooks with each other. We aim to promote savings and sustainability by providing a simple, direct way for students to share resources. Join our community and help foster a collaborative learning environment.</p>
        </div>
    </div>
</section>

<section id="services" class="services">
    <h2>Services</h2>
    <div class="service-container">
        <div class="service">
            <h3>Book Listings</h3>
            <p>Post your textbooks for exchange or sale, and manage your listings effortlessly.</p>
            <form action="add_books.php" method="post">
                <button type="submit" class="btn btn-primary">List Books</button>
            </form>
        </div>
        <div class="service">
            <h3>View Books</h3>
            <p>Discover a wide range of textbooks available for exchange. Check the available books.</p>
            <a href="view_books.php" class="btn btn-primary">View Books</a>
        </div>
        <div class="service">
            <h3>Messaging</h3>
            <p>Communicate directly with other students to arrange exchanges. Stay updated.</p>
            <form action="chat.php" method="get">
                <button type="submit" class="btn btn-primary">Connect with Others</button>
            </form>
        </div>
        <div class="service">
            <h3>OCR Service</h3>
            <p>Extract text from images quickly and easily using our OCR service.</p>
            <form action="ocr.php" method="get">
                <button type="submit" class="btn btn-primary">Convert to OCR</button>
            </form>
        </div>
    </div>
</section>

<!-- Book Showcase Section -->
<section id="available-books" class="available-books">
    <h2>Available Books</h2>
    <?php include 'available_books.php'; ?>
</section>

<section id="testimonials" class="testimonials">
    <h2>Testimonials</h2>
    <div class="testimonial-container">
        <div class="testimonial">
            <img src="images/user-1.png" alt="">
            <p>"This platform has been a lifesaver for me! I've saved so much money by exchanging textbooks with my peers. Highly recommend!"</p>
            <h4>- Jane Doe</h4>
        </div>
        <div class="testimonial">
            <img src="images/user-2.png" alt="">
            <p>"A fantastic way to find and exchange textbooks. The process is smooth, and the community is very supportive."</p>
            <h4>- John Smith</h4>
        </div>
        <div class="testimonial">
            <img src="images/user-3.png" alt="">
            <p>"Using this platform has made getting textbooks for my classes so much easier. It's a great resource for students."</p>
            <h4>- Emily Johnson</h4>
        </div>
    </div>
</section>

<section id="contact" class="contact-us">
    <h2>Contact Us</h2>
    <div class="contact-container">
        <p>Have questions? Feel free to reach out to us by filling out the form below:</p>
        <form id="contactForm" action="process_contact.php" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
    </div>
</section>

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
    document.addEventListener("DOMContentLoaded", function() {
    window.addEventListener('scroll', function() {
        var aboutSection = document.querySelector('.about-container');
        var aboutImage = document.querySelector('.about-image');
        var aboutText = document.querySelector('.about-text');

        var sectionPosition = aboutSection.getBoundingClientRect().top;
        var screenPosition = window.innerHeight / 1.3;

        if (sectionPosition < screenPosition) {
            aboutImage.classList.add('scroll-in-left');
            aboutText.classList.add('scroll-in-right');
        }
    });
});

</script>

<script>
    let currentIndex = 0;
    const bookList = document.getElementById('bookList');
    const bookItems = document.querySelectorAll('.book-item');
    const itemsPerView = 4;
    const totalItems = bookItems.length;

    function showNextBooks() {
        currentIndex += itemsPerView;
        if (currentIndex >= totalItems) {
            currentIndex = 0;
        }
        const offset = -currentIndex * (100 / itemsPerView);
        bookList.style.transform = `translateX(${offset}%)`;
    }

    setInterval(showNextBooks, 5000);

    function addToCart(bookId, type) {
        let quantity = 1;
        window.location.href = `add_to_cart.php?id=${bookId}&type=${type}&quantity=${quantity}`;
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
