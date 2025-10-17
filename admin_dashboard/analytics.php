<?php
// admin_dashboard/analytics.php - FULL PERFORMANCE ANALYTICS DASHBOARD
session_start();
include '../config/db.php';

// Check if analytics class exists, if not include it
if (!class_exists('PerformanceAnalytics')) {
    include '../config/analytics.php';
}

// Check if database connection is working
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

$analytics = new PerformanceAnalytics($conn);

// Get all analytics data
$clientAnalytics = $analytics->getClientBookingAnalytics();
$paymentAnalytics = $analytics->getPaymentAnalytics();
$revenueTrends = $analytics->getRevenueTrends();
$clientRatings = $analytics->getClientRatings();
$quickStats = $analytics->getQuickStats();

// Calculate summary statistics
$totalRevenue = $quickStats['total_revenue'] ?? 0;
$totalBookings = $quickStats['total_bookings'] ?? 0;
$totalClients = $quickStats['total_clients'] ?? 0;
$avgRating = $quickStats['avg_rating'] ?? 0;
$pendingPayments = $quickStats['pending_payments'] ?? 0;
$todayEvents = $quickStats['today_events'] ?? 0;

// Format average rating
$avgRatingFormatted = number_format($avgRating, 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Analytics</title>
    <link rel="stylesheet" href="../assets/css/admin/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin/analytics.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <li>
                <a href="../admin_dashboard/invoice.php" class="list-item-link">
                    <span class="icon"><img src="../assets/images/icon/invoice.png" alt="invoice"></span>
                    <span class="text">Invoice</span>
                </a>
            </li>
            <li class="active">
                <a href="../admin_dashboard/analytics.php" class="list-item-link">
                    <span class="icon"><img src="../assets/images/icon/perfo_analy.png" alt="performance analytics"></span>
                    <span class="text">Performance Analytics</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Performance Analytics</h1>
            <button class="logout-btn"><a href="../api/logout.php">Log-out</a></button>
        </div>

        <div class="dashboard-content">
            <!-- Quick Stats Cards -->
            <div class="quick-stats-grid">
                <div class="stat-card revenue">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <div class="stat-value">₱<?php echo number_format($totalRevenue, 2); ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>
                <div class="stat-card bookings">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $totalBookings; ?></div>
                        <div class="stat-label">Total Bookings</div>
                    </div>
                </div>
                <div class="stat-card clients">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $totalClients; ?></div>
                        <div class="stat-label">Total Clients</div>
                    </div>
                </div>
                <div class="stat-card rating">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $avgRatingFormatted; ?>/5</div>
                        <div class="stat-label">Average Rating</div>
                    </div>
                </div>
            </div>

            <div class="content-grid">
                <!-- Client List -->
                <div class="client-list">
                    <div class="section-header">
                        <h3>Top Clients</h3>
                    </div>
                    <div class="client-table-container">
                        <table class="client-table">
                            <thead>
                                <tr>
                                    <th>Client Name</th>
                                    <th>Bookings</th>
                                    <th>Total Spent</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach(array_slice($clientAnalytics, 0, 8) as $client): ?>
                                <tr>
                                    <td class="client-name"><?php echo htmlspecialchars($client['client_name'] ?: 'Unknown Client'); ?></td>
                                    <td class="booking-count"><?php echo $client['total_bookings']; ?></td>
                                    <td class="total-spent">₱<?php echo number_format($client['total_spent'], 2); ?></td>
                                    <td class="client-status">
                                        <span class="status-badge <?php echo strtolower($client['latest_status'] ?? 'unknown'); ?>">
                                            <?php echo $client['latest_status'] ?? 'No Bookings'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($clientAnalytics)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #95a5a6; padding: 20px;">
                                        No client data available yet.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="charts-section">
                    <!-- Revenue Chart -->
                    <div class="chart-container">
                        <div class="chart-title">Revenue Trends</div>
                        <div class="chart-content">
                            <canvas id="revenueChart" width="400" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Payment Methods Chart -->
                    <div class="chart-container">
                        <div class="chart-title">Payment Methods</div>
                        <div class="chart-content">
                            <canvas id="paymentChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="bottom-section">
                <!-- Client Evaluations -->
                <div class="chart-container large">
                    <div class="chart-title">Client Evaluations & Ratings</div>
                    <div class="evaluations-container">
                        <?php if(!empty($clientRatings)): ?>
                            <?php foreach($clientRatings as $rating): ?>
                            <div class="evaluation-item">
                                <div class="client-avatar">
                                    <?php if(!empty($rating['profile_picture'])): ?>
                                        <img src="../<?php echo htmlspecialchars($rating['profile_picture']); ?>" alt="Profile">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <?php echo strtoupper(substr($rating['client_name'] ?: 'U', 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="evaluation-content">
                                    <div class="client-name"><?php echo htmlspecialchars($rating['client_name'] ?: 'Unknown Client'); ?></div>
                                    <div class="rating-stars">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?php echo $i <= $rating['rating'] ? 'filled' : ''; ?>">★</span>
                                        <?php endfor; ?>
                                        <span class="rating-value">(<?php echo $rating['rating']; ?>/5)</span>
                                    </div>
                                    <div class="feedback">"<?php echo htmlspecialchars($rating['feedback'] ?: 'No feedback provided'); ?>"</div>
                                    <div class="event-info">
                                        <?php echo htmlspecialchars($rating['event_theme'] ?: 'Unknown Event'); ?> 
                                        - <?php echo date('M d, Y', strtotime($rating['rating_date'])); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-data-message">
                                <div class="no-data-icon">⭐</div>
                                <h3>No Ratings Yet</h3>
                                <p>Client ratings will appear here once customers provide feedback.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="chart-container">
                    <div class="chart-title">Quick Overview</div>
                    <div class="quick-overview">
                        <div class="overview-item">
                            <div class="overview-icon">⏳</div>
                            <div class="overview-content">
                                <div class="overview-value"><?php echo $pendingPayments; ?></div>
                                <div class="overview-label">Pending Payments</div>
                            </div>
                        </div>
                        <div class="overview-item">
                            <div class="overview-icon">📅</div>
                            <div class="overview-content">
                                <div class="overview-value"><?php echo $todayEvents; ?></div>
                                <div class="overview-label">Today's Events</div>
                            </div>
                        </div>
                        <div class="overview-item">
                            <div class="overview-icon">✅</div>
                            <div class="overview-content">
                                <div class="overview-value"><?php echo count(array_filter($clientAnalytics, function($client) { return $client['latest_status'] === 'COMPLETED'; })); ?></div>
                                <div class="overview-label">Completed Events</div>
                            </div>
                        </div>
                        <div class="overview-item">
                            <div class="overview-icon">💼</div>
                            <div class="overview-content">
                                <div class="overview-value">
                                    <?php 
                                    $eventTypes = [];
                                    foreach($clientAnalytics as $client) {
                                        if (!empty($client['preferred_event'])) {
                                            $eventTypes[$client['preferred_event']] = true;
                                        }
                                    }
                                    echo count($eventTypes);
                                    ?>
                                </div>
                                <div class="overview-label">Event Types</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Charts -->
    <script>
        // Revenue Trends Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Monthly Revenue',
                    data: [120000, 150000, 180000, 140000, 200000, 220000, 250000, 230000, 210000, 240000, 260000, 280000],
                    borderColor: '#4a6fa5',
                    backgroundColor: 'rgba(74, 111, 165, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Payment Methods Chart
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        const paymentChart = new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Online Bank', 'E-Wallet', 'Cash', 'Credit Card'],
                datasets: [{
                    data: [65, 25, 8, 2],
                    backgroundColor: [
                        '#4a6fa5', '#6b8cbc', '#ff7e5f', '#2ecc71'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '60%'
            }
        });
    </script>
</body>
</html>