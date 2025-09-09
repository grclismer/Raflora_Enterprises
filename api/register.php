<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "0bdb-login_form";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
        die("Error: All fields are required.");
    }

    // Check if passwords match
    if ($regPassword !== $confirm_password) {
        die("Error: Passwords do not match!");
    }

    // Check if the username or email already exists to prevent duplicate entries
    $stmt = $conn->prepare("SELECT id FROM login_tbl WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $regUsername, $regEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        die("Error: Username or email already exists. Please choose another.");
    }
    $stmt->close();

    // Hash the password securely before saving
    $hashed_password = password_hash($regPassword, PASSWORD_DEFAULT);

    // Prepare and execute the INSERT statement with all fields
    $stmt = $conn->prepare("INSERT INTO login_tbl (first_name, last_name, username, email, password, mobile_number, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $regFirstname, $regLastname, $regUsername, $regEmail, $hashed_password, $regMobile, $regAddress);

    if ($stmt->execute()) {
        echo "Registration successful! You can now log in.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>