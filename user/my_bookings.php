<?php
// FILE: user/my_bookings.php (Admin-Style Table & Navbar)

session_start();
// Assuming ../config/db.php establishes $conn (mysqli object)
include '../config/db.php'; 

// Check user login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: user_login.html");
    exit();
}
$current_user_id = $_SESSION['user_id'] ?? 0;

$bookings = [];
$error_message = null;
// Profile Picture Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

// Create connection for profile picture
$conn_profile = new mysqli($servername, $username, $password, $dbname);
$user_data = [];

if (!$conn_profile->connect_error && isset($_SESSION['user_id'])) {
    // IMPORTANT: Include profile_picture in the query
    $stmt = $conn_profile->prepare("SELECT user_name, profile_picture FROM accounts_tbl WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc() ?? [];
    $stmt->close();
}
$conn_profile->close();
try {
    // UPDATED QUERY: Include reference_number
    $sql = "SELECT 
        booking_id, event_theme, packages, event_date, total_price, 
        amount_due, booking_status, created_at, reference_number
        FROM booking_tbl 
        WHERE user_id = ?
        ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    
    $stmt->close();

} catch (Exception $e) {
    $error_message = "Could not load bookings: " . $e->getMessage();
}

if (isset($conn)) $conn->close();

// Helper function to format currency
function format_price($price) {
    return '₱' . number_format($price, 2);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <link rel="stylesheet" href="../assets/css/user/booking.css">
    <link rel="stylesheet" href="../assets/css/user/footer.css">
    <link rel="stylesheet" href="../assets/css/user/navbar.css">
    <link rel="stylesheet" href="../assets/css/user/my_bookings.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/user/navbar.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <nav class="navbar">
        <img src="../assets/images/logo/raflora-logo.jpg" alt="logo" class="logo" />
        <div class="hamburger-menu">
            <i class="fas fa-bars"></i>
        </div>
        <ul class="nav-links">
            <li><a href="../user/landing.php" class="nav-link">Home</a></li>
            <li><a href="../user/gallery.php" class="nav-link">Gallery</a></li>
            <li><a href="../user/about.php" class="nav-link">About</a></li> 
            <li class="active"><a href="../user/my_bookings.php" class="nav-link">My Bookings</a></li>
            <li class="user-dropdown-toggle">
                <div class="navbar-profile">
                    <?php if (!empty($user_data['profile_picture'])): ?>
                        <!-- Profile picture with CSS class -->
                        <img src="/raflora_enterprises/<?php echo ltrim($user_data['profile_picture'], '/'); ?>" 
                            alt="Profile" 
                            class="profile-picture profile-picture-small">
                    <?php else: ?>
                        <!-- Default icon with CSS class -->
                        <div class="profile-default-icon">
                            <i class="fa fa-user"></i>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Username (optional) -->
                    <span class="navbar-username"><?php echo $user_data['user_name'] ?? 'User'; ?></span>
                </div>
                <ul class="user-dropdown-menu">
                    <li><a href="../user/account_settings.php">Account settings</a></li>
                    <li><a href="../user/booking.php">Book</a></li>
                    <li><a href="../api/logout.php">Log Out</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div class="container mx-auto p-4 md:p-8 mt-4">
        <h1 class="text-3xl font-bold mb-8 text-gray-800">My Booking History</h1>
        <?php if ($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php elseif (empty($bookings)): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">No Bookings Found.</strong>
                <span class="block sm:inline">You haven't placed any orders yet.</span>
            </div>
        <?php else: ?>
            <div class="table-container-wrapper">
                
                <!-- Fixed Table Header -->
                <table class="min-w-full booking-table table-fixed-header">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs tracking-wider w-1/6">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs tracking-wider w-1/4">Event/Package</th>
                            <th class="px-6 py-3 text-left text-xs tracking-wider w-1/6">Event Date</th>
                            <th class="px-6 py-3 text-right text-xs tracking-wider w-1/6">Total Price</th>
                            <th class="px-6 py-3 text-left text-xs tracking-wider w-1/6">Status</th>
                            <th class="px-6 py-3 text-left text-xs tracking-wider w-1/4">Actions</th>
                        </tr>
                    </thead>
                </table>
                
                <!-- Scrollable Table Body -->
                <div class="table-scroll-body overflow-y-auto overflow-x-hidden">
                    <table class="min-w-full booking-table table-fixed-body">
                        <tbody class="divide-y-8 divide-transparent">
                            <?php foreach ($bookings as $booking): 
                                $price = $booking['total_price'] ?? $booking['final_price'];
                                $booking_status = $booking['booking_status'];
                                $normalized_status = strtolower(str_replace('_', ' ', $booking_status));
                                $hasReference = !empty($booking['reference_number']) && $booking['reference_number'] !== 'NULL';
                                $shouldShowSubmitPayment = ($booking_status === 'PENDING_ORDER_CONFIRMATION' && !$hasReference);
                            ?>
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 w-1/6">
                                    #<?php echo htmlspecialchars($booking['booking_id']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 w-1/4">
                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($booking['event_theme']); ?></div>
                                    <div class="text-gray-500 text-xs"><?php echo htmlspecialchars($booking['packages']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 w-1/6">
                                    <?php echo date('M d, Y', strtotime($booking['event_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-gray-900 text-right w-1/6">
                                    <?php echo format_price($price); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 w-1/6">
                                    <span class="status-badge status-<?php echo $normalized_status; ?>">
                                        <?php 
                                        // FIXED: Proper status display
                                        $display_status = str_replace('_', ' ', $booking_status);
                                        echo htmlspecialchars($display_status); 
                                        ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3 w-1/4">
                                    <a href="billing.php?order_id=<?php echo $booking['booking_id']; ?>" class="text-indigo-600 hover:text-indigo-900">View Details</a>
                                    
                                    <?php if ($shouldShowSubmitPayment): ?>
                                        <span class="text-gray-300">|</span>
                                        <!-- UPDATED: Changed parameter name to trigger_modal -->
                                        <a href="billing.php?order_id=<?php echo $booking['booking_id']; ?>&trigger_modal=1" class="text-green-600 hover:text-green-900 font-bold">Submit Payment</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <footer>
    </footer>
</body>
</html>