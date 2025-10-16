<?php
// FILE: config/client_booking.php (CLEANED VERSION)

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
if (isset($_POST['submit_reference_from_modal']) && isset($_POST['order_id_value'])) {
    
    $orderId = intval($_POST['order_id_value']);
    $referenceCode = trim($_POST['reference_code'] ?? '');
    
    // Get payment data from POST
    $paymentMethod = trim($_POST['payment_method'] ?? '');
    $paymentDetails = trim($_POST['payment_details'] ?? '');
    $paymentType = trim($_POST['payment_type'] ?? '');
    
    // ✅ CUSTOM PAYMENT CHANNEL HANDLING
    if ($paymentDetails === 'Other') {
        $paymentDetails = trim($_POST['custom_payment_channel'] ?? '');
    }

    // Validate that we have a payment channel
    if (empty($paymentDetails)) {
        die("Error: Payment Channel is required");
    }
    
    // Validate other required fields
    if (empty($paymentMethod)) {
        die("Error: Payment Method is required");
    }
    if (empty($referenceCode)) {
        die("Error: Reference Code is required");
    }
    
    // Calculate amount due
    $totalPrice = 0;
    $amountDue = 0;
    
    $priceSql = "SELECT total_price FROM booking_tbl WHERE booking_id = ?";
    $priceStmt = $conn->prepare($priceSql);
    $priceStmt->bind_param("i", $orderId);
    $priceStmt->execute();
    $priceResult = $priceStmt->get_result();
    
    if ($priceRow = $priceResult->fetch_assoc()) {
        $totalPrice = (float)$priceRow['total_price'];
        $amountDue = ($paymentType === 'Full Payment') ? $totalPrice : $totalPrice * 0.50;
    }
    $priceStmt->close();
    
    // UPDATE booking_tbl with payment data
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
        
        // INSERT into payments_tbl
        $payment_sql = "INSERT INTO payments_tbl (
            booking_id, amount_paid, payment_type, payment_method, 
            payment_channel, reference_number, status
        ) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        
        $payment_stmt = $conn->prepare($payment_sql);
        $payment_type_db = ($paymentType === 'Full Payment') ? 'full_payment' : 'down_payment';
        
        $payment_stmt->bind_param("idssss", 
            $orderId,
            $amountDue,
            $payment_type_db,
            $paymentMethod,
            $paymentDetails,
            $referenceCode
        );
        
        if ($payment_stmt->execute()) {
            $payment_stmt->close();
            $conn->close();
            header("Location: ../user/billing.php?order_id=$orderId&payment_success=1");
            exit();
        } else {
            die("Error creating payment record: " . $payment_stmt->error);
        }
        
    } else {
        die("Error updating reference: " . $stmt->error);
    }
}

// =======================================================
// SCENARIO B: NEW BOOKING SUBMISSION
// =======================================================
if (isset($_POST['place_order_btn'])) {
    // Data Sanitization and Retrieval
    $fullName = trim($_POST['full_name'] ?? '');
    $mobileNumber = trim($_POST['mobile_number'] ?? ''); 
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');
    $eventTime = trim($_POST['event_time'] ?? '');
    $recommendations = trim($_POST['recommendations'] ?? '');
    $eventTheme = trim($_POST['event_theme'] ?? '');
    $packages = trim($_POST['packages'] ?? '');
    
    // Payment data from form
    $paymentMethod = trim($_POST['payment_method'] ?? '');
    $paymentDetails = trim($_POST['payment_details'] ?? ''); 
    $paymentType = trim($_POST['payment_type'] ?? '');
    
    $bookingStatus = 'PENDING_ORDER_CONFIRMATION';

    $totalPrice = 0.0;
    $amountDue = 0.0;
    $designDocumentPath = 'assets/uploads/default.jpg'; 

    // Fetch Package Price and Package ID
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

    if ($packageId === null || $packageId === 0) {
        die("Error: Invalid package selected.");
    }

    // Calculate Amount Due
    $amountDue = ($paymentType === 'Full Payment') ? $totalPrice : $totalPrice * 0.50;
    
    // Handle File Upload
    if (isset($_FILES['design_document_upload']) && $_FILES['design_document_upload']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../assets/uploads/'; 
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $fileTmpPath = $_FILES['design_document_upload']['tmp_name'];
        $fileName = basename($_FILES['design_document_upload']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = $_SESSION['user_id'] . '_' . time() . '.' . $fileExt;
        $destPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $designDocumentPath = 'assets/uploads/' . $newFileName;
        }
    }

    // Insert Booking into Database
    $sql = "INSERT INTO booking_tbl (
        user_id, package_id, full_name, mobile_number, email, address, 
        event_theme, packages, design_document_path, event_date, event_time, 
        recommendations, payment_method, payment_details, payment_type, 
        total_price, amount_due, booking_status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssssssssssssdds", 
        $_SESSION['user_id'], 
        $packageId,
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
        
        // Redirect to show payment modal
        header("Location: ../user/booking.php?order_id=$orderId&payment_method=" . urlencode($paymentMethod) . "&payment_type=" . urlencode($paymentType) . "&amount_due=$amountDue&payment_details=" . urlencode($paymentDetails));
        exit();
        
    } else {
        die("Database insertion failed: " . $stmt->error);
    }
}

// If neither form was submitted, redirect
header("Location: ../user/booking.php");
exit();
?>