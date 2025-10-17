<?php
// =======================================================================
// PHP SCRIPT START - TIMEZONE CORRECTION
// =======================================================================

// Example: Set the timezone to Manila (Philippines Standard Time)
date_default_timezone_set('Asia/Manila');


session_start();

// Check if the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../user/user_login.html");
    exit();
}
// Check if user is an admin or client
$is_admin = ($_SESSION['role'] === 'admin_type');

// Database connection details
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "raflora_enterprises"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the order_id is present in the URL
if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Prepare and execute a SELECT query
    $sql = "SELECT * FROM booking_tbl WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the booking details into an associative array
        $booking_data = $result->fetch_assoc();
    } else {
        echo "Booking not found.";
        exit();
    }
    $stmt->close();
} else {
    echo "Invalid request.";
    exit();
}

// Close connection at the end of the script
$conn->close();
?>