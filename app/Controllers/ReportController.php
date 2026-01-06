<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

class ReportController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function dueList()
    {
        $sql = "SELECT c.*, p.name as package_name_ref 
                FROM customers c 
                LEFT JOIN packages p ON c.package_id = p.id 
                WHERE c.due_amount > 0 
                ORDER BY c.due_amount DESC";
        $stmt = $this->db->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('reports/due_list', [
            'title' => 'Due List Report',
            'path' => '/reports/due-list',
            'customers' => $customers
        ]);
    }

    public function inactiveList()
    {
        $sql = "SELECT c.*, p.name as package_name_ref 
                FROM customers c 
                LEFT JOIN packages p ON c.package_id = p.id 
                WHERE c.status = 'inactive' OR c.status = 'disabled' OR c.expire_date < CURDATE()
                ORDER BY c.expire_date ASC";
        $stmt = $this->db->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('reports/inactive_list', [
            'title' => 'Inactive / Expired List',
            'path' => '/reports/inactive-list',
            'customers' => $customers
        ]);
    }

    public function collectionReport()
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $sql = "SELECT col.*, cust.full_name, cust.mobile_no 
                FROM collections col 
                JOIN customers cust ON col.customer_id = cust.id 
                WHERE DATE(col.collection_date) BETWEEN ? AND ? 
                ORDER BY col.collection_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $collections = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sum
        $totalCollected = array_sum(array_column($collections, 'amount'));

        $this->view('reports/collection_report', [
            'title' => 'Collection Report',
            'path' => '/reports/collection-report',
            'collections' => $collections,
            'totalCollected' => $totalCollected,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    public function processAutoDisable()
    {
        // This method can be triggered manually or via cron
        try {
            // Logic: Disable customers where auto_disable = 1 AND expire_date < TODAY
            $sql = "UPDATE customers 
                    SET status = 'disabled' 
                    WHERE auto_disable = 1 
                    AND status = 'active' 
                    AND expire_date < CURDATE()";
            $affected = $this->db->exec($sql);

            echo json_encode(['status' => 'success', 'message' => "$affected customers auto-disabled."]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function customerSummary()
    {
        // Summary of counts and amounts
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM customers) as total,
                    (SELECT COUNT(*) FROM customers WHERE status = 'active') as active,
                    (SELECT COUNT(*) FROM customers WHERE status = 'disabled') as disabled,
                    (SELECT SUM(due_amount) FROM customers) as total_due,
                    (SELECT SUM(amount) FROM collections) as total_collected";
        $stmt = $this->db->query($sql);
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->view('reports/customer_summary', [
            'title' => 'Customer Summary',
            'path' => '/reports/customer-summary',
            'summary' => $summary
        ]);
    }
}
