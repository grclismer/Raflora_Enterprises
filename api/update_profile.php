<?php
session_start();
header('Content-Type: application/json');

// Database connection
require_once '../config/database.php';

// Get user ID from session (you'll need to set this properly)
$userId = 7; // Replace with: $_SESSION['user_id'] 

try {
    // Check required fields
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email'])) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'First name, last name, and email are required'
        ]);
        exit;
    }

    // Handle profile picture upload
    $profilePicturePath = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['profile_picture']['type'];
        
        if (in_array($fileType, $allowedTypes)) {
            $fileName = uniqid() . '_' . $_FILES['profile_picture']['name'];
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
                $profilePicturePath = $filePath;
            }
        }
    }

    // Update user data in database
    $pdo->beginTransaction();
    
    if ($profilePicturePath) {
        // Update with profile picture
        $sql = "UPDATE accounts_tbl 
                SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, profile_picture = ? 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['first_name'],
            $_POST['last_name'], 
            $_POST['email'],
            $_POST['phone'] ?? null,
            $_POST['address'] ?? null,
            $profilePicturePath,
            $userId
        ]);
    } else {
        // Update without profile picture
        $sql = "UPDATE accounts_tbl 
                SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ? 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'], 
            $_POST['phone'] ?? null,
            $_POST['address'] ?? null,
            $userId
        ]);
    }

    // Handle password change if provided
    if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
        // Verify current password (you need to implement this)
        // $sql = "SELECT password FROM accounts_tbl WHERE id = ?";
        // If current password matches, update to new password
    }

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Profile updated successfully' . ($profilePicturePath ? ' with new profile picture' : ''),
        'profile_picture' => $profilePicturePath
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>