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
        $stmt = $db->query("SELECT * FROM customers WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5");
        $pendingCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Stats
        $stmtStats = $db->query("SELECT 
            (SELECT COUNT(*) FROM customers WHERE status = 'active') as active_count,
            (SELECT SUM(total_amount) FROM customers WHERE status = 'active') as total_revenue,
            (SELECT SUM(due_amount) FROM customers) as total_due
        ");
        $stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

        // Fetch Status Counts for Chart
        $statusStmt = $db->query("SELECT status, COUNT(*) as count FROM customers GROUP BY status");
        $statusData = $statusStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Fetch Area Counts for Chart
        $areaStmt = $db->query("SELECT area, COUNT(*) as count FROM customers WHERE area IS NOT NULL AND area != '' GROUP BY area ORDER BY count DESC LIMIT 10");
        $areaData = $areaStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $this->view('dashboard', [
            'title' => 'Dashboard',
            'path' => '/dashboard',
            'pendingCustomers' => $pendingCustomers,
            'stats' => $stats,
            'statusData' => $statusData,
            'areaData' => $areaData
        ]);
    }
}
