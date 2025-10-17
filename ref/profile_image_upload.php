<?php
// =======================================================================
// PHP SCRIPT START - TIMEZONE CORRECTION
// =======================================================================

// Example: Set the timezone to Manila (Philippines Standard Time)
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');
ini_set('display_errors', 0);

// Check if file was uploaded
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    
    $uploadDir = '../ref/uploads/profile_pictures';
    
    // Create uploads directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileName = uniqid() . '_' . $_FILES['profile_picture']['name'];
    $filePath = $uploadDir . $fileName;
    
    // Move uploaded file
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Profile picture uploaded successfully',
            'file_path' => $filePath
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'error' => 'Failed to save file'
        ]);
    }
} else {
    $error = 'No file uploaded';
    if (isset($_FILES['profile_picture'])) {
        $error = 'Upload error code: ' . $_FILES['profile_picture']['error'];
    }
    echo json_encode([
        'success' => false, 
        'error' => $error
    ]);
}
?>