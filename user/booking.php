<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Receipt - Order <?php echo $orderId; ?></title>
    <style>
        /* ========== GENERAL RESET ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.5;
        }

        /* ========== MAIN CONTAINER ========== */
        .billing-container {
            display: flex;
            max-width: 1200px;
            margin: 30px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            min-height: 90vh;
        }

        /* ========== LEFT SIDE (Billing Info) ========== */
        .billing-left {
            flex: 1;
            min-width: 380px;
            background: #ffffff;
            border-right: 1px solid #eaeaea;
            padding: 30px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .billing-left h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1a1a1a;
        }

        .billing-left h3 {
            font-size: 18px;
            font-weight: 600;
            margin-top: 24px;
            margin-bottom: 12px;
            color: #333;
        }

        .billing-address {
            margin-top: 12px;
            background: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
        }

        .billing-address p {
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
        }

        .billing-address strong {
            font-weight: 600;
            margin-bottom: 4px;
            color: #444;
        }

        /* Checkbox styling */
        .billing-left label {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 14px;
        }

        .billing-left input[type="checkbox"] {
            margin-right: 10px;
            margin-top: 2px;
        }

        .billing-left a {
            color: #0066cc;
            text-decoration: none;
        }

        .billing-left a:hover {
            text-decoration: underline;
        }

        /* Button styling */
        .proceed-btn, .submit-payment-btn {
            display: block;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .proceed-btn {
            background-color: #2e7d32;
            color: white;
        }

        .proceed-btn:hover {
            background-color: #1b5e20;
        }

        .submit-payment-btn {
            background-color: #1976d2;
            color: white;
        }

        .submit-payment-btn:hover {
            background-color: #1565c0;
        }

        /* Status indicators */
        .status-indicator {
            margin-top: 12px;
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 14px;
            border: 1px solid;
        }

        /* ========== RIGHT SIDE (Summary) ========== */
        .billing-right {
            flex: 2;
            padding: 30px;
            overflow-y: auto;
            height: 100vh;
        }

        .summary-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #eaeaea;
        }

        .summary-header-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .summary-header img {
            height: 48px;
        }

        .summary-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }

        .summary-info {
            text-align: right;
            font-size: 14px;
            background: #f8f9fa;
            padding: 12px 16px;
            border-radius: 8px;
            min-width: 180px;
        }

        .summary-info div {
            margin-bottom: 6px;
        }

        .summary-info strong {
            display: block;
            margin-bottom: 2px;
            color: #666;
        }

        /* Table styling */
        .summary-table {
            width: 100%;
            margin-top: 18px;
            border-collapse: collapse;
            font-size: 14px;
        }

        .summary-table th {
            text-align: left;
            padding: 12px 0;
            border-bottom: 1px solid #eaeaea;
            font-weight: 600;
            color: #555;
        }

        .summary-table td {
            padding: 16px 0;
            vertical-align: top;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-table th:nth-child(2),
        .summary-table td:nth-child(2) {
            text-align: center;
            width: 15%;
        }

        .summary-table th:nth-child(3),
        .summary-table td:nth-child(3) {
            width: 45%;
        }

        .summary-table th:nth-child(4),
        .summary-table td:nth-child(4) {
            text-align: right;
            width: 20%;
        }

        .inclusions-list {
            margin-top: 8px;
            padding-left: 18px;
        }

        .inclusions-list li {
            margin-bottom: 4px;
        }

        /* File link styling */
        .file-link {
            display: inline-block;
            margin-top: 16px;
            padding: 10px 14px;
            background: #f0f7ff;
            border: 1px solid #c2dfff;
            border-radius: 6px;
            color: #0066cc;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .file-link:hover {
            background: #e1f0ff;
            text-decoration: none;
        }

        /* Totals section */
        .totals-section {
            margin-top: 28px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eaeaea;
        }

        .totals-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .totals-row.total-row {
            font-weight: 700;
            font-size: 16px;
        }

        .totals-row.balance-row {
            font-weight: 700;
            color: #d32f2f;
        }

        /* Recommendations section */
        .recommendations-section {
            margin-top: 24px;
            padding: 18px;
            background: #fff;
            border: 1px solid #eaeaea;
            border-radius: 8px;
        }

        .recommendations-section h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            font-weight: 600;
        }

        .recommendations-section p {
            color: #333;
            margin: 0;
        }

        /* ========== PAYMENT MODAL ========== */
        .payment-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
        }

        .payment-modal-content {
            max-width: 600px;
            margin: 60px auto;
            background: #fff;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .payment-modal h2 {
            margin-bottom: 16px;
            font-size: 22px;
            font-weight: 600;
        }

        .alert {
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: 6px;
            font-size: 14px;
        }

        .alert-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            color: #0d47a1;
        }

        .payment-modal p {
            margin-bottom: 12px;
        }

        .form-group {
            margin: 20px 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .submit-btn {
            background: #1976d2;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 18px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .submit-btn:hover {
            background: #1565c0;
        }

        /* ========== SCROLLBAR STYLING ========== */
        .billing-left::-webkit-scrollbar,
        .billing-right::-webkit-scrollbar {
            width: 6px;
        }

        .billing-left::-webkit-scrollbar-thumb,
        .billing-right::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 6px;
        }

        .billing-left::-webkit-scrollbar-track,
        .billing-right::-webkit-scrollbar-track {
            background-color: transparent;
        }

        /* ========== RESPONSIVE VIEW ========== */
        @media (max-width: 992px) {
            .billing-container {
                flex-direction: column;
                max-width: 100%;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
            }

            .billing-left,
            .billing-right {
                height: auto;
                position: static;
                border: none;
                padding: 20px;
            }

            .billing-left {
                border-bottom: 1px solid #eee;
            }

            .summary-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .summary-info {
                text-align: left;
                margin-top: 16px;
                width: 100%;
            }

            .summary-table {
                font-size: 13px;
            }
        }

        @media (max-width: 576px) {
            .billing-left,
            .billing-right {
                padding: 16px;
            }

            .billing-left h1 {
                font-size: 24px;
            }

            .summary-table th:nth-child(2),
            .summary-table td:nth-child(2) {
                display: none;
            }
        }
    </style>
</head>
<body>

  <div class="billing-container">

    <!-- LEFT (Billing info) - fixed / sticky on desktop -->
    <aside class="billing-left">
        <h1>Thank you for your purchase!</h1>
        <h3>Billing address</h3>

        <div class="billing-address">
            <p><strong>Name</strong><?php echo htmlspecialchars($booking['full_name']); ?></p>
            <p><strong>Address</strong><?php echo htmlspecialchars($booking['address']); ?></p>
            <p><strong>Phone</strong><?php echo htmlspecialchars($booking['mobile_number']); ?></p>
            <p><strong>Email</strong><?php echo htmlspecialchars($booking['email']); ?></p>
        </div>

        <div style="margin-top: 20px;">
            <label style="display:block; margin-bottom:8px;"><input type="checkbox" checked> I read and agree to Privacy Policy</label>
            <label style="display:block; margin-bottom:12px;"><input type="checkbox" checked> I read and agree to Terms and Condition</label>

            <button class="proceed-btn" onclick="confirmBooking()">Proceed</button>

            <!-- Buttons / Status area -->
            <?php if ($booking['booking_status'] == 'PENDING_ORDER_CONFIRMATION'): ?>
                <button class="submit-payment-btn" onclick="openPaymentModal()">Submit Payment Reference</button>
                <div class="status-indicator" style="background:#fff3cd; border-color:#ffeaa7; color:#856404;">Status: Waiting for Payment Reference Submission</div>

            <?php elseif ($booking['booking_status'] == 'PENDING_PAYMENT_VERIFICATION'): ?>
                <div class="status-indicator" style="background:#e2e3e5; border-color:#d6d8db; color:#383d41;">Status: Waiting for Admin Payment Verification</div>

            <?php elseif ($booking['booking_status'] == 'APPROVED'): ?>
                <div class="status-indicator" style="background:#d4edda; border-color:#c3e6cb; color:#155724;">✅ Status: Booking Approved! Your event is confirmed.</div>

            <?php elseif ($booking['booking_status'] == 'REJECTED'): ?>
                <div class="status-indicator" style="background:#f8d7da; border-color:#f5c6cb; color:#721c24;">❌ Status: Booking Rejected
                    <?php if (!empty($booking['rejection_reason'])): ?>
                        <div style="margin-top:12px;"><strong>Reason:</strong> <?php echo htmlspecialchars($booking['rejection_reason']); ?></div>
                    <?php endif; ?>
                </div>

                <div style="margin-top:12px; padding:12px; background:#fff3cd; border:1px solid #ffeaa7; border-radius:6px;">
                    <strong>What to do next?</strong>
                    <p>If you believe this was a mistake or would like to discuss further, please contact our support team.</p>
                </div>

            <?php elseif ($booking['booking_status'] == 'COMPLETED'): ?>
                <div class="status-indicator" style="background:#e2e3e5; border-color:#d6d8db; color:#383d41;">✅ Status: Event Completed Successfully</div>
            <?php endif; ?>
        </div>
    </aside>

    <!-- RIGHT (Summary) - scrollable -->
    <main class="billing-right">
        <div class="summary-header">
            <div class="summary-header-content">
                <img src="../assets/img/raflora_logo.png" alt="RAFLORA Logo">
                <h2>Summary of deliverables</h2>
            </div>

            <div class="summary-info">
                <div><strong>Date</strong><div><?php echo $formattedDate; ?></div></div>
                <div style="margin-top:6px;"><strong>Order number</strong><div><?php echo htmlspecialchars($orderId); ?></div></div>
                <div style="margin-top:6px;"><strong>Payment method</strong><div><?php echo htmlspecialchars($booking['payment_method']); ?></div></div>
            </div>
        </div>

        <table class="summary-table">
            <thead>
                <tr>
                    <th>Item (Event/Package)</th>
                    <th>Price/Unit</th>
                    <th>Package Details</th>
                    <th>Total cost</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Event:</strong> <?php echo htmlspecialchars($booking['event_theme']); ?><br>
                        <strong>Package:</strong> <?php echo htmlspecialchars($booking['packages']); ?>
                    </td>
                    <td>
                        <!-- if you have per-unit price, echo here -->
                    </td>
                    <td>
                        <span style="font-weight:bold;">1 Package</span>
                        <?php if ($inclusions !== 'N/A'): ?>
                            <ul class="inclusions-list">
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
                    <td><?php echo format_price($totalPrice); ?></td>
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
        <div class="totals-section">
            <div class="totals-row total-row">
                <div>GRANDTOTAL (Fixed Price)</div>
                <div><?php echo format_price($totalPrice); ?></div>
            </div>
            <div class="totals-row">
                <div>Amount Paid (Down Payment)</div>
                <div><?php echo format_price($amountDue); ?></div>
            </div>
            <div class="totals-row balance-row">
                <div>Remaining Balance Due:</div>
                <div><?php echo format_price($remainingBalance); ?></div>
            </div>
        </div>

        <!-- Client Recommendations -->
        <div class="recommendations-section">
            <h3>Client Recommendations</h3>
            <p><?php echo nl2br(htmlspecialchars($booking['recommendations'])); ?></p>
        </div>

    </main>

  </div>

  <!-- Payment Reference Modal -->
  <div id="paymentModal" class="payment-modal" style="<?php echo $showPaymentModal ? 'display:block;' : 'display:none;'; ?>">
      <div class="payment-modal-content">
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
                  <input type="text" name="reference_code" id="referenceCode" required placeholder="Enter your Payment Reference code">
              </div>

              <button type="submit" name="submit_reference_from_modal" class="submit-btn">Submit Reference</button>
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
    
    // Function to handle booking confirmation
    function confirmBooking() {
      // This would typically submit the booking to the admin
      // For now, we'll just show an alert
      alert('Booking submitted to admin for confirmation!');
      
      // In a real implementation, you would submit a form or make an AJAX request
      // Example:
      // fetch('../config/client_booking.php', {
      //   method: 'POST',
      //   body: new FormData(document.getElementById('bookingForm'))
      // })
    }
  </script>
</body>
</html>