<?php

// =======================================================================
// PHP SCRIPT START - TIMEZONE CORRECTION
// =======================================================================

// Example: Set the timezone to Manila (Philippines Standard Time)
date_default_timezone_set('Asia/Manila');

session_start();
header('Content-Type: image/png');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    exit();
}

// Get user_id from URL parameter
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    http_response_code(400);
    exit();
}

// Verify user exists
$stmt = $conn->prepare("SELECT user_id FROM accounts_tbl WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    exit();
}

// Create QR data
$qr_data = json_encode([
    'user_id' => $user_id,
    'system' => 'raflora_enterprises',
    'method' => 'qr_login'
]);

// Use QR Server API (alternative to Google)
$qr_size = 300;
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size={$qr_size}x{$qr_size}&data=" . urlencode($qr_data);

// Get QR code image
$qr_image = file_get_contents($qr_url);

if ($qr_image === FALSE) {
    // Final fallback: Simple text image
    $im = imagecreate($qr_size, $qr_size);
    $background = imagecolorallocate($im, 255, 255, 255);
    $textcolor = imagecolorallocate($im, 0, 0, 0);
    imagestring($im, 5, 50, 150, "QR Code for User: " . $user_id, $textcolor);
    imagepng($im);
    imagedestroy($im);
} else {
    // Handle download request
    if (isset($_GET['download'])) {
        header('Content-Disposition: attachment; filename="raflora-login-qr-' . $user_id . '.png"');
    }
    echo $qr_image;
}

$stmt->close();
$conn->close();
?>