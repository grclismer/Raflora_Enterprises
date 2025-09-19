<?php
session_start();
require_once 'security.php';

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => "Connection failed: " . $conn->connect_error]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['csrf_token'] ?? '';

    // Corrected CSRF check: pass the form name 'forgot'
    if (!verify_csrf_token('forgot', $token)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
        exit();
    }

    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => "Email is required."]);
        exit();
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM accounts_tbl WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => "No account found with this email."]);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Generate reset token
    $reset_token = bin2hex(random_bytes(16));
    $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Save token to DB
    $stmt = $conn->prepare("UPDATE accounts_tbl SET password_reset_token = ?, password_reset_expires = ? WHERE email = ?");
    $stmt->bind_param("sss", $reset_token, $expires, $email);
    $stmt->execute();
    $stmt->close();

    // For now: just return the reset link in JSON
    $reset_link = "http://localhost/Raflora_Enterprises/user/reset_password.php?token=$reset_token";

    echo json_encode(['status' => 'success', 'message' => "Password reset link generated.", 'reset_link' => $reset_link]);
}
$conn->close();
?>