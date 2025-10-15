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
// SCENARIO A: REFERENCE CODE SUBMISSION (From modal)
// =======================================================
if (isset($_POST['submit_reference_from_modal']) && isset($_POST['order_id_value']) && isset($_POST['reference_code'])) {
    
    $orderId = intval($_POST['order_id_value']);
    $referenceCode = trim($_POST['reference_code']);
    
    // GET PAYMENT DATA FROM MODAL - USE ACTUAL VALUES
    $paymentMethod = trim($_POST['payment_method'] ?? '');
    $paymentDetails = trim($_POST['payment_details'] ?? '');
    $paymentType = trim($_POST['payment_type'] ?? 'Down Payment'); // This should be from the form
    
    // Debug: Check what payment type we're receiving
    error_log("Payment Type Received: " . $paymentType);
    
    // If custom payment channel is used
    if (isset($_POST['custom_payment_channel']) && !empty(trim($_POST['custom_payment_channel']))) {
        $paymentDetails = trim($_POST['custom_payment_channel']);
    }
    
    // Validate that payment method data exists
    if (empty($paymentMethod)) {
        die("Error: Payment Method is required");
    }
    
    if (empty($paymentDetails)) {
        die("Error: Payment Channel is required");
    }
    
    // Calculate correct amount due based on ACTUAL payment type
    $totalPrice = 0;
    $amountDue = 0;
    
    // First, get the total price from the booking
    $priceSql = "SELECT total_price FROM booking_tbl WHERE booking_id = ?";
    $priceStmt = $conn->prepare($priceSql);
    $priceStmt->bind_param("i", $orderId);
    $priceStmt->execute();
    $priceResult = $priceStmt->get_result();
    
    if ($priceRow = $priceResult->fetch_assoc()) {
        $totalPrice = (float)$priceRow['total_price'];
        
        // Calculate amount due based on ACTUAL selected payment type
        if ($paymentType === 'Full Payment') {
            $amountDue = $totalPrice; // 100%
            error_log("Full Payment selected - Amount Due: " . $amountDue);
        } else {
            $amountDue = $totalPrice * 0.50; // Down Payment 50%
            error_log("Down Payment selected - Amount Due: " . $amountDue);
        }
    }
    $priceStmt->close();
    
    // Update the database with CORRECT payment information
    $sql = "UPDATE booking_tbl SET 
            payment_method = ?,
            payment_details = ?,
            payment_type = ?,
            reference_number = ?, 
            amount_due = ?,
            booking_status = 'PENDING_PAYMENT_VERIFICATION' 
            WHERE booking_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdi", 
        $paymentMethod, 
        $paymentDetails, 
        $paymentType, 
        $referenceCode, 
        $amountDue, 
        $orderId
    );
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        
        // Debug success
        error_log("Payment reference updated successfully - Type: " . $paymentType . ", Amount: " . $amountDue);
        
        // Redirect back to billing page
        header("Location: ../user/billing.php?order_id=$orderId&payment_success=1");
        exit();
    } else {
        die("Error updating reference: " . $stmt->error);
    }
}

// =======================================================
// SCENARIO B: NEW BOOKING SUBMISSION (From booking.php form)
// =======================================================
if (isset($_POST['place_order_btn'])) {
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
    $paymentType = trim($_POST['payment_type'] ?? '');
    $bookingStatus = 'PENDING_ORDER_CONFIRMATION';

    $totalPrice = 0.0;
    $amountDue = 0.0;
    $designDocumentPath = 'assets/uploads/default.jpg'; 

    // 2. Fetch Package Price and Package ID - FIXED HERE
    $sqlPrice = "SELECT package_id, fixed_price FROM package_details_tbl WHERE package_name = ? AND event_type = ?";
    $stmtPrice = $conn->prepare($sqlPrice);
    $stmtPrice->bind_param("ss", $packages, $eventTheme);
    $stmtPrice->execute();
    $resultPrice = $stmtPrice->get_result();
    
    $packageId = null;
    if ($row = $resultPrice->fetch_assoc()) {
        $totalPrice = (float)$row['fixed_price'];
        $packageId = (int)$row['package_id'];
    }
    $stmtPrice->close();

    // VALIDATE PACKAGE ID - CRITICAL FIX
    if ($packageId === null || $packageId === 0) {
        die("Error: Invalid package selected. Please choose a valid package.");
    }

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

    // 5. Insert Booking into Database - FIXED: Added package_id
    $sql = "INSERT INTO booking_tbl (user_id, package_id, full_name, mobile_number, email, address, event_theme, packages, design_document_path, event_date, event_time, recommendations, payment_method, payment_details, payment_type, total_price, amount_due, booking_status, reference_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)";
 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssssssssssssdds", 
        $_SESSION['user_id'], 
        $packageId, // ADDED: Valid package_id
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
        // Enhanced error logging
        error_log("Database Error: " . $stmt->error);
        error_log("Package ID used: " . $packageId);
        error_log("User ID: " . $_SESSION['user_id']);
        die("Error during booking submission: " . $stmt->error);
    }
}

// If neither form was submitted, redirect
header("Location: ../user/booking.php");
exit();
?>