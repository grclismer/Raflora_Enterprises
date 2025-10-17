<?php

// =======================================================================
// PHP SCRIPT START - TIMEZONE CORRECTION
// =======================================================================

// Example: Set the timezone to Manila (Philippines Standard Time)
date_default_timezone_set('Asia/Manila');

// Turn off error display but log them
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => "Database connection failed"]);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get POST data
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';

// Validate
if (empty($current_password) || empty($new_password)) {
    echo json_encode(['status' => 'error', 'message' => 'Current password and new password are required']);
    exit();
}

// Get current password hash
$stmt = $conn->prepare("SELECT password FROM accounts_tbl WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($hashed_password);
$stmt->fetch();
$stmt->close();

// Verify current password
if (!password_verify($current_password, $hashed_password)) {
    echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
    exit();
}

// Hash new password
$new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password
$stmt = $conn->prepare("UPDATE accounts_tbl SET password = ? WHERE user_id = ?");
$stmt->bind_param("si", $new_hashed_password, $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
}

$stmt->close();
$conn->close();
?>