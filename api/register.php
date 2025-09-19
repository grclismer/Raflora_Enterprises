<?php
require_once 'security.php';
secure_session_start();

header('Content-Type: application/json');

// DB connection
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
    // CSRF check specific to the 'register' form
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token('register', $token)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
        exit();
    }

    // Get input safely
    $regUsername = trim($_POST['username'] ?? '');
    $regFirstname = trim($_POST['firstname'] ?? '');
    $regLastname  = trim($_POST['lastname'] ?? '');
    $regMobile    = trim($_POST['mobilenumber'] ?? '');
    $regAddress   = trim($_POST['address'] ?? '');
    $regEmail     = trim($_POST['email'] ?? '');
    $regPassword  = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validate required fields
    if (
        empty($regUsername) || empty($regFirstname) || empty($regLastname) ||
        empty($regMobile) || empty($regAddress) || empty($regEmail) ||
        empty($regPassword) || empty($confirm_password)
    ) {
        echo json_encode(['status' => 'error', 'message' => "All fields are required."]);
        exit();
    }

    // Check password match
    if ($regPassword !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => "Passwords do not match!"]);
        exit();
    }

    // Hardcoded role for new registrations
    $role = 'client_type';

    // Check for duplicates (username/email)
    $stmt = $conn->prepare("SELECT user_id FROM accounts_tbl WHERE user_name = ? OR email = ?");
    $stmt->bind_param("ss", $regUsername, $regEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => "Username or email already exists."]);
        exit();
    }
    $stmt->close();

    // Hash password
    $hashed_password = password_hash($regPassword, PASSWORD_DEFAULT);

    // Insert new user
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
?>