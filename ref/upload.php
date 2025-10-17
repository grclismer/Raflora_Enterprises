<?php
// =======================================================================
// PHP SCRIPT START - TIMEZONE CORRECTION
// =======================================================================

// Example: Set the timezone to Manila (Philippines Standard Time)
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileName = uniqid() . '_' . $_FILES['profile_picture']['name'];
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
        echo json_encode([
            'success' => true,
            'message' => 'File uploaded successfully (database disabled)',
            'file_path' => $filePath
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to save file']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No file uploaded']);
}
?>