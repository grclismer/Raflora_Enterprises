<?php
// update.php - Admin Client Management System
session_start();

// Check if admin is logged in (you'll need to implement this)
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
//     header("Location: admin_login.php");
//     exit();
// }

include '../config/db.php';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $booking_id = intval($_POST['booking_id']);
    $status = $_POST['status'];
    $reason = $_POST['reason'] ?? '';
    
    // First, check if rejection_reason column exists, if not add it
    $check_column = $conn->query("SHOW COLUMNS FROM booking_tbl LIKE 'rejection_reason'");
    if ($check_column->num_rows == 0) {
        $conn->query("ALTER TABLE booking_tbl ADD COLUMN rejection_reason TEXT NULL");
    }
    
    // Start transaction to ensure both updates succeed
    $conn->begin_transaction();
    
    try {
        if ($status === 'REJECTED' && !empty($reason)) {
            $sql = "UPDATE booking_tbl SET booking_status = ?, rejection_reason = ? WHERE booking_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $status, $reason, $booking_id);
        } else {
            $sql = "UPDATE booking_tbl SET booking_status = ? WHERE booking_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $status, $booking_id);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating booking: " . $stmt->error);
        }
        
        // ✅ CRITICAL FIX: Update payment status when admin approves booking
        if ($status === 'APPROVED' || $status === 'COMPLETED') {
            $payment_status = ($status === 'APPROVED') ? 'verified' : 'completed';
            
            $payment_sql = "UPDATE payments_tbl SET status = ? WHERE booking_id = ?";
            $payment_stmt = $conn->prepare($payment_sql);
            $payment_stmt->bind_param("si", $payment_status, $booking_id);
            
            if (!$payment_stmt->execute()) {
                throw new Exception("Error updating payment status: " . $payment_stmt->error);
            }
            $payment_stmt->close();
        }
        
        $conn->commit();
        $_SESSION['success_message'] = "Booking status updated successfully!";
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = $e->getMessage();
    }
    
    header("Location: update.php");
    exit();
}
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
        <h2>Booking Details #'.$booking['booking_id'].'</h2>
        <div class="booking-details-grid">
            <div class="detail-section">
                <h3>Client Information</h3>
                <div class="detail-item"><strong>Name:</strong> '.htmlspecialchars($booking['full_name']).'</div>
                <div class="detail-item"><strong>Email:</strong> '.htmlspecialchars($booking['email']).'</div>
                <div class="detail-item"><strong>Phone:</strong> '.htmlspecialchars($booking['mobile_number']).'</div>
                <div class="detail-item"><strong>Address:</strong> '.htmlspecialchars($booking['address']).'</div>
            </div>
            <div class="detail-section">
                <h3>Event Details</h3>
                <div class="detail-item"><strong>Event:</strong> '.htmlspecialchars($booking['event_theme']).'</div>
                <div class="detail-item"><strong>Package:</strong> '.htmlspecialchars($booking['packages']).'</div>
                <div class="detail-item"><strong>Event Date:</strong> '.date('M d, Y', strtotime($booking['event_date'])).'</div>
                <div class="detail-item"><strong>Event Time:</strong> '.htmlspecialchars($booking['event_time']).'</div>
            </div>
        </div>
        <div class="detail-section">
            <h3>Payment Information</h3>
            <div class="detail-item"><strong>Total Price:</strong> ₱'.number_format($booking['total_price'], 2).'</div>
            <div class="detail-item"><strong>Amount Due:</strong> ₱'.number_format($booking['amount_due'], 2).'</div>
            <div class="detail-item"><strong>Payment Method:</strong> '.htmlspecialchars($booking['payment_method']).'</div>
            <div class="detail-item"><strong>Payment Type:</strong> '.htmlspecialchars($booking['payment_type']).'</div>
            <div class="detail-item"><strong>Reference Code:</strong> '.(!empty($booking['reference_number']) ? '<code>'.htmlspecialchars($booking['reference_number']).'</code>' : 'No reference').'</div>
            <div class="detail-item"><strong>Status:</strong> <span class="status-badge status-'.strtolower(str_replace('_', '-', $booking['booking_status'])).'">'.str_replace('_', ' ', $booking['booking_status']).'</span></div>';
        
        if ($booking['booking_status'] === 'REJECTED' && !empty($booking['rejection_reason'])) {
            echo '<div class="detail-item"><strong>Rejection Reason:</strong> '.htmlspecialchars($booking['rejection_reason']).'</div>';
        }
        
        echo '</div>
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

// Get all bookings for the main table
$bookings = [];
$sql = "SELECT b.booking_id, b.full_name, b.email, b.mobile_number, b.address, 
               b.event_theme, b.packages, b.event_date, b.total_price, b.amount_due,
               b.payment_method, b.payment_type, b.booking_status, b.reference_number,
               b.created_at
        FROM booking_tbl b 
        ORDER BY b.created_at DESC";

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Updates</title>
    <link rel="stylesheet" href="../assets/css/admin/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin/update.css">
   
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
            <li class="active">
                <a href="../admin_dashboard/update.php" class="list-item-link">
                    <span class="icon"><img src="../assets/images/icon/client_updates.png" alt="client updates"></span>
                    <span class="text">Client updates</span>
                </a>
            </li>
            <li>
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
            <h1>Client updates</h1>
            <button class="logout-btn"><a href="../api/logout.php">Log-out</a></button>
        </div>

        <div class="dashboard-content">
            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert-success">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert-error">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <div class="section-header">
                <h2>Client Bookings Management</h2>
                <input type="text" class="search-box" placeholder="🔍 Search clients..." id="searchInput">
            </div>

            <div class="filters">
                <button class="filter-btn active" data-status="all">All Bookings</button>
                <button class="filter-btn" data-status="PENDING_ORDER_CONFIRMATION">Pending Order</button>
                <button class="filter-btn" data-status="PENDING_PAYMENT_VERIFICATION">Pending Verification</button>
                <button class="filter-btn" data-status="APPROVED">Approved</button>
                <button class="filter-btn" data-status="REJECTED">Rejected</button>
                <button class="filter-btn" data-status="COMPLETED">Completed</button>
            </div>

            <div class="tools-table">
                <div class="table-container">
                    <table class="bookings-table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Client Name</th>
                                <th>Event</th>
                                <th>Event Date</th>
                                <th>Total Price</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Reference</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsTable">
                            <?php foreach ($bookings as $booking): 
                                $status_class = strtolower(str_replace('_', '-', $booking['booking_status']));
                            ?>
                            <tr data-status="<?php echo $booking['booking_status']; ?>">
                                <td>#<?php echo $booking['booking_id']; ?></td>
                                <td><?php echo htmlspecialchars($booking['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['event_theme']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($booking['event_date'])); ?></td>
                                <td>₱<?php echo number_format($booking['total_price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($booking['payment_method']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $status_class; ?>">
                                        <?php echo str_replace('_', ' ', $booking['booking_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($booking['reference_number']) && $booking['reference_number'] !== 'NULL'): ?>
                                        <code><?php echo htmlspecialchars($booking['reference_number']); ?></code>
                                    <?php else: ?>
                                        <em>No reference</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="action-btn btn-view" onclick="viewBooking(<?php echo $booking['booking_id']; ?>)">
                                        View
                                    </button>
                                    <?php if ($booking['booking_status'] === 'PENDING_PAYMENT_VERIFICATION'): ?>
                                    <button class="action-btn btn-approve" onclick="approveBooking(<?php echo $booking['booking_id']; ?>)">
                                        Approve
                                    </button>
                                    <button class="action-btn btn-reject" onclick="rejectBooking(<?php echo $booking['booking_id']; ?>)">
                                        Reject
                                    </button>
                                    <?php elseif ($booking['booking_status'] === 'APPROVED'): ?>
                                    <button class="action-btn btn-complete" onclick="completeBooking(<?php echo $booking['booking_id']; ?>)">
                                        Complete
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalContent">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>

    <!-- Status Update Forms -->
    <form id="approveForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="update_status">
    <input type="hidden" name="booking_id" id="approveBookingId">
    <input type="hidden" name="status" value="APPROVED">
    </form>

    <form id="rejectForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="update_status">
        <input type="hidden" name="booking_id" id="rejectBookingId">
        <input type="hidden" name="status" value="REJECTED">
        <input type="hidden" name="reason" id="rejectReason">
    </form>

    <form id="completeForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="update_status">
    <input type="hidden" name="booking_id" id="completeBookingId">
    <input type="hidden" name="status" value="COMPLETED">
    </form>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#bookingsTable tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const status = this.dataset.status;
                const rows = document.querySelectorAll('#bookingsTable tr');
                
                rows.forEach(row => {
                    if (status === 'all' || row.dataset.status === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        function viewBooking(bookingId) {
            fetch('update.php?view_booking=' + bookingId)
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.text();
                })
                .then(html => {
                    document.getElementById('modalContent').innerHTML = html;
                    document.getElementById('bookingModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading booking details');
                });
        }

        function approveBooking(bookingId) {
            if (confirm('Approve this booking and confirm payment?')) {
                document.getElementById('approveBookingId').value = bookingId;
                document.getElementById('approveForm').submit();
            }
        }

        function rejectBooking(bookingId) {
            const reason = prompt('Please enter reason for rejection:');
            if (reason !== null && reason.trim() !== '') {
                document.getElementById('rejectBookingId').value = bookingId;
                document.getElementById('rejectReason').value = reason;
                document.getElementById('rejectForm').submit();
            } else if (reason !== null) {
                alert('Please provide a reason for rejection.');
            }
        }

        function completeBooking(bookingId) {
            if (confirm('Mark this booking as completed?')) {
                document.getElementById('completeBookingId').value = bookingId;
                document.getElementById('completeForm').submit();
            }
        }

        function closeModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('bookingModal');
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