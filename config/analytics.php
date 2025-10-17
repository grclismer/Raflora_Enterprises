<?php
class PerformanceAnalytics {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getClientBookingAnalytics() {
        $sql = "
            SELECT 
                full_name as client_name,
                COUNT(booking_id) as total_bookings,
                SUM(total_price) as total_spent,
                event_theme as preferred_event,
                booking_status as latest_status
            FROM booking_tbl 
            GROUP BY full_name
            ORDER BY total_spent DESC
        ";
        
        $result = $this->conn->query($sql);
        $analytics = [];
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $analytics[] = $row;
            }
        }
        
        return $analytics;
    }

    public function getPaymentAnalytics() {
        $sql = "
            SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(total_price) as total_amount
            FROM booking_tbl 
            WHERE booking_status IN ('APPROVED', 'COMPLETED')
            GROUP BY payment_method
        ";
        
        $result = $this->conn->query($sql);
        $payments = [];
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $payments[] = $row;
            }
        }
        
        return $payments;
    }

    public function getRevenueTrends() {
        // Return sample data or implement your revenue trends logic
        return [
            'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'revenue' => [120000, 150000, 180000, 140000, 200000, 220000, 250000, 230000, 210000, 240000, 260000, 280000]
        ];
    }

    public function getClientRatings() {
        // Use the client_feedback table for ratings
        return $this->getClientFeedback();
    }

    public function getQuickStats() {
        $sql = "
            SELECT 
                COUNT(*) as total_bookings,
                COUNT(DISTINCT full_name) as total_clients,
                SUM(CASE WHEN booking_status IN ('APPROVED', 'COMPLETED') THEN total_price ELSE 0 END) as total_revenue,
                SUM(CASE WHEN booking_status = 'PENDING_PAYMENT_VERIFICATION' THEN 1 ELSE 0 END) as pending_payments,
                SUM(CASE WHEN DATE(event_date) = CURDATE() THEN 1 ELSE 0 END) as today_events
            FROM booking_tbl
        ";
        
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $stats = $result->fetch_assoc();
            
            // Calculate average rating from feedback
            $rating_sql = "SELECT AVG(overall_rating) as avg_rating FROM client_feedback";
            $rating_result = $this->conn->query($rating_sql);
            if ($rating_result && $rating_result->num_rows > 0) {
                $rating_data = $rating_result->fetch_assoc();
                $stats['avg_rating'] = $rating_data['avg_rating'] ?? 0;
            } else {
                $stats['avg_rating'] = 0;
            }
            
            return $stats;
        }
        
        return [
            'total_bookings' => 0,
            'total_clients' => 0,
            'total_revenue' => 0,
            'pending_payments' => 0,
            'today_events' => 0,
            'avg_rating' => 0
        ];
    }

    public function getClientFeedback() {
    try {
        // Check if client_feedback table exists
        $table_check = $this->conn->query("SHOW TABLES LIKE 'client_feedback'");
        if ($table_check->num_rows == 0) {
            return [];
        }

        $sql = "
            SELECT 
                cf.order_id,
                cf.client_name,
                cf.event_theme,
                cf.overall_rating as rating,
                CASE 
                    WHEN cf.feedback_text IS NULL THEN 'No detailed feedback provided'
                    WHEN TRIM(cf.feedback_text) = '' THEN 'No detailed feedback provided'
                    WHEN cf.feedback_text = 'No detailed feedback provided' THEN 'No detailed feedback provided'
                    ELSE cf.feedback_text 
                END as feedback,
                cf.feedback_date as rating_date,
                cf.is_anonymous,
                cf.category_floral,
                cf.category_setup,
                cf.category_service,
                cf.would_recommend
            FROM client_feedback cf
            ORDER BY cf.feedback_date DESC
            LIMIT 10
        ";
        
        $result = $this->conn->query($sql);
        $feedback = [];
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $feedback[] = $row;
            }
        }
        
        return $feedback;
    } catch (Exception $e) {
        error_log("Exception in getClientFeedback: " . $e->getMessage());
        return [];
    }
}

public function getMonthlyRevenue() {
    $sql = "
        SELECT 
            DATE_FORMAT(event_date, '%b') as month,
            MONTH(event_date) as month_num,
            SUM(total_price) as revenue
        FROM booking_tbl 
        WHERE booking_status IN ('APPROVED', 'COMPLETED')
        GROUP BY YEAR(event_date), MONTH(event_date), DATE_FORMAT(event_date, '%b')
        ORDER BY YEAR(event_date), MONTH(event_date)
        LIMIT 12
    ";
    
    $result = $this->conn->query($sql);
    $monthlyRevenue = [];
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $monthlyRevenue[] = $row;
        }
    }
    
    return $monthlyRevenue;
}
}
?>