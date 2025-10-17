<?php
// config/analytics.php - Performance Analytics Data Engine

class PerformanceAnalytics {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // 1. CLIENT BOOKING ANALYTICS - Complete client journey
    public function getClientBookingAnalytics() {
        $sql = "SELECT 
                    a.user_id,
                    CONCAT(COALESCE(a.first_name, ''), ' ', COALESCE(a.last_name, '')) as client_name,
                    a.email,
                    a.mobile_number,
                    COUNT(b.booking_id) as total_bookings,
                    COALESCE(SUM(b.total_price), 0) as total_spent,
                    COALESCE(AVG(b.total_price), 0) as avg_booking_value,
                    MIN(b.event_date) as first_booking_date,
                    MAX(b.event_date) as last_booking_date,
                    GROUP_CONCAT(DISTINCT b.event_theme) as event_types,
                    (SELECT event_theme FROM booking_tbl WHERE user_id = a.user_id GROUP BY event_theme ORDER BY COUNT(*) DESC LIMIT 1) as preferred_event,
                    (SELECT booking_status FROM booking_tbl WHERE user_id = a.user_id ORDER BY created_at DESC LIMIT 1) as latest_status,
                    cua.client_tier
                FROM accounts_tbl a
                LEFT JOIN booking_tbl b ON a.user_id = b.user_id
                LEFT JOIN client_usage_analytics_tbl cua ON a.user_id = cua.user_id
                WHERE a.role = 'client_type'
                GROUP BY a.user_id, client_name, a.email, a.mobile_number, cua.client_tier
                ORDER BY total_spent DESC";
        
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    // 2. PAYMENT ANALYTICS - Complete payment journey
    public function getPaymentAnalytics() {
        $sql = "SELECT 
                    p.payment_method,
                    p.payment_type,
                    p.payment_channel,
                    COUNT(*) as transaction_count,
                    SUM(p.amount_paid) as total_amount,
                    AVG(p.amount_paid) as avg_transaction,
                    (COUNT(CASE WHEN p.status = 'completed' THEN 1 END) / COUNT(*)) * 100 as success_rate,
                    b.booking_status,
                    COUNT(DISTINCT b.user_id) as unique_clients
                FROM payments_tbl p
                JOIN booking_tbl b ON p.booking_id = b.booking_id
                GROUP BY p.payment_method, p.payment_type, p.payment_channel, b.booking_status
                ORDER BY total_amount DESC";
        
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    // 3. REVENUE TRENDS - Monthly breakdown
    public function getRevenueTrends() {
        $sql = "SELECT 
                    DATE_FORMAT(b.created_at, '%Y-%m') as month,
                    DATE_FORMAT(b.created_at, '%M %Y') as display_month,
                    COUNT(*) as booking_count,
                    SUM(b.total_price) as monthly_revenue,
                    AVG(b.total_price) as avg_booking_value,
                    COUNT(DISTINCT b.user_id) as unique_clients,
                    (SELECT COUNT(*) FROM booking_tbl WHERE DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(b.created_at, '%Y-%m') AND booking_status = 'COMPLETED') as completed_bookings
                FROM booking_tbl b
                WHERE b.booking_status IN ('APPROVED', 'COMPLETED', 'PENDING_PAYMENT_VERIFICATION')
                GROUP BY DATE_FORMAT(b.created_at, '%Y-%m'), display_month
                ORDER BY month DESC
                LIMIT 12";
        
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    // 4. CLIENT RATINGS WITH FEEDBACK
    public function getClientRatings() {
        $sql = "SELECT 
                    cr.rating_id,
                    a.user_id,
                    CONCAT(COALESCE(a.first_name, ''), ' ', COALESCE(a.last_name, '')) as client_name,
                    cr.rating,
                    cr.feedback,
                    cr.rating_date,
                    b.event_theme,
                    b.packages,
                    b.total_price,
                    a.profile_picture
                FROM client_ratings_tbl cr
                JOIN accounts_tbl a ON cr.user_id = a.user_id
                JOIN booking_tbl b ON cr.booking_id = b.booking_id
                WHERE cr.is_approved = TRUE
                ORDER BY cr.rating_date DESC
                LIMIT 10";
        
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    // 5. EVENT TYPE PERFORMANCE ANALYSIS
    public function getEventTypePerformance() {
        $sql = "SELECT 
                    event_theme,
                    COUNT(*) as total_bookings,
                    SUM(total_price) as total_revenue,
                    AVG(total_price) as avg_revenue,
                    MIN(total_price) as min_revenue,
                    MAX(total_price) as max_revenue,
                    (COUNT(*) / (SELECT COUNT(*) FROM booking_tbl)) * 100 as market_share,
                    COUNT(DISTINCT user_id) as unique_clients,
                    (SELECT COUNT(*) FROM booking_tbl b2 WHERE b2.event_theme = b.event_theme AND b2.booking_status = 'COMPLETED') as completed_events,
                    (SELECT COUNT(*) FROM booking_tbl b2 WHERE b2.event_theme = b.event_theme AND b2.booking_status = 'REJECTED') as rejected_events
                FROM booking_tbl b
                GROUP BY event_theme
                ORDER BY total_revenue DESC";
        
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    // 6. CLIENT USAGE STATUS (For your bar chart)
    public function getClientUsageStatus() {
        $sql = "SELECT 
                    YEAR(b.event_date) as year,
                    COUNT(*) as total_bookings,
                    COUNT(DISTINCT b.user_id) as unique_clients,
                    SUM(b.total_price) as yearly_revenue,
                    AVG(b.total_price) as avg_booking_value,
                    (SELECT COUNT(*) FROM client_usage_analytics_tbl WHERE client_tier = 'VIP' AND YEAR(last_booking_date) = YEAR(b.event_date)) as vip_clients,
                    (SELECT COUNT(*) FROM client_usage_analytics_tbl WHERE client_tier = 'Regular' AND YEAR(last_booking_date) = YEAR(b.event_date)) as regular_clients,
                    (SELECT COUNT(*) FROM client_usage_analytics_tbl WHERE client_tier = 'New' AND YEAR(last_booking_date) = YEAR(b.event_date)) as new_clients
                FROM booking_tbl b
                WHERE b.event_date IS NOT NULL
                GROUP BY YEAR(b.event_date)
                ORDER BY year DESC
                LIMIT 4";
        
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    // 7. QUICK STATS SUMMARY (For dashboard cards)
    public function getQuickStats() {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM accounts_tbl WHERE role = 'client_type') as total_clients,
                    (SELECT COUNT(*) FROM booking_tbl) as total_bookings,
                    (SELECT COALESCE(SUM(total_price), 0) FROM booking_tbl WHERE booking_status IN ('APPROVED', 'COMPLETED')) as total_revenue,
                    (SELECT COALESCE(AVG(rating), 0) FROM client_ratings_tbl WHERE is_approved = TRUE) as avg_rating,
                    (SELECT COUNT(*) FROM booking_tbl WHERE booking_status = 'PENDING_PAYMENT_VERIFICATION') as pending_payments,
                    (SELECT COUNT(*) FROM booking_tbl WHERE DATE(event_date) = CURDATE()) as today_events";
        
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_assoc() : [];
    }
}

// API Endpoint for AJAX calls
if (isset($_GET['action']) && isset($_GET['type']) && $_GET['type'] == 'api') {
    include 'db.php';
    $analytics = new PerformanceAnalytics($conn);
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'client_analytics':
            echo json_encode($analytics->getClientBookingAnalytics());
            break;
        case 'payment_analytics':
            echo json_encode($analytics->getPaymentAnalytics());
            break;
        case 'revenue_trends':
            echo json_encode($analytics->getRevenueTrends());
            break;
        case 'client_ratings':
            echo json_encode($analytics->getClientRatings());
            break;
        case 'event_performance':
            echo json_encode($analytics->getEventTypePerformance());
            break;
        case 'usage_status':
            echo json_encode($analytics->getClientUsageStatus());
            break;
        case 'quick_stats':
            echo json_encode($analytics->getQuickStats());
            break;
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit();
}
?>