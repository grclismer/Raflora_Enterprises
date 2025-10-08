<?php
session_start();
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$userId = $_SESSION['user_id'] ?? 7;

// Remove profile picture from database (set to NULL)
$sql = "UPDATE accounts_tbl SET profile_picture = NULL WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Profile photo removed successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to remove profile photo from database'
    ]);
}

$stmt->close();
$conn->close();
?>