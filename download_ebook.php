<?php
session_start();

if (!isset($_GET['file'])) {
    header("Location: view_books.php");
    exit();
}

$file = urldecode($_GET['file']);
$filepath = 'uploads/ebooks/' . $file;

if (file_exists($filepath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit();
} else {
    echo "File not found.";
}
?>
