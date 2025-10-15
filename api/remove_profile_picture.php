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
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$userId = $_SESSION['user_id'] ?? 7;
$deletedFile = false;

try {
    // Get current profile picture path
    $sql = "SELECT profile_picture FROM accounts_tbl WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $profilePicturePath = $user['profile_picture'];
        
        if (!empty($profilePicturePath)) {
            // Construct full file path
            $fullPath = '../' . ltrim($profilePicturePath, '/');
            
            error_log("Attempting to delete profile picture: " . $fullPath);
            
            // Delete the file if it exists
            if (file_exists($fullPath)) {
                if (is_file($fullPath)) {
                    if (unlink($fullPath)) {
                        $deletedFile = true;
                        error_log("Successfully deleted profile picture: " . $fullPath);
                    } else {
                        error_log("Failed to delete file (unlink failed): " . $fullPath);
                    }
                } else {
                    error_log("Path exists but is not a file: " . $fullPath);
                }
            } else {
                error_log("File does not exist: " . $fullPath);
            }
        } else {
            error_log("No profile picture path found in database for user: " . $userId);
        }
    } else {
        error_log("User not found in database: " . $userId);
    }
    $stmt->close();
    
    // Update database to remove profile picture reference
    $sql = "UPDATE accounts_tbl SET profile_picture = NULL WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        $message = $deletedFile 
            ? 'Profile photo removed successfully' 
            : 'Profile photo reference removed (file was not found)';
        
        echo json_encode([
            'status' => 'success',
            'message' => $message
        ]);
    } else {
        error_log("Database update failed: " . $stmt->error);
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to remove profile photo from database'
        ]);
    }

    $stmt->close();
    
} catch (Exception $e) {
    error_log("Exception in remove_profile_picture.php for user $userId: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An unexpected error occurred'
    ]);
}

$conn->close();
?>