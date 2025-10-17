<?php

// =======================================================================
// PHP SCRIPT START - TIMEZONE CORRECTION
// =======================================================================

// Example: Set the timezone to Manila (Philippines Standard Time)
date_default_timezone_set('Asia/Manila');


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set the content type to JSON to match your JavaScript's fetch request
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

    $regUsername = $_POST['username'] ?? '';
    $regFirstname = $_POST['firstname'] ?? '';
    $regLastname = $_POST['lastname'] ?? '';
    $regMobile = $_POST['mobilenumber'] ?? '';
    $regAddress = $_POST['address'] ?? '';
    $regEmail = $_POST['email'] ?? '';
    $regPassword = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Check if all required fields are present and not empty
    if (empty($regUsername) || empty($regFirstname) || empty($regLastname) || empty($regMobile) || empty($regAddress) || empty($regEmail) || empty($regPassword) || empty($confirm_password)) {
        echo json_encode(['status' => 'error', 'message' => "All fields are required."]);
        exit();
    }

    // Check if passwords match
    if ($regPassword !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => "Passwords do not match!"]);
        exit();
    }

    // Hardcode the role here
    $role = 'client_type';

    // Check if the username or email already exists to prevent duplicate entries
    $stmt = $conn->prepare("SELECT user_id FROM accounts_tbl WHERE user_name = ? OR email = ?");
    $stmt->bind_param("ss", $regUsername, $regEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => "Username or email already exists. Please choose another."]);
        exit();
    }
    $stmt->close();

    // Hash the password securely before saving
    $hashed_password = password_hash($regPassword, PASSWORD_DEFAULT);

    // Prepare and execute the INSERT statement with the 'role' column
    $stmt = $conn->prepare("INSERT INTO accounts_tbl (first_name, last_name, user_name, email, password, mobile_number, address, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $regFirstname, $regLastname, $regUsername, $regEmail, $hashed_password, $regMobile, $regAddress, $role);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful! You can now log in.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Error: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();