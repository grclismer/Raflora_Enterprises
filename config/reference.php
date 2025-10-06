<?php
// FILE: config/submit_reference.php (Bagong file para sa Reference Code)

session_start();
require_once('db.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['reference_code'])) {
    
    $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
    $referenceCode = trim($_POST['reference_code']);
    
    if ($order_id && !empty($referenceCode) && isset($_SESSION['user_id'])) {
        
        // Update the booking with the reference code and change status to PENDING
        $sql = "UPDATE bookings SET reference_code = ?, status = 'PENDING' WHERE booking_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $referenceCode, $order_id, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            // Success! Ibalik sa receipt page (may order_id pa rin)
            header("Location: ../user/booking.php?order_id=" . $order_id . "&ref_submitted=success");
            exit();
        } else {
            die("Error updating reference: " . htmlspecialchars($stmt->error));
        }

        // $stmt->close();
    }
}

// Bumalik sa booking page kung may error o walang data
header("Location: ../user/booking.php?error=ref_failed");
exit();
?>