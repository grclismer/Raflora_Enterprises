<?php
// FILE: config/client_booking.php - COMPLETE VERSION (Both new bookings + reference submissions)
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

// =======================================================
// SCENARIO A: REFERENCE CODE SUBMISSION (From billing.php modal)
// =======================================================
if (isset($_POST['submit_reference_from_modal']) && isset($_POST['order_id_value']) && isset($_POST['reference_code'])) {
    $orderId = intval($_POST['order_id_value']);
    $referenceCode = trim($_POST['reference_code']);
    
    // Update the database
    $sql = "UPDATE booking_tbl SET 
            reference_number = ?, 
            booking_status = 'PENDING_PAYMENT_VERIFICATION' 
            WHERE booking_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $referenceCode, $orderId);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // Redirect back to billing page
        header("Location: ../user/billing.php?order_id=$orderId");
        exit();
    } else {
        die("Error updating reference: " . $stmt->error);
    }
}

// =======================================================
// SCENARIO B: NEW BOOKING SUBMISSION (From booking.php form)
// =======================================================
if (isset($_POST['place_order_btn'])) {
    // YOUR ORIGINAL NEW BOOKING CODE GOES HERE
    // (Keep all your existing code for new bookings)
    
    // 1. Data Sanitization and Retrieval
    $fullName = trim($_POST['full_name'] ?? '');
    $mobileNumber = trim($_POST['mobile_number'] ?? ''); 
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');
    $eventTime = trim($_POST['event_time'] ?? '');
    $recommendations = trim($_POST['recommendations'] ?? '');
    $eventTheme = trim($_POST['event_theme'] ?? '');
    $packages = trim($_POST['packages'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? '');
    $paymentDetails = trim($_POST['payment_details'] ?? 'Not Applicable'); 

    $paymentType = trim($_POST['payment_type'] ?? 'Down Payment');
    $bookingStatus = 'PENDING_ORDER_CONFIRMATION';

    $totalPrice = 0.0;
    $amountDue = 0.0;
    $designDocumentPath = 'assets/uploads/default.jpg'; 

    // 2. Fetch Package Price
    $sqlPrice = "SELECT fixed_price FROM package_details_tbl WHERE package_name = ? AND event_type = ?";
    $stmtPrice = $conn->prepare($sqlPrice);
    $stmtPrice->bind_param("ss", $packages, $eventTheme);
    $stmtPrice->execute();
    $resultPrice = $stmtPrice->get_result();
    
    if ($row = $resultPrice->fetch_assoc()) {
        $totalPrice = (float)$row['fixed_price'];
    }
    $stmtPrice->close();

    // 3. Calculate Amount Due
    if ($paymentType === 'Full Payment') {
        $amountDue = $totalPrice;
    } else {
        $amountDue = $totalPrice * 0.50;
    }
    
    // 4. Handle File Upload
    if (isset($_FILES['design_document_upload']) && $_FILES['design_document_upload']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../assets/uploads/'; 
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileTmpPath = $_FILES['design_document_upload']['tmp_name'];
        $fileName = basename($_FILES['design_document_upload']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = $_SESSION['user_id'] . '_' . time() . '.' . $fileExt;
        $destPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $designDocumentPath = 'assets/uploads/' . $newFileName;
        }
    }

    // 5. Insert Booking into Database 
    $sql = "INSERT INTO booking_tbl (user_id, full_name, mobile_number, email, address, event_theme, packages, design_document_path, event_date, event_time, recommendations, payment_method, payment_details, payment_type, total_price, amount_due, booking_status, reference_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)";
 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssssssssdds", 
        $_SESSION['user_id'], 
        $fullName, 
        $mobileNumber, 
        $email, 
        $address, 
        $eventTheme, 
        $packages, 
        $designDocumentPath,
        $eventDate, 
        $eventTime, 
        $recommendations, 
        $paymentMethod,
        $paymentDetails, 
        $paymentType, 
        $totalPrice,
        $amountDue, 
        $bookingStatus
    );

    if ($stmt->execute()) {
        $orderId = $conn->insert_id;
        $stmt->close();
        $conn->close();

        // Redirect to booking.php to show the payment modal
        header("Location: ../user/booking.php?order_id={$orderId}&payment_method={$paymentMethod}&payment_type={$paymentType}&amount_due={$amountDue}&payment_details={$paymentDetails}");
        exit();
    } else {
        die("Error during booking submission: " . $stmt->error);
    }
}

// If neither form was submitted, redirect
header("Location: ../user/booking.php");
exit();
?>