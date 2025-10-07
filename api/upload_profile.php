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

// Check required fields
if (empty($_POST['firstname']) || empty($_POST['lastname']) || empty($_POST['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'First name, last name, and email are required']);
    exit;
}

$userId = $_SESSION['user_id'] ?? 7;

// Handle profile picture upload if provided
$profilePicturePath = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    $uploadDir = '../uploads/profile_pictures/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = $_FILES['profile_picture']['type'];
    
    if (in_array($fileType, $allowedTypes)) {
        // Generate unique filename
        $fileExtension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $fileName = 'profile_' . $userId . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
            // CORRECT PATH: This should be the web-accessible path
            $profilePicturePath = 'uploads/profile_pictures/' . $fileName;
        }
    }
}

// Update user data in database
if ($profilePicturePath) {
    // Update WITH profile picture
    $sql = "UPDATE accounts_tbl SET first_name = ?, last_name = ?, email = ?, mobile_number = ?, address = ?, profile_picture = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['phone'], $_POST['address'], $profilePicturePath, $userId);
} else {
    // Update WITHOUT profile picture (keep existing)
    $sql = "UPDATE accounts_tbl SET first_name = ?, last_name = ?, email = ?, mobile_number = ?, address = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['phone'], $_POST['address'], $userId);
}

if ($stmt->execute()) {
    $message = $profilePicturePath ? 'Profile updated successfully with new profile picture' : 'Profile updated successfully';
    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'profile_picture' => $profilePicturePath
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update database: ' . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>