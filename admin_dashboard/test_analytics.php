<?php
// admin_dashboard/analytics.php - SIMPLIFIED VERSION FOR TESTING
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

// Test each function with error handling
echo "<h1>Testing Analytics System</h1>";

try {
    echo "<h2>Testing Quick Stats</h2>";
    $quickStats = $analytics->getQuickStats();
    echo "<pre>" . print_r($quickStats, true) . "</pre>";
    
    echo "<h2>Testing Client Analytics</h2>";
    $clientData = $analytics->getClientBookingAnalytics();
    echo "<pre>" . print_r($clientData, true) . "</pre>";
    
    echo "<h2>Testing Payment Analytics</h2>";
    $paymentData = $analytics->getPaymentAnalytics();
    echo "<pre>" . print_r($paymentData, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<h2>Error: " . $e->getMessage() . "</h2>";
    echo "<p>Let's check if tables exist...</p>";
    
    // Check if required tables exist
    $tables = ['client_ratings_tbl', 'client_usage_analytics_tbl', 'analytics_cache_tbl'];
    foreach ($tables as $table) {
        $check = $conn->query("SHOW TABLES LIKE '$table'");
        if ($check->num_rows > 0) {
            echo "<p>✅ Table '$table' exists</p>";
        } else {
            echo "<p>❌ Table '$table' is missing</p>";
        }
    }
}
?>