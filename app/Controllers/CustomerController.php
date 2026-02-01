<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

/**
 * CustomerController
 * 
 * Manages customer lifecycle including registration, profile updates, 
 * activation, and search/filtering.
 */
class CustomerController extends Controller
{
    /**
     * Show recent customers (default: current month) with date filtering and custom columns.
     * 
     * @return void
     */
    public function recent()
    {
        // 1. Date Filter Logic
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        // 2. Pagination
        $limit = 30;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1)
            $page = 1;
        $offset = ($page - 1) * $limit;

        // 3. Sorting
        $allowedSortColumns = ['id', 'full_name', 'mobile_no', 'area', 'package_name', 'created_at', 'status'];
        $sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSortColumns) ? $_GET['sort'] : 'id';
        $order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';

        // 4. Query Construction
        $where = ["DATE(c.created_at) BETWEEN ? AND ?"];
        $params = [$startDate, $endDate];

        $q = $_GET['q'] ?? '';
        if ($q) {
            $where[] = "(LOWER(c.full_name) LIKE ? OR c.mobile_no LIKE ? OR LOWER(c.area) LIKE ? OR LOWER(c.payment_id) LIKE ?)";
            $term = "%" . strtolower($q) . "%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        $whereSql = "WHERE " . implode(" AND ", $where);

        // 5. Get Total Count
        $joins = "LEFT JOIN packages p ON c.package_id = p.id LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id";
        $countSql = "SELECT COUNT(*) FROM customers c $joins $whereSql";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $totalRecords = $countStmt->fetchColumn();
        $totalPages = ceil($totalRecords / $limit);

        // 6. Fetch Records
        $sql = "SELECT c.*, p.name as package_name, ip.prefix_code 
                FROM customers c 
                $joins
                $whereSql
                ORDER BY c.$sort $order LIMIT $limit OFFSET $offset";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 7. Table Columns (Recent Customers)
        $colStmt = $this->db->prepare("SELECT columns_json FROM table_settings WHERE table_name = 'recent_customers'");
        $colStmt->execute();
        $colJson = $colStmt->fetchColumn();

        $tableColumns = [];
        if ($colJson) {
            $decoded = json_decode($colJson, true);
            $tableColumns = array_filter($decoded, fn($c) => !empty($c['enabled']));
        } else {
            // Default Fallback
            $tableColumns = [
                ['key' => 'id', 'label' => 'ID', 'enabled' => true],
                ['key' => 'full_name', 'label' => 'Name', 'enabled' => true],
                ['key' => 'mobile_no', 'label' => 'Mobile', 'enabled' => true],
                ['key' => 'area', 'label' => 'Area', 'enabled' => true],
                ['key' => 'package_name', 'label' => 'Package', 'enabled' => true],
                ['key' => 'created_at', 'label' => 'Date Added', 'enabled' => true],
                ['key' => 'status', 'label' => 'Status', 'enabled' => true]
            ];
        }

        $this->view('customers/recent', [
            'title' => 'Recent Customers',
            'path' => '/customer/recent',
            'customers' => $customers,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'sort' => $sort,
            'order' => $order,
            'totalRecords' => $totalRecords,
            'q' => $q,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tableColumns' => $tableColumns
        ]);
    }
    /**
     * CustomerController constructor.
     */
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    /**
     * List all customers with pagination, sorting, and filtering.
     * 
     * @return void
     */
    public function index()
    {
        // 1. Pagination Settings
        $limit = 30;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1)
            $page = 1;
        $offset = ($page - 1) * $limit;

        // 2. Sorting Settings
        $allowedSortColumns = ['id', 'full_name', 'mobile_no', 'area', 'package_name', 'due_amount', 'status'];
        $sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSortColumns) ? $_GET['sort'] : 'id';
        $order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';

        // 3. Filtering & Search Logic
        $where = [];
        $params = [];

        $q = $_GET['q'] ?? '';
        if ($q) {
            $where[] = "(LOWER(c.full_name) LIKE ? OR c.mobile_no LIKE ? OR LOWER(c.area) LIKE ? OR LOWER(c.payment_id) LIKE ? OR CONCAT(LOWER(COALESCE(ip.prefix_code,'')), c.id) LIKE ?)";
            $term = "%" . strtolower($q) . "%";
            $params = array_merge($params, [$term, $term, $term, $term, $term]);
        }

        $statuses = $_GET['status'] ?? [];
        if (!is_array($statuses))
            $statuses = [$statuses];
        $statuses = array_filter($statuses);

        if (!empty($statuses)) {
            $placeholders = implode(',', array_fill(0, count($statuses), '?'));
            $where[] = "c.status IN ($placeholders)";
            $params = array_merge($params, array_values($statuses));
        }

        $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        // 4. Get Total Count
        $joins = "LEFT JOIN packages p ON c.package_id = p.id LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id";
        $countSql = "SELECT COUNT(*) FROM customers c $joins $whereSql";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $totalRecords = $countStmt->fetchColumn();
        $totalPages = ceil($totalRecords / $limit);

        // 5. Fetch Records
        $sql = "SELECT c.*, p.name as package_name, ip.prefix_code 
                FROM customers c 
                $joins
                $whereSql
                ORDER BY c.$sort $order LIMIT $limit OFFSET $offset";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 6. Fetch Table Columns
        $colStmt = $this->db->prepare("SELECT columns_json FROM table_settings WHERE table_name = 'all_customers'");
        $colStmt->execute();
        $colJson = $colStmt->fetchColumn();

        $tableColumns = [];
        if ($colJson) {
            $decoded = json_decode($colJson, true);
            // Filter enabled only
            $tableColumns = array_filter($decoded, fn($c) => !empty($c['enabled']));
        } else {
            // Defaults
            $tableColumns = [
                ['key' => 'id', 'label' => 'ID', 'enabled' => true],
                ['key' => 'full_name', 'label' => 'Name', 'enabled' => true],
                ['key' => 'mobile_no', 'label' => 'Mobile', 'enabled' => true],
                ['key' => 'area', 'label' => 'Area', 'enabled' => true],
                ['key' => 'package_name', 'label' => 'Package', 'enabled' => true],
                ['key' => 'payment_id', 'label' => 'Payment ID', 'enabled' => true],
                ['key' => 'due_amount', 'label' => 'Due', 'enabled' => true],
                ['key' => 'status', 'label' => 'Status', 'enabled' => true]
            ];
        }

        $this->view('customers/index', [
            'title' => 'All Customers',
            'path' => '/customer',
            'customers' => $customers,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'sort' => $sort,
            'order' => $order,
            'totalRecords' => $totalRecords,
            'q' => $q,
            'statuses' => $statuses,
            'tableColumns' => $tableColumns
        ]);
    }

    /**
     * List pending customers with pagination, sorting, and filtering.
     * 
     * @return void
     */
    public function pending()
    {
        // 1. Pagination Settings
        $limit = 30;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1)
            $page = 1;
        $offset = ($page - 1) * $limit;

        // 2. Sorting Settings
        $allowedSortColumns = ['id', 'full_name', 'mobile_no', 'area', 'package_name', 'created_at', 'status'];
        $sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSortColumns) ? $_GET['sort'] : 'id';
        $order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';

        // 3. Filtering & Search Logic
        $where = ["c.status = 'pending'"]; // Always filter for pending status
        $params = [];

        $q = $_GET['q'] ?? '';
        if ($q) {
            $where[] = "(LOWER(c.full_name) LIKE ? OR c.mobile_no LIKE ? OR LOWER(c.area) LIKE ? OR CONCAT(LOWER(COALESCE(ip.prefix_code,'')), c.id) LIKE ?)";
            $term = "%" . strtolower($q) . "%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        // 4. Get Total Count
        $joins = "LEFT JOIN packages p ON c.package_id = p.id LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id";
        $countSql = "SELECT COUNT(*) FROM customers c $joins $whereSql";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $totalRecords = $countStmt->fetchColumn();
        $totalPages = ceil($totalRecords / $limit);

        // 5. Fetch Records
        $sql = "SELECT c.*, p.name as package_name, ip.prefix_code 
                FROM customers c 
                $joins
                $whereSql
                ORDER BY c.$sort $order LIMIT $limit OFFSET $offset";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Table Columns for Pending
        $colStmt = $this->db->prepare("SELECT columns_json FROM table_settings WHERE table_name = 'pending_customers'");
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
                ['key' => 'full_name', 'label' => 'Name', 'enabled' => true],
                ['key' => 'mobile_no', 'label' => 'Mobile', 'enabled' => true],
                ['key' => 'area', 'label' => 'Area', 'enabled' => true],
                ['key' => 'package_name', 'label' => 'Package', 'enabled' => true],
                ['key' => 'created_at', 'label' => 'Request Date', 'enabled' => true],
                ['key' => 'status', 'label' => 'Status', 'enabled' => true],
            ];
        }

        $this->view('customers/pending', [
            'title' => 'Pending Customers',
            'path' => '/customer/pending',
            'customers' => $customers,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'sort' => $sort,
            'order' => $order,
            'totalRecords' => $totalRecords,
            'q' => $q,
            'tableColumns' => $tableColumns
        ]);
    }



    /**
     * Show the create customer form.
     * 
     * @return void
     */
    public function create()
    {
        $employees = $this->db->query("SELECT id, name FROM employees ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $packages = $this->db->query("SELECT id, name, price FROM packages ORDER BY price ASC")->fetchAll(PDO::FETCH_ASSOC);
        $prefixStmt = $this->db->query("SELECT prefix_code FROM id_prefixes WHERE is_default = TRUE LIMIT 1");
        $defaultPrefix = $prefixStmt->fetchColumn() ?: '';
        $nextIdStmt = $this->db->query("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customers'");
        $nextId = $nextIdStmt->fetchColumn() ?: 1;

        // Fetch Dynamic Form Configuration
        $sections = $this->db->query("SELECT * FROM customer_form_sections ORDER BY order_index ASC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($sections as &$section) {
            $section['fields'] = $this->db->prepare("SELECT * FROM customer_form_fields WHERE section_id = ? AND is_visible = 1 ORDER BY order_index ASC");
            $section['fields']->execute([$section['id']]);
            $section['fields'] = $section['fields']->fetchAll(PDO::FETCH_ASSOC);
        }

        $this->view('customers/create', [
            'title' => 'Create Customer',
            'path' => '/customer/create',
            'employees' => $employees,
            'packages' => $packages,
            'defaultPrefix' => $defaultPrefix,
            'nextId' => $nextId,
            'formSections' => $sections
        ]);
    }

    /**
     * Handle Create Customer (POST).
     * 
     * @return void Sends JSON response.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true) ?: $_POST;

            try {
                $this->db->beginTransaction();

                // 1. Get Standard Field Keys
                $standardFieldsStmt = $this->db->query("SELECT field_key FROM customer_form_fields WHERE is_standard = 1");
                $standardKeys = $standardFieldsStmt->fetchAll(PDO::FETCH_COLUMN);

                // 2. Prepare Standard Fields SQL
                $cols = [];
                $placeholders = [];
                $values = [];

                foreach ($standardKeys as $key) {
                    // Map special keys if needed
                    $dbKey = ($key === 'connection_type_tech') ? 'connection_type' : $key;

                    if (isset($data[$key])) {
                        $cols[] = $dbKey;
                        $placeholders[] = "?";
                        $values[] = $data[$key];
                    }
                }

                // Add prefix_id if not in dynamic fields
                $prefixStmt = $this->db->query("SELECT id FROM id_prefixes WHERE is_default = TRUE LIMIT 1");
                $prefixId = $prefixStmt->fetchColumn();
                if ($prefixId) {
                    $cols[] = 'prefix_id';
                    $placeholders[] = "?";
                    $values[] = $prefixId;
                }

                $sql = "INSERT INTO customers (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $placeholders) . ")";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($values);
                $customerId = $this->db->lastInsertId();

                // 3. Handle Custom (Meta) Fields
                $customFieldsStmt = $this->db->query("SELECT field_key FROM customer_form_fields WHERE is_standard = 0");
                $customKeys = $customFieldsStmt->fetchAll(PDO::FETCH_COLUMN);

                foreach ($customKeys as $key) {
                    if (isset($data[$key])) {
                        $metaStmt = $this->db->prepare("INSERT INTO customer_meta (customer_id, field_key, field_value) VALUES (?, ?, ?)");
                        $metaStmt->execute([$customerId, $key, $data[$key]]);
                    }
                }

                $this->db->commit();
                return $this->json(['status' => 'success', 'message' => 'Created successfully', 'id' => $customerId]);
            } catch (\Exception $e) {
                if ($this->db->inTransaction())
                    $this->db->rollBack();
                return $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        }
    }

    /**
     * Handle Update Customer (POST).
     * 
     * @param int $id The customer ID.
     * @return void Sends JSON response.
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $sql = "UPDATE customers SET 
                full_name=?, email=?, identification_no=?, mobile_no=?, alt_mobile_no=?, professional_detail=?,
                district=?, thana=?, area=?, building_name=?, floor=?, tj_box=?, house_no=?,
                fiber_code=?, onu_mac=?, group_name=?, lazar_info=?, latitude=?, longitude=?, server_info=?, connection_date=?, expire_date=?,
                mikrotik_id=?, pppoe_name=?, pppoe_password=?, pppoe_profile=?, ip_address=?, mac_address=?, bandwidth=?, comment=?,
                package_id=?, monthly_rent=?, payment_id=?, due_amount=?, additional_charge=?, discount=?, advance_amount=?, vat_percent=?, total_amount=?,
                billing_type=?, connectivity_type=?, connection_type=?, client_type=?, distribution_point=?, description=?, note=?, connected_by=?, reference_name=?, security_deposit=?, status=?,
                auto_disable=?, auto_disable_month=?, extra_days=?, extra_days_type=?
                WHERE id=?";

            $values = [
                $data['full_name'] ?? null,
                $data['email'] ?? null,
                $data['identification_no'] ?? null,
                $data['mobile_no'] ?? null,
                $data['alt_mobile_no'] ?? null,
                $data['professional_detail'] ?? null,
                $data['district'] ?? null,
                $data['thana'] ?? null,
                $data['area'] ?? null,
                $data['building_name'] ?? null,
                $data['floor'] ?? null,
                $data['tj_box'] ?? null,
                $data['house_no'] ?? null,
                $data['fiber_code'] ?? null,
                $data['onu_mac'] ?? null,
                $data['group_name'] ?? null,
                $data['lazar_info'] ?? null,
                $data['latitude'] ?? null,
                $data['longitude'] ?? null,
                $data['server_info'] ?? null,
                $data['connection_date'] ?? null,
                $data['expire_date'] ?? null,
                $data['mikrotik_id'] ?? 1,
                $data['pppoe_name'] ?? null,
                $data['pppoe_password'] ?? null,
                $data['pppoe_profile'] ?? null,
                $data['ip_address'] ?? null,
                $data['mac_address'] ?? null,
                $data['bandwidth'] ?? null,
                $data['comment'] ?? null,
                $data['package_id'] ?? null,
                (float) ($data['monthly_rent'] ?? 0),
                $data['payment_id'] ?? null,
                (float) ($data['due_amount'] ?? 0),
                (float) ($data['additional_charge'] ?? 0),
                (float) ($data['discount'] ?? 0),
                (float) ($data['advance_amount'] ?? 0),
                (float) ($data['vat_percent'] ?? 0),
                (float) ($data['total_amount'] ?? 0),
                $data['billing_type'] ?? null,
                $data['connectivity_type'] ?? null,
                $data['connection_type'] ?? null,
                $data['client_type'] ?? null,
                $data['distribution_point'] ?? null,
                $data['description'] ?? null,
                $data['note'] ?? null,
                $data['connected_by'] ?? null,
                $data['reference_name'] ?? null,
                (float) ($data['security_deposit'] ?? 0),
                $data['status'] ?? 'active',
                $data['auto_disable'] ?? 0,
                $data['auto_disable_month'] ?? 0,
                $data['extra_days'] ?? 0,
                $data['extra_days_type'] ?? 'One month',
                $id
            ];

            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute($values);
                return $this->json(['status' => 'success', 'message' => 'Updated successfully']);
            } catch (\Exception $e) {
                return $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        }
    }

    /**
     * Delete a customer.
     * 
     * @param int $id The customer ID.
     * @return void Redirects back.
     */
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = $this->db->prepare("DELETE FROM customers WHERE id = ?");
            $stmt->execute([$id]);
            return $this->redirect($_SERVER['HTTP_REFERER'] ?? '/customer');
        }
    }

    /**
     * Show the customer search view.
     * 
     * @return void
     */
    public function search()
    {
        $this->view('customers/search', [
            'title' => 'Search Customer',
            'path' => '/customer/search'
        ]);
    }

    /**
     * Show a specific customer profile.
     * 
     * @param int $id The customer ID.
     * @return void
     */
    public function show($id)
    {
        $stmt = $this->db->prepare("SELECT c.*, p.name as package_name, ip.prefix_code 
                                    FROM customers c 
                                    LEFT JOIN packages p ON c.package_id = p.id 
                                    LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id
                                    WHERE c.id = ?");
        $stmt->execute([$id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$customer) {
            die("Customer not found");
        }

        $employees = $this->db->query("SELECT id, name FROM employees ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $packages = $this->db->query("SELECT id, name, price FROM packages ORDER BY price ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->view('customers/show', [
            'title' => 'Customer Profile: ' . $customer['full_name'],
            'path' => '/customer',
            'c' => $customer,
            'employees' => $employees,
            'packages' => $packages
        ]);
    }

    /**
     * AJAX Search/Filter customers.
     * 
     * @return void Sends JSON response.
     */
    public function filter()
    {
        $query = $_GET['q'] ?? '';
        $sql = "SELECT c.*, ip.prefix_code FROM customers c LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id ORDER BY c.id DESC LIMIT 20";
        $params = [];

        if ($query) {
            $sql = "SELECT c.*, ip.prefix_code 
                    FROM customers c 
                    LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id 
                    WHERE (LOWER(c.full_name) LIKE ? OR c.mobile_no LIKE ? OR LOWER(c.identification_no) LIKE ? OR LOWER(c.payment_id) LIKE ? OR CONCAT(LOWER(COALESCE(ip.prefix_code,'')), c.id) LIKE ?) 
                    LIMIT 50";
            $term = "%" . strtolower($query) . "%";
            $params = [$term, $term, $term, $term, $term];
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->json(['status' => 'success', 'data' => $rows]);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Activate a pending customer.
     * 
     * @param int $id The customer ID.
     * @return void Redirects back.
     */
    public function activate($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = $this->db->prepare("UPDATE customers SET status = 'active' WHERE id = ?");
            $stmt->execute([$id]);
            return $this->redirect($_SERVER['HTTP_REFERER'] ?? '/dashboard');
        }
    }

    /**
     * Seed dummy data (For internal/dev use).
     * 
     * @return void Redirects to customer list.
     */
    public function seed()
    {
        $prefixStmt = $this->db->query("SELECT id FROM id_prefixes WHERE is_default = TRUE LIMIT 1");
        $prefixId = $prefixStmt->fetchColumn() ?: null;

        $dummy = [
            ['Pending User 1', '01712345671', 'Dhaka', 'pending', $prefixId],
            ['Pending User 2', '01812345672', 'Ctg', 'pending', $prefixId],
            ['Active User 1', '01912345673', 'Sylhet', 'active', $prefixId],
        ];

        $stmt = $this->db->prepare("INSERT INTO customers (full_name, mobile_no, area, status, prefix_id) VALUES (?, ?, ?, ?, ?)");
        foreach ($dummy as $d) {
            $stmt->execute($d);
        }
        return $this->redirect('/customer');
    }

    /**
     * Bulk Import Customers (JSON).
     * 
     * @return void Sends JSON response.
     */
    public function import()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $customers = $data['customers'] ?? [];

            if (empty($customers)) {
                return $this->json(['status' => 'error', 'message' => 'No customer data provided.'], 400);
            }

            // Get default prefix
            $prefixStmt = $this->db->query("SELECT id FROM id_prefixes WHERE is_default = TRUE LIMIT 1");
            $prefixId = $prefixStmt->fetchColumn() ?: null;

            // Fetch package map for name -> id lookup (case-insensitive)
            $pkgs = $this->db->query("SELECT id, name FROM packages")->fetchAll(PDO::FETCH_ASSOC);
            $pkgMap = [];
            foreach ($pkgs as $p) {
                $pkgMap[strtolower(trim($p['name']))] = $p['id'];
            }

            // --- Fuzzy Field Mapping (Excel Header -> DB Column) ---
            // Only columns present in this map will be processed. Others are ignored.
            $fieldMap = [
                // Identity
                'identification_no' => ['identification_no', 'nid', 'birth_cert', 'national_id'],
                'full_name' => ['full_name', 'name', 'customer_name', 'customer', 'client_name'],
                'mobile_no' => ['mobile_no', 'mobile', 'phone', 'contact', 'cell', 'phone_no'],
                'alt_mobile_no' => ['alt_mobile_no', 'alt_mobile', 'alternative_mobile', 'backup_phone'],
                'email' => ['email', 'mail', 'e-mail'],
                'professional_detail' => ['professional_detail', 'profession', 'occupation', 'job'],

                // Address
                'district' => ['district', 'city'],
                'thana' => ['thana', 'ps', 'police_station'],
                'area' => ['area', 'zone', 'location'],
                'building_name' => ['building_name', 'building'],
                'floor' => ['floor', 'level'],
                'tj_box' => ['tj_box', 'tj', 'box'],
                'house_no' => ['house_no', 'house', 'flat', 'apt'],
                'fiber_code' => ['fiber_code', 'fiber', 'cable_code'],
                'onu_mac' => ['onu_mac', 'onu', 'onu_id'],
                'group_name' => ['group_name', 'group', 'batch'],
                'lazar_info' => ['lazar_info', 'lazar'],
                'server_info' => ['server_info', 'server'],

                // Tech
                'pppoe_name' => ['pppoe_name', 'pppoe_user', 'user_id', 'username'],
                'pppoe_password' => ['pppoe_password', 'password', 'pass', 'user_pass'],
                'pppoe_profile' => ['pppoe_profile', 'profile'],
                'ip_address' => ['ip_address', 'ip', 'static_ip'],
                'mac_address' => ['mac_address', 'mac', 'device_mac'],
                'bandwidth' => ['bandwidth', 'speed', 'mbps'],
                'comment' => ['comment', 'remarks'],

                // Billing
                'package_id' => ['package_id', 'package', 'plan', 'internet_package'], // Logic handled separate
                'monthly_rent' => ['monthly_rent', 'rent', 'bill', 'monthly_fee', 'price'],
                'payment_id' => ['payment_id', 'payment_code', 'client_id', 'customer_id_manual'],
                'due_amount' => ['due_amount', 'due', 'outstanding', 'previous_due'],
                'additional_charge' => ['additional_charge', 'additional', 'extra_charge'],
                'discount' => ['discount', 'discount_amount', 'less'],
                'advance_amount' => ['advance_amount', 'advance', 'prepaid_amount'],
                'vat_percent' => ['vat_percent', 'vat', 'tax'],
                'total_amount' => ['total_amount', 'total', 'grand_total'],
                'security_deposit' => ['security_deposit', 'deposit', 'security'],

                // Others
                'billing_type' => ['billing_type', 'type_billing', 'pay_type'], // Pre Paid / Post Paid
                'connectivity_type' => ['connectivity_type', 'conn_type'], // Shared / Dedicated
                'connection_type' => ['connection_type', 'cable_type'], // Fiber / Cat5
                'client_type' => ['client_type'], // Home / Corporate
                'distribution_point' => ['distribution_point', 'dp', 'dist_point'],
                'description' => ['description', 'desc', 'details'],
                'note' => ['note'],
                'connected_by' => ['connected_by', 'agent', 'employee', 'technician'],
                'reference_name' => ['reference_name', 'ref', 'reference'],
                'status' => ['status', 'state'] // pending/active/etc
            ];

            $createdCount = 0;
            $updatedCount = 0;
            $errors = [];

            foreach ($customers as $index => $row) {
                // ... (previous logic for mapping) ...
                // Re-implementing logic inside loop to ensure scope access or use existing vars
                // Actually, due to replace_file_content scope, need to be careful.
                // Re-writing the loop inner part properly:

                $dbRow = [];
                $rowLower = array_change_key_case($row, CASE_LOWER);

                // 1. Map Fields
                foreach ($fieldMap as $dbCol => $aliases) {
                    $foundValue = null;
                    foreach ($aliases as $alias) {
                        if (array_key_exists($alias, $rowLower)) {
                            $foundValue = $rowLower[$alias];
                            break;
                        }
                    }
                    if ($foundValue !== null && $foundValue !== '') {
                        $dbRow[$dbCol] = $foundValue;
                    }
                }

                // 2. Validate
                // Note: Only validate name/mobile if we are creating new. 
                // But for matching we need mobile.

                // Fallback attempt for mobile/name in case mapping missed (redundant but safe)
                $mobile = $dbRow['mobile_no'] ?? $rowLower['mobile_no'] ?? $rowLower['mobile'] ?? null;
                if (!$mobile) {
                    $errors[] = "Row " . ($index + 1) . ": Skipped (Mobile is required).";
                    continue;
                }
                $dbRow['mobile_no'] = $mobile; // ensure set

                // 3. Package Lookup
                if (isset($dbRow['package_id']) && !is_numeric($dbRow['package_id'])) {
                    $pkgName = strtolower(trim($dbRow['package_id']));
                    $dbRow['package_id'] = $pkgMap[$pkgName] ?? null;
                }

                // 4. Defaults for New Records Only (Applied later if INSERT)
                $dbRow['prefix_id'] = $prefixId;

                // 5. Check Existence
                $checkStmt = $this->db->prepare("SELECT id FROM customers WHERE mobile_no = ?");
                $checkStmt->execute([$mobile]);
                $existingId = $checkStmt->fetchColumn();

                try {
                    if ($existingId) {
                        // UPDATE Logic
                        // Only update fields present in $dbRow (except mobile).
                        unset($dbRow['mobile_no']); // Don't update unique key

                        if (empty($dbRow)) {
                            // Nothing to update
                            continue;
                        }

                        $updateParts = [];
                        $params = [];
                        foreach ($dbRow as $col => $val) {
                            $updateParts[] = "$col = ?";
                            $params[] = $val;
                        }
                        $params[] = $existingId; // WHERE id = ?

                        $sql = "UPDATE customers SET " . implode(', ', $updateParts) . " WHERE id = ?";
                        $stmt = $this->db->prepare($sql);
                        $stmt->execute($params);
                        $updatedCount++;
                    } else {
                        // INSERT Logic
                        if (empty($dbRow['full_name'])) {
                            // Name only required for INSERT
                            $errors[] = "Row " . ($index + 1) . ": Skipped (New customer requires Name).";
                            continue;
                        }

                        // Apply Defaults for Insert
                        if (!isset($dbRow['status']))
                            $dbRow['status'] = 'active';
                        $numericFields = ['monthly_rent', 'due_amount', 'additional_charge', 'discount', 'advance_amount', 'vat_percent', 'total_amount', 'security_deposit'];
                        foreach ($numericFields as $field) {
                            if (!isset($dbRow[$field]))
                                $dbRow[$field] = 0;
                        }

                        $cols = array_keys($dbRow);
                        $placeholders = implode(',', array_fill(0, count($cols), '?'));
                        $colNames = implode(',', $cols);

                        $sql = "INSERT INTO customers ($colNames) VALUES ($placeholders)";
                        $stmt = $this->db->prepare($sql);
                        $stmt->execute(array_values($dbRow));
                        $createdCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            $msg = "Import Complete. Created: $createdCount. Updated: $updatedCount.";
            if (count($errors) > 0) {
                $msg .= " Failed: " . count($errors);
            }

            return $this->json([
                'success' => true,
                'status' => 'success',
                'message' => $msg,
                'created' => $createdCount,
                'updated' => $updatedCount,
                'failed' => count($errors),
                'errors' => $errors
            ]);
        }
    }
}
