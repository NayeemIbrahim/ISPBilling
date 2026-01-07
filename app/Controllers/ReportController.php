<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

/**
 * ReportController
 * 
 * Generates various reports including due lists, inactive customer reports,
 * and collection summaries.
 */
class ReportController extends Controller
{
    /**
     * ReportController constructor.
     */
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Generate and display the Due List report.
     * 
     * @return void
     */
    public function dueList()
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t'); // t = last day of month

        // Filters
        $district = $_GET['district'] ?? '';
        $thana = $_GET['thana'] ?? '';
        $area = $_GET['area'] ?? '';
        $building = $_GET['building_name'] ?? '';
        $floor = $_GET['floor'] ?? '';
        $house = $_GET['house_no'] ?? '';
        $connectedBy = $_GET['connected_by'] ?? '';

        // Base SQL: Use explicit date check and ensure due_amount > 0
        $sql = "SELECT c.*, p.name as package_name_ref, ip.prefix_code 
                FROM customers c 
                LEFT JOIN packages p ON c.package_id = p.id 
                LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id
                WHERE c.due_amount > 0 
                AND c.expire_date BETWEEN ? AND ?
                AND c.status != 'inactive' AND c.status != 'disabled'";

        $params = [$startDate, $endDate];

        if ($district) {
            $sql .= " AND c.district = ?";
            $params[] = $district;
        }
        if ($thana) {
            $sql .= " AND c.thana = ?";
            $params[] = $thana;
        }
        if ($area) {
            $sql .= " AND c.area = ?";
            $params[] = $area;
        }
        if ($building) {
            $sql .= " AND c.building_name = ?";
            $params[] = $building;
        }
        if ($floor) {
            $sql .= " AND c.floor = ?";
            $params[] = $floor;
        }
        if ($house) {
            $sql .= " AND c.house_no = ?";
            $params[] = $house;
        }
        if ($connectedBy) {
            $sql .= " AND c.connected_by = ?";
            $params[] = $connectedBy;
        }

        $sql .= " ORDER BY c.expire_date ASC, c.due_amount DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Stats & Graph Data
        $totalDue = array_sum(array_column($customers, 'due_amount'));

        // Group by Area for Graph
        $areaLabels = [];
        $areaValues = [];
        $areaGroups = [];
        foreach ($customers as $c) {
            $a = $c['area'] ?: 'Other';
            $areaGroups[$a] = ($areaGroups[$a] ?? 0) + $c['due_amount'];
        }
        arsort($areaGroups); // Sort highest due first
        $areaLabels = array_keys(array_slice($areaGroups, 0, 8)); // Top 8 areas
        $areaValues = array_values(array_slice($areaGroups, 0, 8));

        // Fetch Dropdown Options for Filters
        $districts = $this->db->query("SELECT DISTINCT district FROM customers WHERE district IS NOT NULL AND district != ''")->fetchAll(PDO::FETCH_COLUMN);
        $thanas = $this->db->query("SELECT DISTINCT thana FROM customers WHERE thana IS NOT NULL AND thana != ''")->fetchAll(PDO::FETCH_COLUMN);
        $areas = $this->db->query("SELECT DISTINCT area FROM customers WHERE area IS NOT NULL AND area != ''")->fetchAll(PDO::FETCH_COLUMN);
        $buildings = $this->db->query("SELECT DISTINCT building_name FROM customers WHERE building_name IS NOT NULL AND building_name != ''")->fetchAll(PDO::FETCH_COLUMN);
        $floors = $this->db->query("SELECT DISTINCT floor FROM customers WHERE floor IS NOT NULL AND floor != ''")->fetchAll(PDO::FETCH_COLUMN);
        $houses = $this->db->query("SELECT DISTINCT house_no FROM customers WHERE house_no IS NOT NULL AND house_no != ''")->fetchAll(PDO::FETCH_COLUMN);
        $connectedByList = $this->db->query("SELECT DISTINCT connected_by FROM customers WHERE connected_by IS NOT NULL AND connected_by != ''")->fetchAll(PDO::FETCH_COLUMN);

        $this->view('reports/due_list', [
            'title' => 'Due List Report',
            'path' => '/reports/due-list',
            'customers' => $customers,
            'totalDue' => $totalDue,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'chartData' => [
                'labels' => $areaLabels,
                'values' => $areaValues
            ],
            'filters' => [
                'district' => $district,
                'thana' => $thana,
                'area' => $area,
                'building_name' => $building,
                'floor' => $floor,
                'house_no' => $house,
                'connected_by' => $connectedBy
            ],
            'options' => [
                'districts' => $districts,
                'thanas' => $thanas,
                'areas' => $areas,
                'buildings' => $buildings,
                'floors' => $floors,
                'houses' => $houses,
                'connectedByList' => $connectedByList
            ]
        ]);
    }

    /**
     * Generate and display the Inactive List report.
     * 
     * @return void
     */
    public function inactiveList()
    {
        $sql = "SELECT c.*, p.name as package_name_ref, ip.prefix_code 
                FROM customers c 
                LEFT JOIN packages p ON c.package_id = p.id 
                LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id
                WHERE c.status IN ('inactive', 'disabled') OR c.expire_date < CURDATE()
                ORDER BY c.status DESC, c.expire_date ASC";
        $stmt = $this->db->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('reports/inactive_list', [
            'title' => 'Inactive / Expired List',
            'path' => '/reports/inactive-list',
            'customers' => $customers
        ]);
    }

    /**
     * Generate and display the Collection Report.
     * 
     * @return void
     */
    public function collectionReport()
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        // Filters
        $district = $_GET['district'] ?? '';
        $thana = $_GET['thana'] ?? '';
        $area = $_GET['area'] ?? '';
        $building = $_GET['building_name'] ?? '';
        $floor = $_GET['floor'] ?? '';
        $house = $_GET['house_no'] ?? '';
        $connectedBy = $_GET['connected_by'] ?? '';
        $collectedBy = $_GET['collected_by'] ?? '';

        // Base SQL
        $sql = "SELECT col.id as payment_id, col.amount, col.payment_method, col.collection_date, col.invoice_no, 
                       col.next_expire_date, col.note,
                       cust.id as customer_id, cust.full_name, cust.mobile_no, cust.status,cust.expire_date as current_expire_date,
                       emp.name as collected_by_name,
                       cust.connected_by,
                       ip.prefix_code
                FROM collections col 
                JOIN customers cust ON col.customer_id = cust.id 
                LEFT JOIN employees emp ON col.collected_by = emp.id
                LEFT JOIN id_prefixes ip ON cust.prefix_id = ip.id
                WHERE DATE(col.collection_date) BETWEEN ? AND ?";

        $params = [$startDate, $endDate];

        if ($district) {
            $sql .= " AND cust.district = ?";
            $params[] = $district;
        }
        if ($thana) {
            $sql .= " AND cust.thana = ?";
            $params[] = $thana;
        }
        if ($area) {
            $sql .= " AND cust.area = ?";
            $params[] = $area;
        }
        if ($building) {
            $sql .= " AND cust.building_name = ?";
            $params[] = $building;
        }
        if ($floor) {
            $sql .= " AND cust.floor = ?";
            $params[] = $floor;
        }
        if ($house) {
            $sql .= " AND cust.house_no = ?";
            $params[] = $house;
        }
        if ($connectedBy) {
            $sql .= " AND cust.connected_by = ?";
            $params[] = $connectedBy;
        }
        if ($collectedBy) {
            $sql .= " AND col.collected_by = ?";
            $params[] = $collectedBy;
        }

        $sql .= " ORDER BY col.collection_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $collections = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sum
        $totalCollected = array_sum(array_column($collections, 'amount'));

        // Fetch Dropdown Options
        $districts = $this->db->query("SELECT DISTINCT district FROM customers WHERE district IS NOT NULL AND district != ''")->fetchAll(PDO::FETCH_COLUMN);
        $thanas = $this->db->query("SELECT DISTINCT thana FROM customers WHERE thana IS NOT NULL AND thana != ''")->fetchAll(PDO::FETCH_COLUMN);
        $areas = $this->db->query("SELECT DISTINCT area FROM customers WHERE area IS NOT NULL AND area != ''")->fetchAll(PDO::FETCH_COLUMN);
        $buildings = $this->db->query("SELECT DISTINCT building_name FROM customers WHERE building_name IS NOT NULL AND building_name != ''")->fetchAll(PDO::FETCH_COLUMN);
        $floors = $this->db->query("SELECT DISTINCT floor FROM customers WHERE floor IS NOT NULL AND floor != ''")->fetchAll(PDO::FETCH_COLUMN);
        $houses = $this->db->query("SELECT DISTINCT house_no FROM customers WHERE house_no IS NOT NULL AND house_no != ''")->fetchAll(PDO::FETCH_COLUMN);

        $connectedByList = $this->db->query("SELECT DISTINCT connected_by FROM customers WHERE connected_by IS NOT NULL AND connected_by != ''")->fetchAll(PDO::FETCH_COLUMN);
        $employees = $this->db->query("SELECT id, name FROM employees")->fetchAll(PDO::FETCH_ASSOC);

        $this->view('reports/collection_report', [
            'title' => 'Collection Report',
            'path' => '/reports/collection-report',
            'collections' => $collections,
            'totalCollected' => $totalCollected,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filters' => [
                'district' => $district,
                'thana' => $thana,
                'area' => $area,
                'building_name' => $building,
                'floor' => $floor,
                'house_no' => $house,
                'connected_by' => $connectedBy,
                'collected_by' => $collectedBy
            ],
            'options' => [
                'districts' => $districts,
                'thanas' => $thanas,
                'areas' => $areas,
                'buildings' => $buildings,
                'floors' => $floors,
                'houses' => $houses,
                'connectedByList' => $connectedByList,
                'employees' => $employees,
            ]
        ]);
    }

    /**
     * Internal process for auto-disabling expired customers.
     * 
     * @return void Sends JSON response.
     */
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

            return $this->json(['status' => 'success', 'message' => "$affected customers auto-disabled."]);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the Customer Summary view.
     * 
     * @return void
     */
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

    /**
     * AJAX fetch bill history for a specific customer.
     * 
     * @return void Sends JSON response.
     */
    public function customerHistory()
    {
        $customerId = $_GET['customer_id'] ?? 0;

        $sqlCustomer = "SELECT c.*, ip.prefix_code 
                        FROM customers c 
                        LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id 
                        WHERE c.id = ?";
        $stmt = $this->db->prepare($sqlCustomer);
        $stmt->execute([$customerId]);
        $customer = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$customer) {
            return $this->json(['success' => false, 'message' => 'Customer not found'], 404);
        }

        // Fetch collections with collector name from employees, fallback to 'System' if ID exists but not in employees
        $sqlCollections = "SELECT c.*, 
                                  CASE 
                                      WHEN e.name IS NOT NULL THEN e.name 
                                      WHEN c.collected_by IS NOT NULL THEN 'System' 
                                      ELSE NULL 
                                  END as collected_by_name 
                           FROM collections c 
                           LEFT JOIN employees e ON c.collected_by = e.id 
                           WHERE c.customer_id = ? 
                           ORDER BY c.collection_date DESC";
        $stmtCol = $this->db->prepare($sqlCollections);
        $stmtCol->execute([$customerId]);
        $collections = $stmtCol->fetchAll(\PDO::FETCH_ASSOC);

        return $this->json([
            'success' => true,
            'customer' => $customer,
            'collections' => $collections
        ]);
    }
}
