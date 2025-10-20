<?php
// components/top_clients.php
session_start();

// Database connection - adjust path based on where you include this
$db_path = '../config/db.php'; // Adjust this path based on your structure
if (file_exists($db_path)) {
    include $db_path;
    
    if (!class_exists('PerformanceAnalytics')) {
        include '../config/analytics.php';
    }
    
    $analytics = new PerformanceAnalytics($conn);
    $topClients = $analytics->getTopThreeClients();
} else {
    $topClients = []; // Fallback if database not available
}

if (!empty($topClients)): 
?>
<!-- Top Clients Section -->
<section class="top-clients-section">
    <div class="container">
        <div class="section-header">
            <h2>Our Valued Clients</h2>
            <p>Trusted by businesses and individuals who choose excellence</p>
        </div>
        
        <div class="clients-grid">
            <?php foreach($topClients as $index => $client): 
                $totalSpent = $client['total_spent'] ?? 0;
                $initial = strtoupper(substr($client['client_name'], 0, 1));
            ?>
            <div class="client-card">
                <div class="client-rank">#<?php echo $index + 1; ?></div>
                <div class="client-avatar">
                    <?php if (!empty($client['profile_picture'])): ?>
                        <img src="<?php echo htmlspecialchars($client['profile_picture']); ?>" alt="<?php echo htmlspecialchars($client['client_name']); ?>" class="avatar-img">
                    <?php else: ?>
                        <div class="avatar-placeholder"><?php echo $initial; ?></div>
                    <?php endif; ?>
                </div>
                <div class="client-info">
                    <h4 class="client-name"><?php echo htmlspecialchars($client['client_name']); ?></h4>
                    <p class="client-stats">
                        <span class="stat">₱<?php echo number_format($totalSpent, 0); ?>+ spent</span>
                        <span class="stat"><?php echo $client['total_bookings']; ?> events</span>
                    </p>
                    <?php if (!empty($client['favorite_event'])): ?>
                        <p class="favorite-event">Favorite: <?php echo htmlspecialchars($client['favorite_event']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="client-badge">
                    <?php 
                    if ($totalSpent >= 50000) {
                        echo '🏆 VIP Client';
                    } elseif ($totalSpent >= 20000) {
                        echo '⭐ Premium Client';
                    } else {
                        echo '💼 Valued Client';
                    }
                    ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>