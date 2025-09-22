<?php
session_start();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Database connection details
    $servername = "localhost";
    $username = "root"; // Ensure this is correct
    $password = ""; // Your password if you have one
    $dbname = "raflora_enterprises";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // 2. Get user ID from session
    $user_id = $_SESSION['user_id'];

    // 3. Collect and sanitize form data
    $fullname = $conn->real_escape_string($_POST['full_name']);
    $mobile_number = $conn->real_escape_string($_POST['mobile_number']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);
    $event_date = $conn->real_escape_string($_POST['event_date']);
    $event_time = $conn->real_escape_string($_POST['event_time']);
    $recommendations = $conn->real_escape_string($_POST['recommendations']);
    $event_theme = $conn->real_escape_string($_POST['event_theme']);
    $packages = $conn->real_escape_string($_POST['packages']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $payment_type = $conn->real_escape_string($_POST['payment_type']);

    // Combine date and time into a single DATETIME string
    $event_timedate = $event_date . ' ' . $event_time;
    
    // 4. Handle file uploads (color scheme and preferred design)
    // You will need a more robust file upload handler for a production environment
    $color_scheme = "default_color.jpg";
    $preferred_image_path = "default_design1.jpg";

    if (isset($_FILES['color_scheme_upload']) && $_FILES['color_scheme_upload']['error'] == 0) {
        // Here you would save the file and get its path
        $color_scheme = basename($_FILES['color_scheme_upload']['name']);
        // Save the file to a secure directory on your server
    }
    
    // Repeat for other image uploads
    if (isset($_FILES['preferred_image_1']) && $_FILES['preferred_image_1']['error'] == 0) {
        // Save the file and get its path
        $preferred_image_path = basename($_FILES['preferred_image_1']['name']);
    }

    // 5. Construct the SQL INSERT statement
    $sql = "INSERT INTO booking_tbl (
        user_id,
        full_name,
        mobile_number,
        address,
        color_scheme,
        event_timedate,
        recommendations,
        event_theme,
        packages,
        payment_method,
        payment,
        preferred_image_path
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )";

    // 6. Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssssss",
        $user_id,
        $fullname,
        $mobile_number,
        $address,
        $color_scheme,
        $event_timedate,
        $recommendations,
        $event_theme,
        $packages,
        $payment_method,
        $payment_type, // Use the payment_type variable
        $preferred_image_path
    );

    // 7. Execute the statement and check for success
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        header("Location: ../user/billing.php?order_id=" . $order_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    // If not a POST request, redirect back to the form
    header("Location: ../user/booking.php");
    exit();
}
?>