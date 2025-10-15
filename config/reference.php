<?php
session_start();

// Database configuration
$db_host = 'localhost'; 
$db_name = 'raflora_enterprises';
$db_user = 'root'; 
$db_pass = '';

// Connect to database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['reference_code'])) {
    
    $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
    $referenceCode = trim($_POST['reference_code']);
    
    if ($order_id && !empty($referenceCode) && isset($_SESSION['user_id'])) {
        
        $sql = "UPDATE booking_tbl SET 
                reference_number = ?, 
                booking_status = 'PENDING_PAYMENT_VERIFICATION' 
                WHERE booking_id = ? AND user_id = ?";
        
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $conn->close();
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }
        
        $stmt->bind_param("sii", $referenceCode, $order_id, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: ../user/billing.php?order_id=" . $order_id . "&ref_submitted=success");
            exit();
        } else {
            $error = $stmt->error;
            $stmt->close();
            $conn->close();
            die("Error updating reference: " . htmlspecialchars($error));
        }
    } else {
        $conn->close();
        header("Location: ../user/booking.php?error=invalid_data");
        exit();
    }
} else {
    $conn->close();
    header("Location: ../user/booking.php?error=invalid_request");
    exit();
}

// This should never be reached, but just in case
$conn->close();
?>