<?php

// =======================================================================
// PHP SCRIPT START - TIMEZONE CORRECTION
// =======================================================================

// Example: Set the timezone to Manila (Philippines Standard Time)
date_default_timezone_set('Asia/Manila');


session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$qr_data = $input['qr_data'] ?? '';

if (empty($qr_data)) {
    echo json_encode(['status' => 'error', 'message' => 'QR data required']);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => "Database connection failed"]);
    exit();
}

// Parse QR data
$qr_object = json_decode($qr_data, true);

if (!isset($qr_object['user_id']) || $qr_object['system'] !== 'raflora_enterprises') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid QR code']);
    exit();
}

$user_id = $qr_object['user_id'];

// Get user data
$stmt = $conn->prepare("SELECT user_id, user_name, role FROM accounts_tbl WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Set session for the logged-in user
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['user_name'];
    $_SESSION['is_logged_in'] = true;
    $_SESSION['role'] = $user['role'];
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Login successful',
        'user' => [
            'user_id' => $user['user_id'],
            'username' => $user['user_name'],
            'role' => $user['role']
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}

$stmt->close();
$conn->close();
?>