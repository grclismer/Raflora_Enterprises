<?php
// invoice.php - Dynamic Invoice Management System
session_start();

// Check if admin is logged in
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
//     header("Location: admin_login.php");
//     exit();
// }

include '../config/db.php';

// Handle booking details view via AJAX
if (isset($_GET['view_booking'])) {
    $booking_id = intval($_GET['view_booking']);
    $sql = "SELECT * FROM booking_tbl WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    
    if ($booking) {
        echo '
        <h2>Invoice Details #'.$booking['booking_id'].'</h2>
        <div class="booking-details-grid">
            <div class="detail-section">
                <h3>Client Information</h3>
                <div class="detail-item"><strong>Name:</strong> '.htmlspecialchars($booking['full_name']).'</div>
                <div class="detail-item"><strong>Email:</strong> '.htmlspecialchars($booking['email']).'</div>
                <div class="detail-item"><strong>Phone:</strong> '.htmlspecialchars($booking['mobile_number']).'</div>
                <div class="detail-item"><strong>Address:</strong> '.htmlspecialchars($booking['address']).'</div>
            </div>
            <div class="detail-section">
                <h3>Event & Payment Details</h3>
                <div class="detail-item"><strong>Event:</strong> '.htmlspecialchars($booking['event_theme']).'</div>
                <div class="detail-item"><strong>Package:</strong> '.htmlspecialchars($booking['packages']).'</div>
                <div class="detail-item"><strong>Event Date:</strong> '.date('M d, Y', strtotime($booking['event_date'])).'</div>
                <div class="detail-item"><strong>Event Time:</strong> '.htmlspecialchars($booking['event_time']).'</div>
                <div class="detail-item"><strong>Payment Date:</strong> '.date('M d, Y', strtotime($booking['created_at'])).'</div>
            </div>
        </div>
        <div class="detail-section">
            <h3>Financial Information</h3>
            <div class="detail-item"><strong>Total Price:</strong> ₱'.number_format($booking['total_price'], 2).'</div>
            <div class="detail-item"><strong>Amount Due:</strong> ₱'.number_format($booking['amount_due'], 2).'</div>
            <div class="detail-item"><strong>Payment Method:</strong> '.htmlspecialchars($booking['payment_method']).'</div>
            <div class="detail-item"><strong>Payment Type:</strong> '.htmlspecialchars($booking['payment_type']).'</div>
            <div class="detail-item"><strong>Payment Channel:</strong> '.htmlspecialchars($booking['payment_details']).'</div>
            <div class="detail-item"><strong>Reference Code:</strong> '.(!empty($booking['reference_number']) ? '<code>'.htmlspecialchars($booking['reference_number']).'</code>' : 'No reference').'</div>
            <div class="detail-item"><strong>Status:</strong> <span class="status-badge status-'.strtolower(str_replace('_', '-', $booking['booking_status'])).'">'.str_replace('_', ' ', $booking['booking_status']).'</span></div>
        </div>
        <div class="detail-section">
            <h3>Design & Recommendations</h3>
            <div class="detail-item"><strong>Design Document:</strong> '.($booking['design_document_path'] != 'assets/uploads/default.jpg' ? '<a href="../'.$booking['design_document_path'].'" target="_blank">View Design</a>' : 'No design uploaded').'</div>
            <div class="detail-item"><strong>Client Recommendations:</strong> '.(!empty($booking['recommendations']) ? htmlspecialchars($booking['recommendations']) : 'No recommendations provided').'</div>
        </div>';
    } else {
        echo '<p>Booking not found.</p>';
    }
    exit();
}

// Get all bookings for the invoice table (only completed/payment verified bookings)
$invoices = [];
$sql = "SELECT b.booking_id, b.full_name, b.email, b.mobile_number, b.address, 
               b.event_theme, b.packages, b.event_date, b.total_price, b.amount_due,
               b.payment_method, b.payment_details, b.payment_type, b.booking_status, 
               b.reference_number, b.created_at, b.design_document_path
        FROM booking_tbl b 
        WHERE b.booking_status IN ('APPROVED', 'COMPLETED', 'PENDING_PAYMENT_VERIFICATION')
        ORDER BY b.created_at DESC";

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $invoices[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Invoice</title>
    <link rel="stylesheet" href="../assets/css/admin/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin/invoice.css">
    <style>
        /* Modal and additional styles */
        .modal { 
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0,0,0,0.5); 
        }
        .modal-content { 
            background: white; 
            margin: 5% auto; 
            padding: 30px; 
            width: 80%; 
            max-width: 800px; 
            border-radius: 12px; 
            position: relative;
            max-height: 80vh;
            overflow-y: auto;
        }
        .close { 
            position: absolute; 
            right: 15px; 
            top: 15px; 
            font-size: 28px; 
            font-weight: bold; 
            cursor: pointer; 
        }
        
        .booking-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }
        .detail-section h3 {
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        .detail-item {
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .detail-item strong {
            display: inline-block;
            width: 180px;
            color: #555;
        }
        
        .status-badge { 
            padding: 4px 8px; 
            border-radius: 12px; 
            font-size: 12px; 
            font-weight: bold;
        }
        .status-pending-payment-verification { background: #d1ecf1; color: #0c5460; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-completed { background: #e2e3e5; color: #383d41; }
        
        .search-box { 
            padding: 10px 15px; 
            width: 300px; 
            border: 1px solid #ddd; 
            border-radius: 20px; 
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .action-btn { 
            padding: 6px 12px; 
            margin: 2px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 12px;
            background: #17a2b8; 
            color: white; 
        }
        
        .invoice-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="raflora logo">
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="../admin_dashboard/inventory.php" class="list-item-link">
                    <span class="icon"><img src="../assets/images/icon/tools_equipment.png" alt="inventory"></span>
                    <span class="text">Tools and Equipment</span>
                </a>
            </li>
            <li>
                <a href="../admin_dashboard/update.php" class="list-item-link">
                    <span class="icon"><img src="../assets/images/icon/client_updates.png" alt="client updates"></span>
                    <span class="text">Client updates</span>
                </a>
            </li>
            <li class="active">
                <a href="../admin_dashboard/invoice.php" class="list-item-link">
                    <span class="icon"><img src="../assets/images/icon/invoice.png" alt="invoice"></span>
                    <span class="text">Invoice</span>
                </a>
            </li>
            <li>
                <a href="../admin_dashboard/analytics.php" class="list-item-link">
                    <span class="icon"><img src="../assets/images/icon/perfo_analy.png" alt="performance analytics"></span>
                    <span class="text">Performance Analytics</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Sales Invoice</h1>
            <button class="logout-btn"><a href="../api/logout.php">Log-out</a></button>
        </div>

        <div class="dashboard-content">
            <!-- Invoice Summary -->
            <div class="invoice-summary">
                <h3>Invoice Summary</h3>
                <div class="summary-stats">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($invoices); ?></div>
                        <div class="stat-label">Total Invoices</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">₱<?php echo number_format(array_sum(array_column($invoices, 'total_price')), 2); ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">₱<?php echo number_format(array_sum(array_column($invoices, 'amount_due')), 2); ?></div>
                        <div class="stat-label">Pending Collection</div>
                    </div>
                </div>
            </div>

            <div class="section-header">
                <h2>Invoice Records</h2>
                <input type="text" class="search-box" placeholder="🔍 Search invoices..." id="searchInput">
            </div>

            <div class="invoice-table">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Client Name</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>Event Theme</th>
                                <th>Payment Date</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceTable">
                            <?php foreach ($invoices as $invoice): 
                                $status_class = strtolower(str_replace('_', '-', $invoice['booking_status']));
                            ?>
                            <tr>
                                <td>#<?php echo $invoice['booking_id']; ?></td>
                                <td><?php echo htmlspecialchars($invoice['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($invoice['address']); ?></td>
                                <td><?php echo htmlspecialchars($invoice['email']); ?></td>
                                <td><?php echo htmlspecialchars($invoice['mobile_number']); ?></td>
                                <td><?php echo htmlspecialchars($invoice['event_theme']); ?></td>
                                <td><?php echo date('m-d-Y', strtotime($invoice['created_at'])); ?></td>
                                <td>₱<?php echo number_format($invoice['total_price'], 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $status_class; ?>">
                                        <?php echo str_replace('_', ' ', $invoice['booking_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="payment-badge payment-<?php echo strtolower(str_replace(' ', '-', $invoice['payment_method'])); ?>">
                                        <?php echo htmlspecialchars($invoice['payment_method']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn" onclick="viewInvoice(<?php echo $invoice['booking_id']; ?>)">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Details Modal -->
    <div id="invoiceModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalContent">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#invoiceTable tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        function viewInvoice(bookingId) {
            fetch('invoice.php?view_booking=' + bookingId)
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.text();
                })
                .then(html => {
                    document.getElementById('modalContent').innerHTML = html;
                    document.getElementById('invoiceModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading invoice details');
                });
        }

        function closeModal() {
            document.getElementById('invoiceModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('invoiceModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>