<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

function generateQR($number) {
    $directory = 'qrcodes/'; // Folder to save QR codes
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true); // Create directory if it doesn't exist
    }

    $filename = $directory . $number . '.png'; // Define file name
    $filePath = __DIR__ . '/' . $filename;

    // Generate QR code
    $qrCode = QrCode::create($number)->setSize(300)->setMargin(10);
    
    // Write QR Code
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    
    // Save QR code to file
    file_put_contents($filePath, $result->getString());

    // Return the URL to the generated QR code
    return 'https://' . $_SERVER['HTTP_HOST'] . '/' . $filename;
}

?>
