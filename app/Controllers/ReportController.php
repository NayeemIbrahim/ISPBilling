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
     * Calculates balance dynamically based on connection date and monthly rent.
     * 
     * @return void
     */
    public function dueList()
    {
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';

        // Filters
        $district = $_GET['district'] ?? '';
        $thana = $_GET['thana'] ?? '';
        $area = $_GET['area'] ?? '';
        $connectedBy = $_GET['connected_by'] ?? '';

        // Fetch all non-inactive customers to calculate their balances
        $sql = "SELECT c.*, p.name as package_name_ref, ip.prefix_code 
                FROM customers c 
                LEFT JOIN packages p ON c.package_id = p.id 
                LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id
                WHERE c.status NOT IN ('inactive', 'disabled')";

        $params = [];
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
        if ($connectedBy) {
            $sql .= " AND c.connected_by = ?";
            $params[] = $connectedBy;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $allCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch all collections to account for payments in balance calculation
        $sqlCollections = "SELECT customer_id, SUM(amount) as total_paid FROM collections GROUP BY customer_id";
        $paidMap = $this->db->query($sqlCollections)->fetchAll(PDO::FETCH_KEY_PAIR);

        $dueCustomers = [];
        $totalOutstanding = 0;
        $today = new \DateTime();
        $filterStart = $startDate ? new \DateTime($startDate) : null;
        $filterEnd = $endDate ? new \DateTime($endDate) : null;

        foreach ($allCustomers as $c) {
            $rent = (float) ($c['monthly_rent'] ?? 0);
            $add = (float) ($c['additional_charge'] ?? 0);
            $disc = (float) ($c['discount'] ?? 0);
            $monthlyBill = $rent + $add - $disc;

            // Start billing from connection date
            $connDateStr = $c['connection_date'] ?: $c['created_at'];
            if (!$connDateStr)
                continue;

            $connDate = new \DateTime($connDateStr);
            $connDate->modify('first day of this month');

            // Calculate total billed up to current month
            $monthsCount = 0;
            $iter = clone $connDate;
            while ($iter <= $today) {
                $monthsCount++;
                $iter->modify('+1 month');
            }

            $totalBilled = $monthsCount * $monthlyBill;
            $totalPaid = (float) ($paidMap[$c['id']] ?? 0);
            $currentBalance = $totalBilled - $totalPaid;

            // Filter: Only those with balance > 0
            if ($currentBalance > 0) {
                // Apply date filter if set (Filter by Expire Date)
                if ($startDate || $endDate) {
                    $expireDate = $c['expire_date'] ? new \DateTime($c['expire_date']) : null;
                    if ($expireDate) {
                        if ($filterStart && $expireDate < $filterStart)
                            continue;
                        if ($filterEnd && $expireDate > $filterEnd)
                            continue;
                    } else if ($startDate || $endDate) {
                        continue; // If dates are set but customer has no expire date, skip? (Safe default)
                    }
                }

                $c['due_amount'] = $currentBalance; // Overwrite with calculated value
                $dueCustomers[] = $c;
                $totalOutstanding += $currentBalance;
            }
        }

        // Sort: Highest due first
        usort($dueCustomers, function ($a, $b) {
            return $b['due_amount'] <=> $a['due_amount'];
        });

        // Group by Area for Graph (Top 8)
        $areaGroups = [];
        foreach ($dueCustomers as $c) {
            $a = $c['area'] ?: 'Other';
            $areaGroups[$a] = ($areaGroups[$a] ?? 0) + $c['due_amount'];
        }
        arsort($areaGroups);
        $areaLabels = array_keys(array_slice($areaGroups, 0, 8));
        $areaValues = array_values(array_slice($areaGroups, 0, 8));

        // Options for filters
        $districts = $this->db->query("SELECT DISTINCT district FROM customers WHERE district IS NOT NULL AND district != ''")->fetchAll(PDO::FETCH_COLUMN);
        $thanas = $this->db->query("SELECT DISTINCT thana FROM customers WHERE thana IS NOT NULL AND thana != ''")->fetchAll(PDO::FETCH_COLUMN);
        $areas = $this->db->query("SELECT DISTINCT area FROM customers WHERE area IS NOT NULL AND area != ''")->fetchAll(PDO::FETCH_COLUMN);
        $connectedByList = $this->db->query("SELECT DISTINCT connected_by FROM customers WHERE connected_by IS NOT NULL AND connected_by != ''")->fetchAll(PDO::FETCH_COLUMN);

        // Fetch Table Columns
        $colStmt = $this->db->prepare("SELECT columns_json FROM table_settings WHERE table_name = 'due_list'");
        $colStmt->execute();
        $colJson = $colStmt->fetchColumn();

        $tableColumns = [];
        if ($colJson) {
            $decoded = json_decode($colJson, true);
            $tableColumns = array_filter($decoded, fn($c) => !empty($c['enabled']));
        } else {
            // Defaults
            $tableColumns = [
                ['key' => 'id', 'label' => 'ID', 'enabled' => true],
                ['key' => 'customer_info', 'label' => 'Customer', 'enabled' => true],
                ['key' => 'mobile_no', 'label' => 'Mobile', 'enabled' => true],
                ['key' => 'area', 'label' => 'Area', 'enabled' => true],
                ['key' => 'monthly_rent', 'label' => 'Monthly Rent', 'enabled' => true],
                ['key' => 'due_amount', 'label' => 'Due Amount', 'enabled' => true],
            ];
        }

        $this->view('reports/due_list', [
            'title' => 'Due List Report',
            'path' => '/reports/due-list',
            'customers' => $dueCustomers,
            'totalDue' => $totalOutstanding,
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
                'connected_by' => $connectedBy
            ],
            'options' => [
                'districts' => $districts,
                'thanas' => $thanas,
                'areas' => $areas,
                'connectedByList' => $connectedByList
            ],
            'tableColumns' => $tableColumns
        ]);
    }

    /**
     * Sync all customer balances.
     * Recalculates due_amount for all customers based on rent history and payments,
     * then updates the 'due_amount' column in the database.
     * 
     * @return void Sends JSON response.
     */
    public function syncBalances()
    {
        try {
            // 1. Fetch all customers
            $customers = $this->db->query("SELECT id, status, connection_date, created_at, monthly_rent, additional_charge, discount FROM customers")->fetchAll(PDO::FETCH_ASSOC);

            // 2. Fetch all collections grouped by customer
            $sqlCollections = "SELECT customer_id, SUM(amount) as total_paid FROM collections GROUP BY customer_id";
            $results = $this->db->query($sqlCollections)->fetchAll(PDO::FETCH_ASSOC);
            $paidMap = [];
            foreach ($results as $r) {
                $paidMap[$r['customer_id']] = (float) $r['total_paid'];
            }

            $today = new \DateTime();
            $today->modify('last day of this month');
            $updatedCount = 0;

            $this->db->beginTransaction();

            // 3. Requested Cleanup: Set connection date to 2024-11-02 and expire date to 2026-01-05 for all invalid/placeholder dates
            $cleanupSql = "UPDATE customers 
                          SET connection_date = '2024-11-02', expire_date = '2026-01-05' 
                          WHERE connection_date = '0001-11-30' 
                             OR connection_date = '30/11/0001' 
                             OR connection_date = '0000-00-00' 
                             OR connection_date IS NULL 
                             OR connection_date = ''";
            $this->db->exec($cleanupSql);

            // Re-fetch customers after cleanup to ensure calculations are based on new dates
            $customers = $this->db->query("SELECT id, status, connection_date, created_at, monthly_rent, additional_charge, discount FROM customers")->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("UPDATE customers SET due_amount = ? WHERE id = ?");
            $stmtWithStatus = $this->db->prepare("UPDATE customers SET due_amount = ?, status = 'temp_disable' WHERE id = ?");

            foreach ($customers as $c) {
                $rent = (float) ($c['monthly_rent'] ?? 0);
                $add = (float) ($c['additional_charge'] ?? 0);
                $disc = (float) ($c['discount'] ?? 0);
                $monthlyBill = $rent + $add - $disc;

                $connDateStr = $c['connection_date'] ?: $c['created_at'];
                if (!$connDateStr)
                    continue;

                $connDate = new \DateTime($connDateStr);
                $connDate->modify('first day of this month');

                $monthsCount = 0;
                $iter = clone $connDate;
                while ($iter <= $today) {
                    $monthsCount++;
                    $iter->modify('+1 month');
                }

                $totalBilled = $monthsCount * $monthlyBill;
                $totalPaid = $paidMap[$c['id']] ?? 0;
                $finalBalance = $totalBilled - $totalPaid;

                // Auto-disable if active and has due
                if ($finalBalance > 0 && $c['status'] === 'active') {
                    $stmtWithStatus->execute([$finalBalance, $c['id']]);
                } else {
                    $stmt->execute([$finalBalance, $c['id']]);
                }
                $updatedCount++;
            }

            $this->db->commit();
            return $this->json(['status' => 'success', 'message' => "Successfully synced $updatedCount customer balances."]);

        } catch (\Exception $e) {
            if ($this->db->inTransaction())
                $this->db->rollBack();
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
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

        // Fetch Table Columns
        $colStmt = $this->db->prepare("SELECT columns_json FROM table_settings WHERE table_name = 'inactive_list'");
        $colStmt->execute();
        $colJson = $colStmt->fetchColumn();

        $tableColumns = [];
        if ($colJson) {
            $decoded = json_decode($colJson, true);
            $tableColumns = array_filter($decoded, fn($c) => !empty($c['enabled']));
        } else {
            // Defaults
            $tableColumns = [
                ['key' => 'id', 'label' => 'ID', 'enabled' => true],
                ['key' => 'customer_info', 'label' => 'Customer', 'enabled' => true],
                ['key' => 'mobile_no', 'label' => 'Mobile', 'enabled' => true],
                ['key' => 'status', 'label' => 'Status', 'enabled' => true],
                ['key' => 'expire_date', 'label' => 'Expiry Date', 'enabled' => true],
                ['key' => 'manual_auto_disable', 'label' => 'Manual / Auto', 'enabled' => true],
            ];
        }

        $this->view('reports/inactive_list', [
            'title' => 'Inactive / Expired List',
            'path' => '/reports/inactive-list',
            'customers' => $customers,
            'tableColumns' => $tableColumns
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
        $q = $_GET['q'] ?? '';

        // Base SQL
        $sql = "SELECT col.id as transaction_id, col.amount, col.payment_method, col.collection_date, col.invoice_no, 
                       col.next_expire_date, col.note,
                       cust.id as customer_id, cust.full_name, cust.mobile_no, cust.status,cust.expire_date as current_expire_date,
                       cust.payment_id,
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
        if ($q) {
            $sql .= " AND (cust.full_name LIKE ? OR cust.mobile_no LIKE ? OR col.invoice_no LIKE ?)";
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
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

        // Fetch Table Columns
        $colStmt = $this->db->prepare("SELECT columns_json FROM table_settings WHERE table_name = 'collection_report'");
        $colStmt->execute();
        $colJson = $colStmt->fetchColumn();

        $tableColumns = [];
        if ($colJson) {
            $decoded = json_decode($colJson, true);
            $tableColumns = array_filter($decoded, fn($c) => !empty($c['enabled']));
        } else {
            // Defaults
            $tableColumns = [
                ['key' => 'collection_date', 'label' => 'Date', 'enabled' => true],
                ['key' => 'payment_id', 'label' => 'Payment ID', 'enabled' => true],
                ['key' => 'customer_id', 'label' => 'ID', 'enabled' => true],
                ['key' => 'customer_name', 'label' => 'Customer', 'enabled' => true],
                ['key' => 'collected_by', 'label' => 'Collected By', 'enabled' => true],
                ['key' => 'amount', 'label' => 'Amount', 'enabled' => true],
                ['key' => 'status', 'label' => 'Status', 'enabled' => true],
                ['key' => 'next_expire_date', 'label' => 'Expiry Date', 'enabled' => true],
            ];
        }

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
                'collected_by' => $collectedBy,
                'q' => $q
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
            ],
            'tableColumns' => $tableColumns
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

        // Fetch Table Columns
        $colStmt = $this->db->prepare("SELECT columns_json FROM table_settings WHERE table_name = 'customer_summary'");
        $colStmt->execute();
        $colJson = $colStmt->fetchColumn();

        $tableColumns = [];
        if ($colJson) {
            $decoded = json_decode($colJson, true);
            $tableColumns = array_filter($decoded, fn($c) => !empty($c['enabled']));
        } else {
            // Defaults
            $tableColumns = [
                ['key' => 'date', 'label' => 'Date', 'enabled' => true],
                ['key' => 'description', 'label' => 'Description', 'enabled' => true],
                ['key' => 'bill_amount', 'label' => 'Bill Amount', 'enabled' => true],
                ['key' => 'additional', 'label' => 'Additional', 'enabled' => true],
                ['key' => 'discount', 'label' => 'Discount', 'enabled' => true],
                ['key' => 'due', 'label' => 'Due', 'enabled' => true],
                ['key' => 'advance', 'label' => 'Advance', 'enabled' => true],
                ['key' => 'paid_amount', 'label' => 'Paid Amount', 'enabled' => true],
                ['key' => 'collected_by', 'label' => 'Collected By', 'enabled' => true],
                ['key' => 'note', 'label' => 'Note', 'enabled' => true],
            ];
        }

        $this->view('reports/customer_summary', [
            'title' => 'Customer Summary',
            'path' => '/reports/customer-summary',
            'summary' => $summary,
            'tableColumns' => $tableColumns
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
