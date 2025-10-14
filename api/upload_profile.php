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

// Function to delete profile picture file
function deleteProfilePictureFile($filePath) {
    if (!empty($filePath)) {
        $fullPath = '../' . ltrim($filePath, '/');
        if (file_exists($fullPath) && is_file($fullPath)) {
            if (unlink($fullPath)) {
                error_log("Deleted old profile picture: " . $fullPath);
                return true;
            } else {
                error_log("Failed to delete old profile picture: " . $fullPath);
            }
        }
    }
    return false;
}

// Handle profile picture upload if provided
$profilePicturePath = null;

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    // First, get the current profile picture to delete it later
    $oldPicturePath = null;
    $stmt = $conn->prepare("SELECT profile_picture FROM accounts_tbl WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $oldPicturePath = $user['profile_picture'];
    }
    $stmt->close();

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
            $profilePicturePath = 'uploads/profile_pictures/' . $fileName;
            
            // Delete the OLD profile picture file after successful upload
            if ($oldPicturePath && $oldPicturePath !== $profilePicturePath) {
                deleteProfilePictureFile($oldPicturePath);
            }
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