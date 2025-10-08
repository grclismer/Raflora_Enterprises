<?php
session_start();

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

// UPDATED: Check if we should show the payment modal
$showPaymentModal = false;

// Method 1: Check URL parameter (for direct access from my_bookings.php)
if (isset($_GET['trigger_modal']) && $_GET['trigger_modal'] == 1) {
    if ($booking && $booking['booking_status'] === 'PENDING_ORDER_CONFIRMATION') {
        $showPaymentModal = true;
    }
}

// Method 2: Check session (for redirects from other pages)
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

    <!-- KEEP your original CSS links (place cleaned CSS into these file paths if you want new styling) -->
    <link rel="stylesheet" href="../assets/css/user/billing_modal.css">
    <link rel="stylesheet" href="../assets/css/user/billing.css">

    <style>
      /* Small safety CSS to ensure layout won't be fully broken if your external css missing.
         This will be overridden by your billing.css when it loads. */
      .billing-container { display:flex; gap:0; width:100%; min-height:100vh; }
      .billing-left, .billing-right { padding:24px; box-sizing:border-box; }
      body { background:#f5f5f5; }
    </style>
</head>
<body>

  <div class="billing-container">

    <!-- LEFT (Billing info) - fixed / sticky on desktop -->
    <aside class="billing-left" style="width: 40%; min-width: 320px; background: #fff; border-right: 1px solid #eee; position: sticky; top:0; height:100vh; overflow:auto;">
        <h1 style="font-size:28px; font-weight:800; margin-bottom:12px;">Thank you for your purchase!</h1>
        <h3 style="font-size:18px; margin-top:18px;">Billing address</h3>

        <div class="billing-address" style="margin-top:12px;">
            <p><strong>Name</strong><?php echo htmlspecialchars($booking['full_name']); ?></p>
            <p><strong>Address</strong><?php echo htmlspecialchars($booking['address']); ?></p>
            <p><strong>Phone</strong><?php echo htmlspecialchars($booking['mobile_number']); ?></p>
            <p><strong>Email</strong><?php echo htmlspecialchars($booking['email']); ?></p>
        </div>

        <div style="margin-top: 20px;">
            <label style="display:block; margin-bottom:8px;"><input type="checkbox" unchecked required> I read and agree to <a href="#" id="showPrivacyPolicy">Privacy Policy</a></label>
            <label style="display:block; margin-bottom:12px;"><input type="checkbox" unchecked required> I read and agree to <a href="#" id="showTermsCondition">Terms and Condition</a></label>

            <!-- <button class="proceed-btn" onclick="window.print()" style="display:block; width:80%; margin-top:10px;">Proceed</button> -->
            <button class="proceed-btn" href="../user/my_bookings.php">Proceed</button>
            <!-- Buttons / Status area (kept same conditions as original) -->
            <?php if ($booking['booking_status'] == 'PENDING_ORDER_CONFIRMATION'): ?>
                <button class="submit-payment-btn" onclick="openPaymentModal()" style="margin-top:12px; width:80%;">Submit Payment Reference</button>
                <div class="status-indicator" style="margin-top:12px;">Status: Waiting for Payment Reference Submission</div>

            <?php elseif ($booking['booking_status'] == 'PENDING_PAYMENT_VERIFICATION'): ?>
                <div class="status-indicator" style="margin-top:12px;">Status: Waiting for Admin Payment Verification</div>

            <?php elseif ($booking['booking_status'] == 'APPROVED'): ?>
                <div class="status-indicator" style="margin-top:12px; background:#d4edda; border-color:#c3e6cb; color:#155724;">✅ Status: Booking Approved! Your event is confirmed.</div>

            <?php elseif ($booking['booking_status'] == 'REJECTED'): ?>
                <div class="status-indicator" style="margin-top:12px; background:#f8d7da; border-color:#f5c6cb; color:#721c24;">❌ Status: Booking Rejected
                    <?php if (!empty($booking['rejection_reason'])): ?>
                        <div style="margin-top:12px;"><strong>Reason:</strong> <?php echo htmlspecialchars($booking['rejection_reason']); ?></div>
                    <?php endif; ?>
                </div>

                <div style="margin-top:12px; padding:12px; background:#fff3cd; border:1px solid #ffeaa7; border-radius:6px;">
                    <strong>What to do next?</strong>
                    <p>If you believe this was a mistake or would like to discuss further, please contact our support team.</p>
                </div>

            <?php elseif ($booking['booking_status'] == 'COMPLETED'): ?>
                <div class="status-indicator" style="margin-top:12px; background:#e2e3e5; border-color:#d6d8db; color:#383d41;">✅ Status: Event Completed Successfully</div>
            <?php endif; ?>

            <a href="#" class="feedback-link" id="showFeedbackCondition" style="display:block; margin-top:18px;">Feedback and evaluation</a>
        </div>
    </aside>

    <!-- RIGHT (Summary) - scrollable -->
    <main class="billing-right" style="flex:1; height:100vh; overflow:auto; background:#fafafa;">
        <div class="summary-header" style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <img src="../assets/images/logo/raflora-logo.jpg" alt="RAFLORA Logo" style="height:48px;">
                <h2 style="margin:0; font-size:22px;">Summary of deliverables</h2>
            </div>

            <div class="summary-info" style="text-align:right;">
                <div><strong>Date</strong><div><?php echo $formattedDate; ?></div></div>
                <div style="margin-top:6px;"><strong>Order number</strong><div><?php echo htmlspecialchars($orderId); ?></div></div>
                <div style="margin-top:6px;"><strong>Payment method</strong><div><?php echo htmlspecialchars($booking['payment_method']); ?></div></div>
            </div>
        </div>

        <table class="summary-table" style="width:100%; margin-top:18px; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:8px 0;">Item (Event/Package)</th>
                    <th style="text-align:center; padding:8px 0;">Price/Unit</th>
                    <th style="text-align:left; padding:8px 0;">Package Details</th>
                    <th style="text-align:right; padding:8px 0;">Total cost</th>
                </tr>
            </thead>
            <tbody>
                <tr style="vertical-align:top;">
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
        <div style="margin-top:28px; padding:16px; background:#fff; border-radius:8px;">
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
                <div style="font-weight:800; color:#cc0000;"><?php echo format_price($remainingBalance); ?></div>
            </div>
        </div>

        <!-- Client Recommendations & feedback -->
        <div style="margin-top:18px; padding:14px; background:#fff; border-radius:8px;">
            <h3 style="margin:0 0 8px 0;">Client Recommendations</h3>
            <p style="color:#333; margin:0;"><?php echo nl2br(htmlspecialchars($booking['recommendations'])); ?></p>
        </div>

        <!-- <div style="margin-top:22px; text-align:center;">
            <button class="proceed-btn" onclick="window.print()" style="padding:12px 24px; border-radius:8px;">Print Receipt</button>
        </div> -->

    </main>

  </div>

  <!-- Payment Reference Modal (keeps original form/action names) -->
  <div id="paymentModal" class="payment-modal" style="<?php echo $showPaymentModal ? 'display:block;' : 'display:none;'; ?>">
      <div class="payment-modal-content" style="max-width:600px; margin:60px auto; background:#fff; padding:20px; border-radius:8px;">
          <h2>Submit Payment Reference</h2>

          <div class="alert alert-info">Please pay the required amount to get the <strong>Reference code</strong>.</div>

          <p><strong>Order ID:</strong> #<?php echo $orderId; ?></p>
          <p><strong>Amount Due:</strong> <span style="color:#28a745; font-weight:bold;"><?php echo format_price($amountDue); ?></span></p>
          <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($booking['payment_method']); ?></p>
          <p><strong>Payment Channel:</strong> <?php echo htmlspecialchars($booking['payment_details']); ?></p>
          <p><strong>Payment Type:</strong> <?php echo htmlspecialchars($booking['payment_type']); ?></p>

          <form action="../config/client_booking.php" method="POST" id="referenceForm">
              <input type="hidden" name="order_id_value" value="<?php echo $orderId; ?>">
              <input type="hidden" name="payment_details" value="<?php echo htmlspecialchars($booking['payment_details']); ?>">

              <div class="form-group">
                  <label for="referenceCode">Reference ID/Code</label>
                  <input type="text" name="reference_code" id="referenceCode" required placeholder="Enter your Payment Reference code" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
              </div>

              <button type="submit" name="submit_reference_from_modal" class="submit-btn" style="background:#28a745; color:#fff; padding:10px 18px; border-radius:6px; border:none;">Submit Reference</button>
          </form>
      </div>
  </div>

  <script>
    function openPaymentModal() {
      var modal = document.getElementById('paymentModal');
      modal.style.display = 'block';
      document.body.classList.add('modal-open');
    }
    function closePaymentModal() {
      var modal = document.getElementById('paymentModal');
      modal.style.display = 'none';
      document.body.classList.remove('modal-open');
    }
    // If the server determined we should show it, modal is already visible (via PHP $showPaymentModal)
    if (<?php echo $showPaymentModal ? 'true' : 'false'; ?>) {
      document.body.classList.add('modal-open');
    }
  </script>
</body>
</html>
