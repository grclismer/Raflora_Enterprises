<?php

// =======================================================================
// PHP SCRIPT START - TIMEZONE CORRECTION
// =======================================================================

// Example: Set the timezone to Manila (Philippines Standard Time)
date_default_timezone_set('Asia/Manila');


require_once 'vendor/autoload.php'; // You'll need to install QR code library

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

session_start();
header('Content-Type: image/png');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the latest QR session for this user
$stmt = $conn->prepare("SELECT qr_code_data FROM qr_login_sessions WHERE user_id = ? AND status = 'pending' AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $qr_data = $row['qr_code_data'];
    
    // Generate QR code
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($qr_data)
        ->size(300)
        ->build();
    
    echo $result->getString();
} else {
    http_response_code(404);
}

$stmt->close();
$conn->close();
?>