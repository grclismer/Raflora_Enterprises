<?php
// update_booking_status.php
session_start();
include '../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$booking_id = intval($input['booking_id']);
$new_status = $input['status'];
$reason = $input['reason'] ?? '';

// Add rejection reason to database if provided
if ($new_status === 'REJECTED' && !empty($reason)) {
    $sql = "UPDATE booking_tbl SET booking_status = ?, rejection_reason = ? WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $new_status, $reason, $booking_id);
} else {
    $sql = "UPDATE booking_tbl SET booking_status = ? WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $booking_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$conn->close();
?>