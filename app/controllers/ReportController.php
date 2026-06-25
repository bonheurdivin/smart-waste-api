<?php

require_once 'app/middleware/AuthMiddleware.php';
require_once 'app/middleware/RoleMiddleware.php';

class ReportController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // GET daily pickups report
    public function dailyPickups() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher', 'finance']);

        $stmt = $this->conn->prepare(
            "SELECT 
            DATE(scheduled_at) as date,
            COUNT(*) as total_pickups,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = 'missed' THEN 1 ELSE 0 END) as missed,
            SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) as scheduled
            FROM pickups
            GROUP BY DATE(scheduled_at)
            ORDER BY date DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // GET monthly revenue report
    public function monthlyRevenue() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'finance']);

        $stmt = $this->conn->prepare(
            "SELECT
            DATE_FORMAT(paid_at, '%Y-%m') as month,
            COUNT(*) as total_payments,
            SUM(amount) as total_revenue,
            SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as paid_amount,
            SUM(CASE WHEN status = 'unpaid' THEN amount ELSE 0 END) as unpaid_amount
            FROM payments
            GROUP BY DATE_FORMAT(paid_at, '%Y-%m')
            ORDER BY month DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // GET worker productivity report
    public function workerProductivity() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $stmt = $this->conn->prepare(
            "SELECT
            u.name as worker_name,
            COUNT(p.id) as total_pickups,
            SUM(CASE WHEN p.status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN p.status = 'missed' THEN 1 ELSE 0 END) as missed,
            ROUND(AVG(p.rating), 2) as avg_rating
            FROM pickups p
            LEFT JOIN workers w ON p.worker_id = w.id
            LEFT JOIN users u ON w.user_id = u.id
            GROUP BY w.id, u.name
            ORDER BY completed DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // GET high volume zones report
    public function highVolumeZones() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $stmt = $this->conn->prepare(
            "SELECT
            h.zone,
            COUNT(DISTINCT h.id) as total_households,
            COUNT(p.id) as total_pickups,
            SUM(CASE WHEN p.status = 'completed' THEN 1 ELSE 0 END) as completed_pickups,
            SUM(CASE WHEN p.status = 'missed' THEN 1 ELSE 0 END) as missed_pickups
            FROM households h
            LEFT JOIN pickups p ON h.id = p.household_id
            GROUP BY h.zone
            ORDER BY total_pickups DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // GET dashboard summary
    public function dashboardSummary() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher', 'finance']);

        // Total households
        $householdsResult = $this->conn->query(
            "SELECT COUNT(*) as total FROM households"
        );
        $households = $householdsResult->fetch_assoc();

        // Total workers
        $workersResult = $this->conn->query(
            "SELECT COUNT(*) as total FROM workers WHERE status = 'active'"
        );
        $workers = $workersResult->fetch_assoc();

        // Today's pickups
        $todayResult = $this->conn->query(
            "SELECT COUNT(*) as total FROM pickups 
            WHERE DATE(scheduled_at) = CURDATE()"
        );
        $today = $todayResult->fetch_assoc();

        // Monthly revenue
        $revenueResult = $this->conn->query(
            "SELECT SUM(amount) as total FROM payments 
            WHERE status = 'paid' 
            AND MONTH(paid_at) = MONTH(CURDATE())"
        );
        $revenue = $revenueResult->fetch_assoc();

        echo json_encode([
            'status' => 'success',
            'data' => [
                'total_households' => $households['total'],
                'active_workers' => $workers['total'],
                'pickups_today' => $today['total'],
                'revenue_this_month' => $revenue['total'] ?? 0
            ]
        ]);
    }
}