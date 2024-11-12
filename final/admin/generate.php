<?php
require "vendor/autoload.php";
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

header('Content-Type: application/json'); // Set JSON header

if (isset($_POST['qr_text'])) {
    $text = trim($_POST['qr_text']);
    // Sanitize the input
    if (!preg_match('/^[a-zA-Z0-9_ ]+$/', $text)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid QR text.']);
        exit;
    }

    // Define the QR code URL
    $qr_value = "http://127.0.0.1/final/index.php?tno=" . urlencode($text);

    // Create the QR code
    $qr = QrCode::create($qr_value);
    $writer = new PngWriter();

    // Ensure the qrcodes directory exists
    $directory = "qrcodes";
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    // Save the QR code to a file
    $fileName = $text . ".png";
    $filePath = $directory . "/" . $fileName;
    try {
        $writer->write($qr)->saveToFile($filePath);
        echo json_encode(['status' => 'success', 'fileName' => $fileName]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No QR text provided.']);
}
