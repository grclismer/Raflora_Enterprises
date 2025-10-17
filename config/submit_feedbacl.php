<?php
session_start();

// Database configuration
$db_host = 'localhost'; 
$db_name = 'raflora_enterprises';
$db_user = 'root'; 
$db_pass = '';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['feedback_error'] = "Invalid request method.";
    header("Location: ../user/my_bookings.php");
    exit();
}

// Get form data
$order_id = intval($_POST['order_id']);
$client_name = $conn->real_escape_string($_POST['client_name'] ?? '');
$event_theme = $conn->real_escape_string($_POST['event_theme'] ?? '');
$overall_rating = intval($_POST['rating'] ?? 0);
$category_floral = intval($_POST['category_floral'] ?? 3);
$category_setup = intval($_POST['category_setup'] ?? 3);
$category_service = intval($_POST['category_service'] ?? 3);
$feedback_text = $conn->real_escape_string($_POST['feedback'] ?? '');
$would_recommend = isset($_POST['would_recommend']) ? 1 : 0;
$is_anonymous = isset($_POST['anonymous']) ? 1 : 0;

// Validate required fields
if (empty($order_id) || $order_id <= 0) {
    $_SESSION['feedback_error'] = "Invalid order ID.";
    header("Location: ../user/billing.php?order_id=" . $order_id);
    exit();
}

// Validate rating
if ($overall_rating < 1 || $overall_rating > 5) {
    $_SESSION['feedback_error'] = "Please provide a valid rating (1-5 stars).";
    header("Location: ../user/billing.php?order_id=" . $order_id);
    exit();
}

// Check if the order exists and is completed
$check_order_sql = "SELECT booking_id, booking_status FROM booking_tbl WHERE booking_id = ?";
$check_order_stmt = $conn->prepare($check_order_sql);
$check_order_stmt->bind_param("i", $order_id);
$check_order_stmt->execute();
$order_result = $check_order_stmt->get_result();

if ($order_result->num_rows === 0) {
    $_SESSION['feedback_error'] = "Order not found in the system.";
    header("Location: ../user/billing.php?order_id=" . $order_id);
    exit();
}

$order_data = $order_result->fetch_assoc();
if ($order_data['booking_status'] !== 'COMPLETED') {
    $_SESSION['feedback_error'] = "Feedback can only be submitted for completed events.";
    header("Location: ../user/billing.php?order_id=" . $order_id);
    exit();
}
$check_order_stmt->close();

// Check if feedback already exists for this order
$check_feedback_sql = "SELECT feedback_id FROM client_feedback WHERE order_id = ?";
$check_feedback_stmt = $conn->prepare($check_feedback_sql);
$check_feedback_stmt->bind_param("i", $order_id);
$check_feedback_stmt->execute();
$feedback_result = $check_feedback_stmt->get_result();

if ($feedback_result->num_rows > 0) {
    $_SESSION['feedback_error'] = "You have already submitted feedback for this order.";
    header("Location: ../user/billing.php?order_id=" . $order_id);
    exit();
}
$check_feedback_stmt->close();

// Insert feedback
$sql = "INSERT INTO client_feedback (
    order_id, client_name, event_theme, overall_rating, 
    category_floral, category_setup, category_service, 
    feedback_text, would_recommend, is_anonymous
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    $_SESSION['feedback_error'] = "Database error. Please try again.";
    header("Location: ../user/billing.php?order_id=" . $order_id);
    exit();
}

$stmt->bind_param(
    "issiiiiisi", 
    $order_id, $client_name, $event_theme, $overall_rating,
    $category_floral, $category_setup, $category_service,
    $feedback_text, $would_recommend, $is_anonymous
);

if ($stmt->execute()) {
    $_SESSION['feedback_success'] = "Thank you for your feedback! Your review has been submitted successfully.";
} else {
    $_SESSION['feedback_error'] = "There was an error submitting your feedback. Please try again.";
}

$stmt->close();
$conn->close();

// Redirect back to billing page
header("Location: ../user/billing.php?order_id=" . $order_id);
exit();
?>