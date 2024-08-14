<?php
session_start();
require 'config.php';

$user_id = $_SESSION['user_id'];
$cart_items = [];
$total_price = 0.0;

// Fetch cart items with both prices
$stmt = $conn->prepare("
    SELECT b.id, b.title, b.price_hardcopy, b.price_ebook, c.quantity, c.book_type
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
    <title>Proceed to Pay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 800px;
            background: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        h2, h4 {
            color: #333;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .cart-summary {
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }

        .cart-summary ul {
            list-style: none;
            padding: 0;
        }

        .cart-summary ul li {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .cart-summary h3 {
            font-size: 24px;
            color: #e47911;
            margin-top: 20px;
        }

        .payment-options h4 {
            font-size: 20px;
            margin-bottom: 15px;
        }

        .form-check-label {
            font-size: 18px;
            margin-left: 10px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .form-check-input {
            margin-top: 6px;
            margin-left: 3px;
            cursor: pointer;
        }

        .payment-details {
            display: none;
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f8f8f8;
        }

        .payment-details label {
            font-size: 16px;
            color: #555;
            margin-bottom: 5px;
        }

        .payment-details input {
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .card-details .form-group {
            margin-bottom: 1rem;
        }

        #confirm-order {
            background-color: #e47911;
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #confirm-order:hover {
            background-color: #c46607;
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

    <div class="container mt-5">
        <div class="cart-summary">
            <h4>Cart Summary</h4>
            <ul>
                <?php foreach ($cart_items as $item): ?>
                    <li><?= htmlspecialchars($item['title']) ?> - 
                        <?php if ($item['book_type'] === 'hardcopy'): ?>
                            Hardcopy - $<?= number_format($item['price_hardcopy'], 2) ?> x <?= htmlspecialchars($item['quantity']) ?>
                        <?php elseif ($item['book_type'] === 'ebook'): ?>
                            eBook - $<?= number_format($item['price_ebook'], 2) ?> x <?= htmlspecialchars($item['quantity']) ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <h3>Total: $<?= number_format($total_price, 2) ?></h3>
        </div>

        <div class="payment-options">
            <h4>Select Payment Method</h4>
            <form id="payment-form">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="cash-on-delivery" value="cash-on-delivery">
                    <label class="form-check-label" for="cash-on-delivery">
                        Cash on Delivery
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="upi" value="upi">
                    <label class="form-check-label" for="upi">
                        UPI
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                    <label class="form-check-label" for="card">
                        Card
                    </label>
                </div>
                <div id="payment-details" class="payment-details">
                    <div id="upi-details" class="upi-details">
                        <label for="upi-id">Enter UPI ID:</label>
                        <input type="text" id="upi-id" class="form-control" placeholder="example@upi">
                    </div>
                    <div id="card-details" class="card-details">
                        <div class="form-group">
                            <label for="card-number">Card Number:</label>
                            <input type="text" id="card-number" class="form-control" placeholder="**** **** **** ****">
                        </div>
                        <div class="form-group">
                            <label for="card-expiry">Expiry Date:</label>
                            <input type="text" id="card-expiry" class="form-control" placeholder="MM/YY">
                        </div>
                        <div class="form-group">
                            <label for="card-cvv">CVV:</label>
                            <input type="text" id="card-cvv" class="form-control" placeholder="***">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success mt-3" id="confirm-order">Confirm Order</button>
            </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const paymentForm = document.getElementById('payment-form');
            const paymentDetails = document.getElementById('payment-details');
            const upiDetails = document.getElementById('upi-details');
            const cardDetails = document.getElementById('card-details');
            const confirmOrderButton = document.getElementById('confirm-order');

            paymentForm.addEventListener('change', (event) => {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                if (selectedMethod) {
                    const methodValue = selectedMethod.value;
                    if (methodValue === 'upi') {
                        upiDetails.style.display = 'block';
                        cardDetails.style.display = 'none';
                    } else if (methodValue === 'card') {
                        upiDetails.style.display = 'none';
                        cardDetails.style.display = 'block';
                    } else {
                        upiDetails.style.display = 'none';
                        cardDetails.style.display = 'none';
                    }
                    paymentDetails.style.display = 'block';
                }
            });

            confirmOrderButton.addEventListener('click', () => {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                if (selectedMethod) {
                    const methodValue = selectedMethod.value;
                    if (methodValue === 'cash-on-delivery') {
                        Swal.fire('Order Confirmed', 'Your order will be delivered to your address.', 'success');
                    } else if (methodValue === 'upi') {
                        const upiId = document.getElementById('upi-id').value;
                        if (upiId.trim() === '') {
                            Swal.fire('Error', 'Please enter your UPI ID.', 'error');
                            return;
                        }
                        Swal.fire('Order Confirmed', 'Your order will be processed. Thank you!', 'success');
                    } else if (methodValue === 'card') {
                        const cardNumber = document.getElementById('card-number').value;
                        const cardExpiry = document.getElementById('card-expiry').value;
                        const cardCvv = document.getElementById('card-cvv').value;
                        if (cardNumber.trim() === '' || cardExpiry.trim() === '' || cardCvv.trim() === '') {
                            Swal.fire('Error', 'Please fill out all card details.', 'error');
                            return;
                        }
                        Swal.fire('Order Confirmed', 'Your order will be processed. Thank you!', 'success');
                    }
                } else {
                    Swal.fire('Error', 'Please select a payment method.', 'error');
                }
            });
        });
    </script>
</body>
</html>
