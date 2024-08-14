<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OCR Service</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background-color:#d8d0aa">
    <style>
        /* OCR Service Container */
        .ocr-container {
            width: 40%;
            margin: auto;
            margin-top:10px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .ocr-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }

        /* OCR Form */
        .ocr-form {
            width: 100%;
        }

        .ocr-form label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #555;
        }

        .ocr-form input[type="file"] {
            /* width: 100%; */
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .ocr-form .btn-primary {
            display: block;
            width: 40%;
            margin:auto;
            padding: 10px;
            background-color: #582f2f;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .ocr-form .btn-primary:hover {
            background-color: #2980b9;
        }
    </style>

    <nav>
        <ul>
            <li class="nav-left"><a href="index.php"><img src="logo.png" alt="Logo" class="logo"></a></li>
            <li><a href="index.php">Home</a></li>
            <li><a href="#about">About Us</a></li>
            <!-- <li><a href="#services">Services</a></li> -->
            <li><a href="#contact">Contact Us</a></li>
            <li class="nav-right"><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="ocr-container" s>
        <h2>OCR Service</h2>
        <form action="ocr.php" method="post" enctype="multipart/form-data" class="ocr-form">
            <div style="90%">
                <label for="ocr_image">Upload Image</label>
                <input type="file" name="ocr_image" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Convert to Text</button>
        </form>

        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['ocr_image'])) {
            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . basename($_FILES['ocr_image']['name']);

            if (move_uploaded_file($_FILES['ocr_image']['tmp_name'], $uploadFile)) {
                // API key for OCR.space
                $apiKey = 'K84119785588957';

                // Prepare the image file for sending
                $imageFile = new CURLFile($uploadFile);

                // Prepare data for POST request
                $postData = [
                    'apikey' => $apiKey,
                    'file' => $imageFile,
                    'language' => 'eng' // Optional: specify the language of the OCR
                ];

                // Send POST request to OCR.space API
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.ocr.space/parse/image');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

                $response = curl_exec($ch);
                curl_close($ch);

                // Parse the JSON response
                $responseData = json_decode($response, true);
                $text = $responseData['ParsedResults'][0]['ParsedText'] ?? 'No text detected.';

                // Display the extracted text
                echo '<h3>Extracted Text:</h3>';
                echo '<pre>' . htmlspecialchars($text) . '</pre>';

                // Provide a button to download the text as a file
                echo '
                    <form id="downloadForm" method="post">
                        <input type="hidden" name="downloadText" value="' . htmlspecialchars($text) . '">
                        <button type="button" class="btn btn-success mt-3" onclick="downloadTextFile()">Download Text File</button>
                    </form>
                ';
            } else {
                echo '<p>There was an error uploading the file.</p>';
            }
        }
        ?>
    </div>

    <script>
        function downloadTextFile() {
            var form = document.getElementById('downloadForm');
            var text = form.elements['downloadText'].value;
            var blob = new Blob([text], { type: 'text/plain' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'ocr_result.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
