<?php
// update_account.php
session_start();

// Set timezone
date_default_timezone_set('Asia/Manila');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $_SESSION['error'] = "Database connection failed: " . $conn->connect_error;
    header("Location: account_settings.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "User not logged in";
    header("Location: ../guest/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get form data
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_new_password = $_POST['confirm_new_password'] ?? '';

// Validate required fields
if (empty($firstname) || empty($lastname) || empty($email)) {
    $_SESSION['error'] = "First name, last name, and email are required.";
    header("Location: account_settings.php");
    exit();
}

// Check if email already exists (excluding current user)
$check_stmt = $conn->prepare("SELECT user_id FROM accounts_tbl WHERE email = ? AND user_id != ?");
$check_stmt->bind_param("si", $email, $user_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    $_SESSION['error'] = "Email already exists. Please use a different email.";
    $check_stmt->close();
    header("Location: account_settings.php");
    exit();
}
$check_stmt->close();

// Check if password change is requested
$password_change_requested = !empty($current_password) || !empty($new_password) || !empty($confirm_new_password);

if ($password_change_requested) {
    // Validate password fields
    if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
        $_SESSION['error'] = "All password fields are required when changing password.";
        header("Location: account_settings.php");
        exit();
    }
    
    // Check if new passwords match
    if ($new_password !== $confirm_new_password) {
        $_SESSION['error'] = "New passwords do not match.";
        header("Location: account_settings.php");
        exit();
    }
    
    // Check password strength
    if (strlen($new_password) < 6) {
        $_SESSION['error'] = "New password must be at least 6 characters long.";
        header("Location: account_settings.php");
        exit();
    }
    
    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM accounts_tbl WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();
    
    if (!password_verify($current_password, $hashed_password)) {
        $_SESSION['error'] = "Current password is incorrect.";
        header("Location: account_settings.php");
        exit();
    }
    
    // Hash new password
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update with password
    $update_stmt = $conn->prepare("UPDATE accounts_tbl SET first_name = ?, last_name = ?, email = ?, mobile_number = ?, address = ?, password = ? WHERE user_id = ?");
    $update_stmt->bind_param("ssssssi", $firstname, $lastname, $email, $phone, $address, $new_hashed_password, $user_id);
} else {
    // Update without password
    $update_stmt = $conn->prepare("UPDATE accounts_tbl SET first_name = ?, last_name = ?, email = ?, mobile_number = ?, address = ? WHERE user_id = ?");
    $update_stmt->bind_param("sssssi", $firstname, $lastname, $email, $phone, $address, $user_id);
}

// Execute the update
if ($update_stmt->execute()) {
    if ($password_change_requested) {
        $_SESSION['success'] = "Profile and password updated successfully!";
    } else {
        $_SESSION['success'] = "Profile updated successfully!";
    }
} else {
    $_SESSION['error'] = "Error updating account: " . $update_stmt->error;
}

$update_stmt->close();
$conn->close();

// Redirect back to account settings
header("Location: account_settings.php");
exit();
?>