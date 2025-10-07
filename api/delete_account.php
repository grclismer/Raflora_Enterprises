<?php
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
    echo json_encode(['status' => 'error', 'message' => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Delete user account
$stmt = $conn->prepare("DELETE FROM accounts_tbl WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // Destroy session and redirect
    session_destroy();
    echo json_encode(['status' => 'success', 'message' => 'Account deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete account']);
}

$stmt->close();
$conn->close();
?>