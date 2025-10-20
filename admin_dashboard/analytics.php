<?php
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
$clientRatings = $analytics->getClientRatings(); // This should work now
$quickStats = $analytics->getQuickStats();
$clientFeedback = $analytics->getClientFeedback(); // Direct call for feedback data

// Get real revenue data
$monthlyRevenue = $analytics->getMonthlyRevenue();

// Prepare data for the chart
$revenueMonths = [];
$revenueData = [];

// If we have real data, use it
if (!empty($monthlyRevenue)) {
    foreach ($monthlyRevenue as $revenue) {
        $revenueMonths[] = $revenue['month'];
        $revenueData[] = floatval($revenue['revenue']);
    }
} else {
    // Fallback to sample data if no real data
    $revenueMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $revenueData = [120000, 150000, 180000, 140000, 200000, 220000, 250000, 230000, 210000, 240000, 260000, 280000];
}

// Get current month for highlighting
$currentMonth = date('M');

// Calculate summary statistics
$totalRevenue = $quickStats['total_revenue'] ?? 0;


// Calculate summary statistics
$totalRevenue = $quickStats['total_revenue'] ?? 0;
$totalBookings = $quickStats['total_bookings'] ?? 0;
$totalClients = $quickStats['total_clients'] ?? 0;
$avgRating = $quickStats['avg_rating'] ?? 0;
$pendingPayments = $quickStats['pending_payments'] ?? 0;
$todayEvents = $quickStats['today_events'] ?? 0;

// Calculate completed events count
$completedEvents = count(array_filter($clientAnalytics, function($client) { 
    return ($client['latest_status'] ?? '') === 'COMPLETED'; 
}));

// Calculate unique event types
$eventTypes = [];
foreach($clientAnalytics as $client) {
    if (!empty($client['preferred_event'])) {
        $eventTypes[$client['preferred_event']] = true;
    }
}
$uniqueEventTypes = count($eventTypes);

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
                    <th>Client Tier</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(array_slice($clientAnalytics, 0, 8) as $client): 
                    $totalSpent = $client['total_spent'] ?? 0;
                    // Define client tiers based on total spending
                    if ($totalSpent >= 50000) {
                        $tier = 'VIP';
                        $tierClass = 'vip-tier';
                        $tierDescription = 'Elite Client';
                    } elseif ($totalSpent >= 20000) {
                        $tier = 'Premium';
                        $tierClass = 'premium-tier';
                        $tierDescription = 'Loyal Client';
                    } elseif ($totalSpent >= 5000) {
                        $tier = 'Standard';
                        $tierClass = 'standard-tier';
                        $tierDescription = 'Regular Client';
                    } else {
                        $tier = 'New';
                        $tierClass = 'new-tier';
                        $tierDescription = 'New Client';
                    }
                ?>
                <tr>
                    <td class="client-name"><?php echo htmlspecialchars($client['client_name'] ?: 'Unknown Client'); ?></td>
                    <td class="booking-count"><?php echo $client['total_bookings']; ?></td>
                    <td class="total-spent">₱<?php echo number_format($totalSpent, 2); ?></td>
                    <td class="client-tier">
                        <span class="tier-badge <?php echo $tierClass; ?>" title="<?php echo $tierDescription; ?>">
                            <?php echo $tier; ?>
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
                    <div class="chart-container revenue-chart">
                        <div class="chart-title">Revenue Trends</div>
                        <div class="chart-content">
                            <canvas id="revenueChart" width="400" height="270"></canvas>
                        </div>
                    </div>

                    <!-- Payment Methods Chart -->
                    <div class="chart-container payment-methods">
                        <div class="chart-title">Payment Methods</div>
                        <div class="chart-content">
                            <canvas id="paymentChart" width="400" height="270"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Overview Section -->
<div class="chart-container quick-overview-section">
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
                <div class="overview-value"><?php echo $completedEvents; ?></div>
                <div class="overview-label">Completed Events</div>
            </div>
        </div>
        <div class="overview-item">
            <div class="overview-icon">💼</div>
            <div class="overview-content">
                <div class="overview-value"><?php echo $uniqueEventTypes; ?></div>
                <div class="overview-label">Event Types</div>
            </div>
        </div>
    </div>
</div>

            <!-- Bottom Section -->
            <div class="bottom-section">
            <!-- Client Evaluations -->
<!-- Client Evaluations -->
<!-- Client Evaluations -->
<div class="chart-container large">
    <div class="chart-title">Client Evaluations & Ratings</div>
    <div class="evaluations-container">
        <?php if(!empty($clientFeedback)): ?>
            <?php foreach($clientFeedback as $feedback): ?>
                <?php 
                $display_name = $feedback['is_anonymous'] ? 'Anonymous Client' : $feedback['client_name'];
                $initial = $feedback['is_anonymous'] ? 'A' : strtoupper(substr($feedback['client_name'], 0, 1));
                
                // Try to get user profile picture from accounts_tbl
                $profile_picture = null;
                if (!$feedback['is_anonymous']) {
                    $user_sql = "SELECT profile_picture FROM accounts_tbl WHERE CONCAT(first_name, ' ', last_name) = ? LIMIT 1";
                    $user_stmt = $conn->prepare($user_sql);
                    $user_stmt->bind_param("s", $feedback['client_name']);
                    $user_stmt->execute();
                    $user_result = $user_stmt->get_result();
                    
                    if ($user_result->num_rows > 0) {
                        $user_data = $user_result->fetch_assoc();
                        $profile_picture = $user_data['profile_picture'];
                    }
                    $user_stmt->close();
                }
                ?>
                <div class="evaluation-item">
                    <div class="client-avatar">
                        <?php if ($profile_picture && !empty($profile_picture)): ?>
                            <img src="../<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile" class="avatar-image">
                        <?php else: ?>
                            <div class="avatar-placeholder">
                                <?php echo $initial; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="evaluation-content">
                        <div class="client-name">
                            <?php echo htmlspecialchars($display_name); ?>
                            <small class="order-number">Order #<?php echo $feedback['order_id']; ?></small>
                        </div>
                        <div class="rating-stars">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?php echo $i <= $feedback['rating'] ? 'filled' : ''; ?>">★</span>
                            <?php endfor; ?>
                            <span class="rating-value">(<?php echo $feedback['rating']; ?>/5)</span>
                        </div>
                        <div class="category-ratings">
                            <small>
                                Floral: <?php echo $feedback['category_floral']; ?>/5 • 
                                Setup: <?php echo $feedback['category_setup']; ?>/5 • 
                                Service: <?php echo $feedback['category_service']; ?>/5
                            </small>
                        </div>
                        <div class="feedback">
                            "<?php echo htmlspecialchars($feedback['feedback'] ?: 'No detailed feedback provided'); ?>"
                        </div>
                        <div class="event-info">
                            <?php echo htmlspecialchars($feedback['event_theme']); ?> 
                            - <?php echo date('M d, Y', strtotime($feedback['rating_date'])); ?>
                            <?php if($feedback['would_recommend']): ?>
                                <span class="recommend-badge">✓ Would recommend</span>
                            <?php endif; ?>
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
            </div>
            
        </div>
    </div>

    <!-- JavaScript for Charts -->
    <!-- JavaScript for Charts -->
<script>
    // Revenue Trends Chart with Real Data
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($revenueMonths); ?>,
            datasets: [{
                label: 'Monthly Revenue',
                data: <?php echo json_encode($revenueData); ?>,
                borderColor: '#4a6fa5',
                backgroundColor: 'rgba(74, 111, 165, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: function(context) {
                    const index = context.dataIndex;
                    const label = context.chart.data.labels[index];
                    return label === '<?php echo $currentMonth; ?>' ? '#ff7e5f' : '#4a6fa5';
                },
                pointBorderColor: function(context) {
                    const index = context.dataIndex;
                    const label = context.chart.data.labels[index];
                    return label === '<?php echo $currentMonth; ?>' ? '#ff7e5f' : '#4a6fa5';
                },
                pointRadius: function(context) {
                    const index = context.dataIndex;
                    const label = context.chart.data.labels[index];
                    return label === '<?php echo $currentMonth; ?>' ? 6 : 3;
                }
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: ₱' + context.parsed.y.toLocaleString();
                        }
                    }
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

    // Payment Methods Chart - Labels on right side with better styling
const paymentCtx = document.getElementById('paymentChart').getContext('2d');
const paymentChart = new Chart(paymentCtx, {
    type: 'doughnut',
    data: {
        labels: ['Online Bank', 'E-Wallet', 'Credit Card'],
        datasets: [{
            data: [70, 25, 5],
            backgroundColor: [
                '#f3c258ff', '#71a5f4ff', '#37e27eff'
            ],
            borderWidth: 3,
            borderColor: '#ffffff',
            hoverBorderWidth: 4,
            hoverBorderColor: '#f8f9fa'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 20,
                right: 120, // More space for right-side labels
                top: 20,
                bottom: 20
            }
        },
        plugins: {
            legend: {
                position: 'right',
                align: 'center',
                labels: {
                    boxWidth: 12,
                    padding: 15,
                    font: {
                        size: 13,
                        family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
                        weight: '600'
                    },
                    color: '#2c3e50'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(44, 62, 80, 0.9)',
                titleFont: {
                    size: 13,
                    weight: '600'
                },
                bodyFont: {
                    size: 12
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        const label = context.label;
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${percentage}%`;
                    }
                }
            }
        },
        cutout: '60%',
        animation: {
            animateScale: true,
            animateRotate: true
        }
    }
});
</script>
</body>
</html>