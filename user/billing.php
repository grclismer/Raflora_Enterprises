<?php
session_start();
// ===== FEEDBACK SUBMISSION HANDLER =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'])) {
    // Database configuration
    $db_host = 'localhost'; 
    $db_name = 'raflora_enterprises';
    $db_user = 'root'; 
    $db_pass = '';
    
    // Create connection for feedback submission
    $feedback_conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($feedback_conn->connect_error) {
        $_SESSION['feedback_error'] = "Database connection failed.";
        header("Location: billing.php?order_id=" . $_POST['order_id']);
        exit();
    }
    
    // Get form data
    $order_id = intval($_POST['order_id']);
    $client_name = $feedback_conn->real_escape_string($_POST['client_name'] ?? '');
    $event_theme = $feedback_conn->real_escape_string($_POST['event_theme'] ?? '');
    $overall_rating = intval($_POST['rating'] ?? 0);
    $category_floral = intval($_POST['category_floral'] ?? 3);
    $category_setup = intval($_POST['category_setup'] ?? 3);
    $category_service = intval($_POST['category_service'] ?? 3);
    // Get and validate feedback text
$feedback_text = trim($_POST['feedback'] ?? '');
if (empty($feedback_text)) {
    $feedback_text = 'No detailed feedback provided';
}
$feedback_text = $feedback_conn->real_escape_string($feedback_text);

// Debug logging
error_log("Feedback submission debug:");
error_log("Order ID: " . $order_id);
error_log("Feedback text received: '" . $_POST['feedback'] . "'");
error_log("Feedback text after processing: '" . $feedback_text . "'");
    $would_recommend = isset($_POST['would_recommend']) ? 1 : 0;
    $is_anonymous = isset($_POST['anonymous']) ? 1 : 0;


    error_log("Feedback submission - Order: $order_id, Rating: $overall_rating, Feedback: " . ($_POST['feedback'] ?? 'EMPTY'));

// Validate feedback text
if (empty(trim($feedback_text))) {
    $feedback_text = 'No detailed feedback provided';
}
    // Validate required fields
    if (empty($order_id) || $order_id <= 0) {
        $_SESSION['feedback_error'] = "Invalid order ID.";
        header("Location: billing.php?order_id=" . $order_id);
        exit();
    }
    
    // Validate rating
    if ($overall_rating < 1 || $overall_rating > 5) {
        $_SESSION['feedback_error'] = "Please provide a valid rating (1-5 stars).";
        header("Location: billing.php?order_id=" . $order_id);
        exit();
    }
    
    // Check if the order exists and is completed
    $check_order_sql = "SELECT booking_id, booking_status FROM booking_tbl WHERE booking_id = ?";
    $check_order_stmt = $feedback_conn->prepare($check_order_sql);
    $check_order_stmt->bind_param("i", $order_id);
    $check_order_stmt->execute();
    $order_result = $check_order_stmt->get_result();
    
    if ($order_result->num_rows === 0) {
        $_SESSION['feedback_error'] = "Order not found in the system.";
        header("Location: billing.php?order_id=" . $order_id);
        exit();
    }
    
    $order_data = $order_result->fetch_assoc();
    if ($order_data['booking_status'] !== 'COMPLETED') {
        $_SESSION['feedback_error'] = "Feedback can only be submitted for completed events.";
        header("Location: billing.php?order_id=" . $order_id);
        exit();
    }
    $check_order_stmt->close();
    
    // Check if feedback already exists for this order
    $check_feedback_sql = "SELECT feedback_id FROM client_feedback WHERE order_id = ?";
    $check_feedback_stmt = $feedback_conn->prepare($check_feedback_sql);
    $check_feedback_stmt->bind_param("i", $order_id);
    $check_feedback_stmt->execute();
    $feedback_result = $check_feedback_stmt->get_result();
    
    if ($feedback_result->num_rows > 0) {
        $_SESSION['feedback_error'] = "You have already submitted feedback for this order.";
        header("Location: billing.php?order_id=" . $order_id);
        exit();
    }
    $check_feedback_stmt->close();
    
    // Insert feedback
    $sql = "INSERT INTO client_feedback (
        order_id, client_name, event_theme, overall_rating, 
        category_floral, category_setup, category_service, 
        feedback_text, would_recommend, is_anonymous
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $feedback_conn->prepare($sql);
    if ($stmt === false) {
        $_SESSION['feedback_error'] = "Database error. Please try again.";
        header("Location: billing.php?order_id=" . $order_id);
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
    $feedback_conn->close();
    
    // Redirect back to billing page
    header("Location: billing.php?order_id=" . $order_id);
    exit();
}


// Handle payment modal display - Store in session
// ... rest of your existing code continues below ...

// Handle payment modal display - Store in session
if (isset($_GET['show_payment_modal']) && $_GET['show_payment_modal'] == 1 && isset($_GET['order_id'])) {
    $_SESSION['show_payment_modal'] = $_GET['order_id'];
    
    // Redirect to clean URL
    header("Location: billing.php?order_id=" . $_GET['order_id']);
    exit();
}

// Database configuration (Must match client_booking.php)
$db_host = 'localhost'; 
$db_name = 'raflora_enterprises';
$db_user = 'root'; 
$db_pass = ''; 

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    header("Location: my_bookings.php");
    exit();
}

$orderId = intval($_GET['order_id']);

// Initialize database connection
$conn = null;
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database Connection Error: " . $e->getMessage());
}

// 1. Fetch Booking Details from booking_tbl
$booking = null;
$sql_booking = "SELECT user_id, full_name, mobile_number, email, address, event_theme, packages, design_document_path, event_date, event_time, recommendations, payment_method, payment_details, payment_type, total_price, amount_due, booking_status, reference_number, rejection_reason FROM booking_tbl WHERE booking_id = ?";
$stmt_booking = $conn->prepare($sql_booking);
$stmt_booking->bind_param("i", $orderId);
$stmt_booking->execute();
$result_booking = $stmt_booking->get_result();

if ($result_booking->num_rows > 0) {
    $booking = $result_booking->fetch_assoc();
} else {
    die("Error: Booking ID {$orderId} not found.");
}
$stmt_booking->close();

// 2. Fetch Package Details (Inclusions) from package_details_tbl
$inclusions = "N/A";
if ($booking && !empty($booking['packages']) && !empty($booking['event_theme'])) {
    $sql_package = "SELECT inclusions FROM package_details_tbl WHERE package_name = ? AND event_type = ?";
    $stmt_package = $conn->prepare($sql_package);
    
    $stmt_package->bind_param("ss", $booking['packages'], $booking['event_theme']); 
    $stmt_package->execute();
    $result_package = $stmt_package->get_result();
    
    if ($row_package = $result_package->fetch_assoc()) {
        $inclusions = $row_package['inclusions'];
    }
    $stmt_package->close();
}

$conn->close();

// UPDATED: Enhanced check for interrupted transactions
$showPaymentModal = false;

// Method 1: Check URL parameter from booking history
if (isset($_GET['trigger_modal']) && $_GET['trigger_modal'] == 1) {
    if ($booking && $booking['booking_status'] === 'PENDING_ORDER_CONFIRMATION') {
        $showPaymentModal = true;
    }
}

// Method 2: Check for interrupted transactions specifically
if (isset($_GET['continue_transaction']) && $_GET['continue_transaction'] == 1) {
    if ($booking && $booking['booking_status'] === 'PENDING_ORDER_CONFIRMATION') {
        $showPaymentModal = true;
        $_SESSION['continue_transaction'] = $orderId;
    }
}

// Method 3: Check session for interrupted transactions
if (isset($_SESSION['continue_transaction']) && $_SESSION['continue_transaction'] == $orderId) {
    if ($booking && $booking['booking_status'] === 'PENDING_ORDER_CONFIRMATION') {
        $showPaymentModal = true;
    }
}

// Method 4: Check session (for redirects from other pages)
if (isset($_SESSION['show_payment_modal']) && $_SESSION['show_payment_modal'] == $orderId) {
    if ($booking && $booking['booking_status'] === 'PENDING_ORDER_CONFIRMATION') {
        $showPaymentModal = true;
    }
    unset($_SESSION['show_payment_modal']);
}

// Helper function to format currency
function format_price($price) {
    return '₱' . number_format($price, 2);
}

// Calculate remaining balance
$totalPrice = (float)$booking['total_price'];
$amountDue = (float)$booking['amount_due'];
$remainingBalance = $totalPrice - $amountDue;

// Format date 
$formattedDate = date('m-d-y', strtotime($booking['event_date']));

// Determine design file path and name
$designPath = $booking['design_document_path'];
$designFileName = basename($designPath);
$fileUrl = (strpos($designPath, 'default') !== false) ? null : '../' . $designPath;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Receipt - Order <?php echo $orderId; ?></title>

    <link rel="stylesheet" href="../assets/css/user/billing_modal.css">
    <link rel="stylesheet" href="../assets/css/user/billing.css">
    <script src="../assets/js/user/modal.js" defer ></script>

    <!-- Add Bootstrap for modal compatibility -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>

  <div class="billing-container">

    <!-- LEFT (Billing info) - fixed / sticky on desktop -->
    <aside class="billing-left">
        <h1 style="font-size:28px; font-weight:800; margin-bottom:12px;">Thank you for your purchase!</h1>
        <h3 style="font-size:18px; margin-top:18px;">Billing address</h3>

        <div class="billing-address" style="margin-top:12px;">
            <p><strong>Name</strong><?php echo htmlspecialchars($booking['full_name']); ?></p>
            <p><strong>Address</strong><?php echo htmlspecialchars($booking['address']); ?></p>
            <p><strong>Phone</strong><?php echo htmlspecialchars($booking['mobile_number']); ?></p>
            <p><strong>Email</strong><?php echo htmlspecialchars($booking['email']); ?></p>
        </div>

        <div style="margin-top: 40px;">
            

            <!-- Buttons / Status area -->
            <?php if ($booking['booking_status'] == 'PENDING_ORDER_CONFIRMATION'): ?>
                <?php if ($showPaymentModal): ?>
                    <div class="interrupted-transaction-notice">
                        <h4>🔄 Interrupted Transaction</h4>
                        <p>Your booking was created but payment reference wasn't submitted. Please complete your payment below.</p>
                    </div>
                <?php endif; ?>
                
                <button class="submit-payment-btn" onclick="openBillingPaymentModal()" style="margin-top:25px; margin-bottom:25px; width:40%; <?php echo $showPaymentModal ? 'background: #dc3545; border-color: #dc3545;' : ''; ?>">
                     <?php echo $showPaymentModal ? 'Complete Payment Reference' : 'Submit Payment Reference'; ?>
                </button>
                <div class="status-indicator" style="margin-top:12px; <?php echo $showPaymentModal ? 'color: #dc3545;' : ''; ?>">
                    <?php echo $showPaymentModal ? '⚠️ Status: Payment Reference Required' : 'Status: Waiting for Payment Reference Submission'; ?>
                </div>

            <?php elseif ($booking['booking_status'] == 'PENDING_PAYMENT_VERIFICATION'): ?>
                <a href="../user/my_bookings.php" class="proceed-btn">View My Bookings</a>
                <div class="status-indicator" style="margin-top:12px;">Status: Waiting for Admin Payment Verification</div>
                

            <?php elseif ($booking['booking_status'] == 'APPROVED'): ?>
                <a href="../user/my_bookings.php" class="proceed-btn">View My Bookings</a>
                <div class="status-indicator" style="margin-top:25px; margin-bottom:25px; width:90%; background:#d4edda; border-color:#c3e6cb; color:#155724;">✅ Status: Booking Approved! Your event is confirmed.</div>

            <?php elseif ($booking['booking_status'] == 'REJECTED'): ?>
                <div class="status-indicator" style="margin-top:25px; margin-bottom:25px; width:40%; background:#f8d7da; border-color:#f5c6cb; color:#721c24;">❌ Status: Booking Rejected
                    <?php if (!empty($booking['rejection_reason'])): ?>
                        <a href="../user/my_bookings.php" class="View-book-btn"style="margin-top: 12px; margin-left:38px; width:130px; height:70px; font-size:17px;  " >View My Bookings</a>
                        <div style="margin-top:12px;"><strong>Reason:</strong> <?php echo htmlspecialchars($booking['rejection_reason']); ?></div>
                    <?php endif; ?>
                </div>

                <div style="margin-top:12px; padding:12px; background:#fff3cd; border:1px solid #ffeaa7; border-radius:6px;"> 
                    <strong>What to do next?</strong>
                    <p>If you believe this was a mistake or would like to discuss further, please contact our support team.</p>
                </div>
            <?php elseif ($booking['booking_status'] == 'COMPLETED'): ?>
                <a href="../user/my_bookings.php" class="proceed-btn">View My Bookings</a>
                <div class="status-indicator" style="margin-top:25px; margin-bottom:25px; width:90%; background:#e2e3e5; border-color:#d6d8db; color:#383d41;">✅ Status: Event Completed Successfully</div>
            <?php endif; ?>

            
        </div>
        <?php
// Check if feedback already exists for this order
$feedback_exists = false;
if ($booking['booking_status'] == 'COMPLETED') {
    $check_feedback_conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $check_feedback_sql = "SELECT feedback_id FROM client_feedback WHERE order_id = ?";
    $check_feedback_stmt = $check_feedback_conn->prepare($check_feedback_sql);
    $check_feedback_stmt->bind_param("i", $orderId);
    $check_feedback_stmt->execute();
    $feedback_result = $check_feedback_stmt->get_result();
    $feedback_exists = $feedback_result->num_rows > 0;
    $check_feedback_stmt->close();
    $check_feedback_conn->close();
}
?>

<?php if ($booking['booking_status'] == 'COMPLETED'): ?>
    <?php if (!$feedback_exists): ?>
        <a href="#" class="feedback-link" id="showFeedbackCondition" style="display: inline-block; margin-top: 20px; padding: 10px 15px; background: #4a6fa5; color: white; text-decoration: none; border-radius: 5px;">
            📝 Submit Feedback and Evaluation
        </a>
    <?php else: ?>
        <div style="margin-top: 20px; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center;">
            <span style="color: #155724;">✅ Thank you! Your feedback has been submitted.</span>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px; text-align: center;">
        <small>Feedback will be available after event completion</small>
    </div>
<?php endif; ?>
    </aside>
    
    <!-- RIGHT (Summary) - scrollable -->
    <main class="billing-right">
        
        <div class="summary-header">
            <h2>Summary of deliverables</h2>
            <div>
                <img src="../assets/images/logo/raflora-logo.jpg" alt="RAFLORA Logo">
                
            </div>

            <div class="summary-info">
                <div><strong>Date</strong><div><?php echo $formattedDate; ?></div></div>
                <div><strong>Order number</strong><div><?php echo htmlspecialchars($orderId); ?></div></div>
                <div><strong>Payment method</strong><div><?php echo htmlspecialchars($booking['payment_method']); ?></div></div>
            </div>
        </div>
          <thead>
            <table class="sub-header">
                <tr>
                    <th>Item (Event/Package)</th>
                    <!-- <th style="text-align:center; padding:8px 0;">Price/Unit</th> -->
                    <th>Package Details</th>
                    <th>Total cost</th>
                </tr>
            </table>
            </thead>
        <table class="summary-table">
            <tbody class="summary-body">
                <tr>
                    <td style="padding:12px 0;">
                        <strong>Event:</strong> <?php echo htmlspecialchars($booking['event_theme']); ?><br>
                        <strong>Package:</strong> <?php echo htmlspecialchars($booking['packages']); ?>
                    </td>
                    <td style="text-align:center; padding:12px 0;">
                        <!-- if you have per-unit price, echo here -->
                    </td>
                    <td style="padding:12px 0;">
                        <span style="font-weight:bold;">1 Package</span>
                        <?php if ($inclusions !== 'N/A'): ?>
                            <ul class="inclusions-list" style="margin-top:8px; padding-left:18px;">
                                <?php
                                $items = preg_split('/<br\s*\/?>|\n/', $inclusions, -1, PREG_SPLIT_NO_EMPTY);
                                foreach ($items as $item) {
                                    echo '<li>' . trim($item) . '</li>';
                                }
                                ?>
                            </ul>
                        <?php else: ?>
                            <p style="font-style:italic; color:#777; margin-top:6px;">No detailed inclusions available.</p>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:right; padding:12px 0;"><?php echo format_price($totalPrice); ?></td>
                </tr>
            </tbody>
        </table>

        <!-- design file link if exists -->
        <?php if ($fileUrl): ?>
            <a class="file-link" href="<?php echo htmlspecialchars($fileUrl); ?>" target="_blank">
                📁 <?php echo htmlspecialchars($designFileName); ?> (Click to view preferred design document)
            </a>
        <?php endif; ?>

        <!-- Totals area -->
        <div style="margin-top:28px; margin-bottom:30px; padding:16px; background:#fff; border-radius:8px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <div><strong>GRANDTOTAL (Fixed Price)</strong></div>
                <div style="font-weight:800;"><?php echo format_price($totalPrice); ?></div>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <div>Amount Paid (Down Payment)</div>
                <div><?php echo format_price($amountDue); ?></div>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:0;">
                <div><strong>Remaining Balance Due:</strong></div>
                <div style="font-weight:800; color:#fa5353fa;"><?php echo format_price($remainingBalance); ?></div>
            </div>
        </div>

        <!-- Client Recommendations & feedback -->
        <div style="margin-top:10px; margin-bottom:30px; padding:14px; background:#fff; border-radius:8px;">
            <h3 style="margin:0 0 8px 0;">Client Recommendations</h3>
            <p style="color:#333; margin:0;"><?php echo nl2br(htmlspecialchars($booking['recommendations'])); ?></p>
        </div>

    </main>

  </div>

  <!-- Payment Modal - EXACTLY LIKE BOOKING PAGE -->
  <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                
            </div>
            <form action="../config/client_booking.php" method="POST" id="modalReferenceForm">
                <div class="modal-body">
                    <div class="alert alert-info" role="alert">
                        Please complete your payment information below. <br>
                        <strong>Gcash No. 09773436195</strong> <br>
                        <strong>BDO SAVINGS ACCOUNT:  0013-9018-3937</strong><br>
                    </div>
                    
                    <p><strong>Order ID:</strong> <span id="modal-order-id"><?php echo $orderId; ?></span></p>
                    <p><strong>Package Price:</strong> <span id="package-price" class="text-info"><?php echo format_price($totalPrice); ?></span></p>
                    <p><strong>Amount Due Now:</strong> <span id="amount-due-now" class="text-xl text-success"><?php echo format_price($amountDue); ?></span></p>
                    
                    <input type="hidden" id="total-package-price" value="<?php echo $totalPrice; ?>">

                    <!-- Payment Method Display (Read-only) -->
                    <div class="mb-3">
                        <label class="form-label">Selected Payment Method</label>
                        <div class="form-control" style="background-color: #f8f9fa;">
                            <strong><?php echo htmlspecialchars($booking['payment_method']); ?> - <?php echo htmlspecialchars($booking['payment_type']); ?></strong>
                        </div>
                    </div>

                    <!-- Payment Channel Section -->
                    <div class="mb-3" id="payment-channel-section">
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label for="payment-details-select" class="form-label required-field">
                                    Payment Channel <span class="required-indicator">*</span>
                                </label>
                                <select name="payment_details" id="payment-details-select" class="form-control" required>
                                    <option value="">Select channel</option>
                                    <!-- Options will be populated by JavaScript based on payment method -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <!-- Custom Channel Input (Hidden by default) -->
                                <label for="custom-payment-channel" class="form-label" style="color: #6c757d;">
                                    Specify Channel
                                </label>
                                <input type="text" 
                                       name="custom_payment_channel" 
                                       id="custom-payment-channel" 
                                       class="form-control" 
                                       placeholder="Enter other payment channel"
                                       style="display: none; color: #6c757d;"
                                       disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Reference Code -->
                    <div class="mb-3">
                        <label for="referenceCode" class="form-label required-field">
                            Reference ID/Code <span class="required-indicator">*</span>
                        </label>
                        <input type="text" class="form-control" name="reference_code" id="referenceCode" required 
                            placeholder="Enter your 12-13 digit Reference code" 
                            minlength="12" maxlength="13"
                            value="<?php echo htmlspecialchars($booking['reference_number'] ?? ''); ?>">
                    </div>

                    <!-- Hidden fields to pass payment data -->
                    <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($booking['payment_method']); ?>">
                    <input type="hidden" name="payment_type" value="<?php echo htmlspecialchars($booking['payment_type']); ?>">
                    <input type="hidden" name="order_id_value" id="modal-order-id-input" value="<?php echo $orderId; ?>">
                </div>
                <div class="modal-footer">
                    <button type="submit" name="submit_reference_from_modal" class="btn btn-success">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
  </div>

  <script>
    // Function to open modal manually
    function openPaymentModal() {
        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
    }
  </script>
  <script>
// Function to open the billing payment modal
function openBillingPaymentModal() {
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    paymentModal.show();
    
    // Initialize payment channels when modal opens manually
    setTimeout(function() {
        const paymentMethod = "<?php echo htmlspecialchars($booking['payment_method']); ?>";
        const paymentDetailsSelect = document.getElementById('payment-details-select');
        const customChannelInput = document.getElementById('custom-payment-channel');
        
        // Define payment channels
        const paymentChannels = {
            'Online Bank': ['BDO Bank', 'BPI Bank', 'Metrobank', 'UnionBank', 'Landbank', 'Security Bank', 'Other'],
            'E-Wallet': ['GCash', 'PayMaya', 'Other']
        };
        
        function initializePaymentChannels() {
            if (paymentDetailsSelect && paymentChannels[paymentMethod]) {
                // Clear existing options
                paymentDetailsSelect.innerHTML = '<option value="">Select channel</option>';
                
                // Populate with channels based on payment method
                paymentChannels[paymentMethod].forEach(channel => {
                    const option = document.createElement('option');
                    option.value = channel;
                    option.textContent = channel;
                    paymentDetailsSelect.appendChild(option);
                });
                
                console.log('Populated payment channels for:', paymentMethod);
                
                // Pre-select existing payment details if available
                const existingDetails = "<?php echo htmlspecialchars($booking['payment_details'] ?? ''); ?>";
                if (existingDetails) {
                    // Check if existing details is in the dropdown
                    const options = Array.from(paymentDetailsSelect.options);
                    const foundOption = options.find(option => option.value === existingDetails);
                    
                    if (foundOption) {
                        paymentDetailsSelect.value = existingDetails;
                    } else if (existingDetails) {
                        // If not found, set to "Other" and fill custom input
                        paymentDetailsSelect.value = 'Other';
                        customChannelInput.style.display = 'block';
                        customChannelInput.disabled = false;
                        customChannelInput.required = true;
                        customChannelInput.value = existingDetails;
                    }
                    
                    // Trigger change event to update UI
                    paymentDetailsSelect.dispatchEvent(new Event('change'));
                }
            }
        }
        
        function handleChannelSelection() {
            if (paymentDetailsSelect && customChannelInput) {
                const selectedValue = paymentDetailsSelect.value;
                
                if (selectedValue === 'Other') {
                    // Show and enable custom input
                    customChannelInput.style.display = 'block';
                    customChannelInput.disabled = false;
                    customChannelInput.required = true;
                    customChannelInput.placeholder = 'Enter payment channel name';
                } else {
                    // Hide and disable custom input
                    customChannelInput.style.display = 'none';
                    customChannelInput.disabled = true;
                    customChannelInput.required = false;
                    customChannelInput.value = ''; // Clear value
                }
            }
        }
        
        // Initialize on modal open
        initializePaymentChannels();
        
        // Add event listener for channel selection
        if (paymentDetailsSelect) {
            paymentDetailsSelect.addEventListener('change', handleChannelSelection);
        }
        
        // Initialize custom input state
        handleChannelSelection();
        
    }, 100);
}
</script>
<script>
// Feedback Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    const feedbackLink = document.getElementById('showFeedbackCondition');
    
    if (feedbackLink) {
        feedbackLink.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Check if event is completed to allow feedback
            const bookingStatus = "<?php echo $booking['booking_status']; ?>";
            const feedbackExists = <?php echo $feedback_exists ? 'true' : 'false'; ?>;
            
            if (bookingStatus === 'COMPLETED' && !feedbackExists) {
                const feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
                feedbackModal.show();
            } else if (feedbackExists) {
                alert('You have already submitted feedback for this order. Thank you!');
            } else {
                alert('Feedback is only available for completed events. Your current status: ' + bookingStatus);
            }
        });
    }

    // Star rating functionality
    const starInputs = document.querySelectorAll('.rating-stars input');
    const starLabels = document.querySelectorAll('.rating-stars label');
    
    // Initialize star colors
    starLabels.forEach(label => label.style.color = '#ddd');
    
    starInputs.forEach((input, index) => {
        input.addEventListener('change', function() {
            // Reset all stars
            starLabels.forEach(label => label.style.color = '#ddd');
            
            // Color stars up to selected rating
            for (let i = 0; i <= index; i++) {
                starLabels[i].style.color = '#ffc107';
            }
        });
    });

    // Form submission handling
    const feedbackForm = document.getElementById('feedbackForm');
    if (feedbackForm) {
        feedbackForm.addEventListener('submit', function(e) {
            const rating = document.querySelector('input[name="rating"]:checked');
            if (!rating) {
                e.preventDefault();
                alert('Please select a rating before submitting.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = 'Submitting...';
            submitBtn.disabled = true;
            
            return true;
        });
    }

    // Close modal when hidden and reset form
    const feedbackModalElement = document.getElementById('feedbackModal');
    if (feedbackModalElement) {
        feedbackModalElement.addEventListener('hidden.bs.modal', function () {
            // Reset form
            if (feedbackForm) {
                feedbackForm.reset();
                // Reset stars
                starLabels.forEach(label => label.style.color = '#ddd');
                // Reset submit button
                const submitBtn = feedbackForm.querySelector('button[type="submit"]');
                submitBtn.innerHTML = 'Submit Feedback';
                submitBtn.disabled = false;
            }
        });
    }
});

// Function to open feedback modal (can be called from other parts of your code)
function openFeedbackModal() {
    const feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
    feedbackModal.show();
}
</script>
<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">Event Feedback & Evaluation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="feedbackForm" action="billing.php?order_id=<?php echo $orderId; ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                    <input type="hidden" name="client_name" value="<?php echo htmlspecialchars($booking['full_name']); ?>">
                    <input type="hidden" name="event_theme" value="<?php echo htmlspecialchars($booking['event_theme']); ?>">
                    
                    <!-- Rating Section -->
                    <div class="rating-section mb-4">
                        <h6>Overall Rating <span class="text-danger">*</span></h6>
                        <div class="rating-stars">
                            <input type="radio" id="star5" name="rating" value="5" required>
                            <label for="star5">★</label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4">★</label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3">★</label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2">★</label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1">★</label>
                        </div>
                        <small class="text-muted">Click on a star to rate your experience</small>
                    </div>

                    <!-- Service Categories -->
                    <div class="service-ratings mb-4">
                        <h6>Service Categories</h6>
                        <div class="category-rating">
                            <label>Floral Arrangements</label>
                            <select name="category_floral" class="form-select form-select-sm">
                                <option value="5">Excellent</option>
                                <option value="4">Very Good</option>
                                <option value="3" selected>Good</option>
                                <option value="2">Fair</option>
                                <option value="1">Poor</option>
                            </select>
                        </div>
                        <div class="category-rating">
                            <label>Event Setup</label>
                            <select name="category_setup" class="form-select form-select-sm">
                                <option value="5">Excellent</option>
                                <option value="4">Very Good</option>
                                <option value="3" selected>Good</option>
                                <option value="2">Fair</option>
                                <option value="1">Poor</option>
                            </select>
                        </div>
                        <div class="category-rating">
                            <label>Customer Service</label>
                            <select name="category_service" class="form-select form-select-sm">
                                <option value="5">Excellent</option>
                                <option value="4">Very Good</option>
                                <option value="3" selected>Good</option>
                                <option value="2">Fair</option>
                                <option value="1">Poor</option>
                            </select>
                        </div>
                    </div>

                    <!-- Feedback Text -->
                    <div class="mb-3">
                        <label for="feedback" class="form-label">Your Feedback <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="4" 
                                  placeholder="Please share your experience with our service. What did you like? Any suggestions for improvement?" 
                                  required></textarea>
                    </div>

                    <!-- Would Recommend -->
                    <div class="mb-3">
                        <label class="form-label">Would you recommend Raflora Enterprises to others?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="would_recommend" id="recommend_yes" value="1" checked>
                                <label class="form-check-label" for="recommend_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="would_recommend" id="recommend_no" value="0">
                                <label class="form-check-label" for="recommend_no">No</label>
                            </div>
                        </div>
                    </div>

                    <!-- Anonymous Feedback -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="anonymous" id="anonymous" value="1">
                        <label class="form-check-label" for="anonymous">
                            Submit feedback anonymously
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                </div>
            </form>
            <!-- Feedback Messages -->
<!-- Feedback Messages - MOVED OUTSIDE THE MODAL -->
<?php if (isset($_SESSION['feedback_success'])): ?>
    <div class="alert-feedback alert-success" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px; border-radius: 5px; min-width: 300px;">
        <strong>Success!</strong><br>
        <?php echo $_SESSION['feedback_success']; ?>
        <?php unset($_SESSION['feedback_success']); ?>
    </div>
    <script>
        setTimeout(function() {
            const alert = document.querySelector('.alert-success');
            if (alert) alert.style.display = 'none';
        }, 5000);
    </script>
<?php endif; ?>

<?php if (isset($_SESSION['feedback_error'])): ?>
    <div class="alert-feedback alert-error" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px; border-radius: 5px; min-width: 300px;">
        <strong>Error!</strong><br>
        <?php echo $_SESSION['feedback_error']; ?>
        <?php unset($_SESSION['feedback_error']); ?>
    </div>
    <script>
        setTimeout(function() {
            const alert = document.querySelector('.alert-error');
            if (alert) alert.style.display = 'none';
        }, 5000);
    </script>
<?php endif; ?>
        </div>
    </div>
</div>
</div>
</body>
</html>