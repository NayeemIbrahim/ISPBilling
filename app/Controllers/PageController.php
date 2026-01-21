<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

/**
 * PageController
 * 
 * Handles static pages and the main dashboard view.
 */
class PageController extends Controller
{
    /**
     * Display the dashboard with summary stats and pending customers.
     * 
     * @return void
     */
    public function index()
    {
        // Fetch Only Pending Customers for the dashboard table
        $db = (new Database())->getConnection();

        if (!$db) {
            die("Database connection failed. Please ensure MySQL is running in XAMPP and the database 'hk_isp_billing' exists.");
        }

        $stmt = $db->query("SELECT * FROM customers WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5");
        $pendingCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Stats
        $stmtStats = $db->query("SELECT 
            (SELECT COUNT(*) FROM customers WHERE status = 'active') as active_count,
            (SELECT COALESCE(SUM(amount), 0) FROM collections WHERE MONTH(collection_date) = MONTH(CURDATE()) AND YEAR(collection_date) = YEAR(CURDATE())) as total_revenue,
            (SELECT SUM(due_amount) FROM customers) as total_due
        ");
        $stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

        // Fetch Status Counts for Chart
        $statusStmt = $db->query("SELECT status, COUNT(*) as count FROM customers GROUP BY status");
        $statusData = $statusStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Fetch Area Counts for Chart
        $areaStmt = $db->query("SELECT area, COUNT(*) as count FROM customers WHERE area IS NOT NULL AND area != '' GROUP BY area ORDER BY count DESC LIMIT 10");
        $areaData = $areaStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Fetch Ticket Status for Chart (Complain Status)
        $ticketStmt = $db->query("SELECT status, COUNT(*) as count FROM customer_complains GROUP BY status");
        $ticketData = $ticketStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // --- Data for Collection Chart Filters ---

        // 1. Last 30 Days (Daily)
        $collDaily = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $sql = "SELECT COALESCE(SUM(amount), 0) FROM collections WHERE DATE(collection_date) = '$date'";
            $collDaily[$date] = $db->query($sql)->fetchColumn();
        }

        // 2. Monthly (Last 12 Months)
        $collMonthly = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m-01', strtotime("-$i months"));
            $monthKey = date('M Y', strtotime($date));
            $sql = "SELECT COALESCE(SUM(amount), 0) FROM collections WHERE MONTH(collection_date) = MONTH('$date') AND YEAR(collection_date) = YEAR('$date')";
            $collMonthly[$monthKey] = $db->query($sql)->fetchColumn();
        }

        // Fetch Revenue Trend (Last 24 Months for filtering)
        $revenueTrend = [];
        // We'll fetch 24 months back so the UI can filter
        for ($i = 23; $i >= 0; $i--) {
            $date = date('Y-m-01', strtotime("-$i months"));
            $monthName = date('M Y', strtotime($date));
            $sql = "SELECT COALESCE(SUM(amount), 0) FROM collections WHERE MONTH(collection_date) = MONTH('$date') AND YEAR(collection_date) = YEAR('$date')";
            $revenueTrend[$monthName] = $db->query($sql)->fetchColumn();
        }

        $this->view('dashboard', [
            'title' => 'Dashboard',
            'path' => '/dashboard',
            'pendingCustomers' => $pendingCustomers,
            'stats' => $stats,
            'statusData' => $statusData,
            'areaData' => $areaData,
            'ticketData' => $ticketData,
            'collDaily' => $collDaily,
            'collMonthly' => $collMonthly,
            'revenueTrend' => $revenueTrend
        ]);
    }
}
