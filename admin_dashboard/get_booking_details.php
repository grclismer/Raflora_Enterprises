<?php

// =======================================================================
// PHP SCRIPT START - TIMEZONE CORRECTION
// =======================================================================

// Example: Set the timezone to Manila (Philippines Standard Time)
date_default_timezone_set('Asia/Manila');


// get_booking_details.php
session_start();
include '../config/db.php';

$booking_id = intval($_GET['booking_id']);

$sql = "SELECT * FROM booking_tbl WHERE booking_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if ($booking) {
    echo '
    <h2>Booking Details #'.$booking['booking_id'].'</h2>
    <div class="booking-details-grid">
        <div class="detail-section">
            <h3>Client Information</h3>
            <div class="detail-item"><strong>Name:</strong> '.htmlspecialchars($booking['full_name']).'</div>
            <div class="detail-item"><strong>Email:</strong> '.htmlspecialchars($booking['email']).'</div>
            <div class="detail-item"><strong>Phone:</strong> '.htmlspecialchars($booking['mobile_number']).'</div>
            <div class="detail-item"><strong>Address:</strong> '.htmlspecialchars($booking['address']).'</div>
        </div>
        <div class="detail-section">
            <h3>Event Details</h3>
            <div class="detail-item"><strong>Event:</strong> '.htmlspecialchars($booking['event_theme']).'</div>
            <div class="detail-item"><strong>Package:</strong> '.htmlspecialchars($booking['packages']).'</div>
            <div class="detail-item"><strong>Event Date:</strong> '.date('M d, Y', strtotime($booking['event_date'])).'</div>
            <div class="detail-item"><strong>Event Time:</strong> '.htmlspecialchars($booking['event_time']).'</div>
        </div>
    </div>
    <div class="detail-section">
        <h3>Payment Information</h3>
        <div class="detail-item"><strong>Total Price:</strong> ₱'.number_format($booking['total_price'], 2).'</div>
        <div class="detail-item"><strong>Amount Due:</strong> ₱'.number_format($booking['amount_due'], 2).'</div>
        <div class="detail-item"><strong>Payment Method:</strong> '.htmlspecialchars($booking['payment_method']).'</div>
        <div class="detail-item"><strong>Payment Type:</strong> '.htmlspecialchars($booking['payment_type']).'</div>
        <div class="detail-item"><strong>Reference Code:</strong> '.(!empty($booking['reference_number']) ? '<code>'.htmlspecialchars($booking['reference_number']).'</code>' : 'No reference').'</div>
        <div class="detail-item"><strong>Status:</strong> <span class="status-badge status-'.strtolower(str_replace('_', '-', $booking['booking_status'])).'">'.str_replace('_', ' ', $booking['booking_status']).'</span></div>';
    
    // Show rejection reason if booking is rejected
    if ($booking['booking_status'] === 'REJECTED' && !empty($booking['rejection_reason'])) {
        echo '<div class="detail-item"><strong>Rejection Reason:</strong> '.htmlspecialchars($booking['rejection_reason']).'</div>';
    }
    
    echo '</div>
    <div class="detail-section">
        <h3>Client Recommendations</h3>
        <div class="detail-item">'.(!empty($booking['recommendations']) ? htmlspecialchars($booking['recommendations']) : 'No recommendations provided').'</div>
    </div>';
} else {
    echo '<p>Booking not found.</p>';
}

$conn->close();
?>