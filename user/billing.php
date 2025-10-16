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

    <style>
      .billing-container { display:flex; gap:0; width:100%; min-height:100vh; }
      .billing-left, .billing-right { padding:24px; box-sizing:border-box; }
      body { background:#f5f5f5; }
      
      /* Interrupted transaction notice */
      .interrupted-transaction-notice {
          background: #fff3cd;
          border: 1px solid #ffeaa7;
          padding: 15px;
          border-radius: 6px;
          margin-bottom: 15px;
      }
      
      .interrupted-transaction-notice h4 {
          margin: 0 0 8px 0;
          color: #856404;
      }
      
      .interrupted-transaction-notice p {
          margin: 0;
          color: #856404;
      }

      /* Make sure modal displays properly */
      .modal-backdrop {
          z-index: 1040;
      }
      .modal {
          z-index: 1050;
      }
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
            <?php if ($booking['booking_status'] == 'APPROVED'): ?>
                <a href="../user/my_bookings.php" class="proceed-btn">View My Bookings</a>
            <?php endif; ?>

            <!-- Buttons / Status area -->
            <?php if ($booking['booking_status'] == 'PENDING_ORDER_CONFIRMATION'): ?>
                <?php if ($showPaymentModal): ?>
                    <div class="interrupted-transaction-notice">
                        <h4>🔄 Interrupted Transaction</h4>
                        <p>Your booking was created but payment reference wasn't submitted. Please complete your payment below.</p>
                    </div>
                <?php endif; ?>
                
                <button class="submit-payment-btn" onclick="openBillingPaymentModal()" style="margin-top:12px; width:80%; <?php echo $showPaymentModal ? 'background: #dc3545; border-color: #dc3545;' : ''; ?>">
    <?php echo $showPaymentModal ? 'Complete Payment Reference' : 'Submit Payment Reference'; ?>
</button>
                <div class="status-indicator" style="margin-top:12px; <?php echo $showPaymentModal ? 'color: #dc3545;' : ''; ?>">
                    <?php echo $showPaymentModal ? '⚠️ Status: Payment Reference Required' : 'Status: Waiting for Payment Reference Submission'; ?>
                </div>

            <?php elseif ($booking['booking_status'] == 'PENDING_PAYMENT_VERIFICATION'): ?>
                <div class="status-indicator" style="margin-top:12px;">Status: Waiting for Admin Payment Verification</div>

            <?php elseif ($booking['booking_status'] == 'APPROVED'): ?>
                <div class="status-indicator" style="margin-top:12px; background:#d4edda; border-color:#c3e6cb; color:#155724;">✅ Status: Booking Approved! Your event is confirmed.</div>

            <?php elseif ($booking['booking_status'] == 'REJECTED'): ?>
                <div class="status-indicator" style="margin-top:12px; background:#f8d7da; border-color:#f5c6cb; color:#721c24;">❌ Status: Booking Rejected
                    <?php if (!empty($booking['rejection_reason'])): ?>
                        <a href="../user/my_bookings.php" class="proceed-btn">View My Bookings</a>
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
                        <strong>Bank No.  001234567891</strong><br>
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
                            placeholder="Enter your 12-30 digit Reference code" 
                            minlength="12" maxlength="30"
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
    // Auto-open modal for interrupted transactions
    <?php if ($showPaymentModal): ?>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Showing payment modal for interrupted transaction');
            
            // Use Bootstrap modal to show
            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            paymentModal.show();
            
            // Initialize payment channels with existing booking data
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
                            setTimeout(function() {
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
                            }, 100);
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
                
                // Initialize on page load
                initializePaymentChannels();
                
                // Add event listener for channel selection
                if (paymentDetailsSelect) {
                    paymentDetailsSelect.addEventListener('change', handleChannelSelection);
                }
                
                // Initialize custom input state
                handleChannelSelection();
                
            }, 200);
            
            // Clean URL to prevent modal showing on refresh
            if (window.history.replaceState) {
                const cleanUrl = window.location.href.split('?')[0] + '?order_id=<?php echo $orderId; ?>';
                window.history.replaceState({}, document.title, cleanUrl);
            }
        });
    <?php endif; ?>

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
</body>
</html>