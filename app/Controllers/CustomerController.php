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
        $limit = 20;
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
            'statuses' => $statuses
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

        $this->view('customers/create', [
            'title' => 'Create Customer',
            'path' => '/customer/create',
            'employees' => $employees,
            'packages' => $packages,
            'defaultPrefix' => $defaultPrefix,
            'nextId' => $nextId
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

            $sql = "INSERT INTO customers (
                full_name, email, identification_no, mobile_no, alt_mobile_no, professional_detail,
                district, thana, area, building_name, floor, tj_box, house_no,
                fiber_code, onu_mac, group_name, lazar_info, latitude, longitude, server_info, connection_date, expire_date,
                mikrotik_id, pppoe_name, pppoe_password, pppoe_profile, ip_address, mac_address, bandwidth, comment,
                package_id, monthly_rent, payment_id, due_amount, additional_charge, discount, advance_amount, vat_percent, total_amount,
                billing_type, connectivity_type, connection_type, client_type, distribution_point, description, note, connected_by, reference_name, security_deposit, status, prefix_id,
                auto_disable, auto_disable_month, extra_days, extra_days_type
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $prefixStmt = $this->db->query("SELECT id FROM id_prefixes WHERE is_default = TRUE LIMIT 1");
            $prefixId = $prefixStmt->fetchColumn();

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
                $data['status'] ?? 'pending',
                $prefixId ?: null,
                $data['auto_disable'] ?? 0,
                $data['auto_disable_month'] ?? 0,
                $data['extra_days'] ?? 0,
                $data['extra_days_type'] ?? 'One month'
            ];

            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute($values);
                return $this->json(['status' => 'success', 'message' => 'Created successfully']);
            } catch (\Exception $e) {
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
     * List pending customers with pagination and search.
     * 
     * @return void
     */
    public function pending()
    {
        $limit = 20;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1)
            $page = 1;
        $offset = ($page - 1) * $limit;

        $allowedSortColumns = ['id', 'full_name', 'mobile_no', 'area', 'package_name', 'created_at'];
        $sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSortColumns) ? $_GET['sort'] : 'id';
        $order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';

        $where = ["c.status = 'pending'"];
        $params = [];
        $joins = "LEFT JOIN packages p ON c.package_id = p.id LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id";

        $q = $_GET['q'] ?? '';
        if ($q) {
            $where[] = "(LOWER(c.full_name) LIKE ? OR c.mobile_no LIKE ? OR LOWER(c.area) LIKE ? OR CONCAT(LOWER(COALESCE(ip.prefix_code,'')), c.id) LIKE ?)";
            $term = "%" . strtolower($q) . "%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        $whereSql = "WHERE " . implode(" AND ", $where);
        $countSql = "SELECT COUNT(*) FROM customers c $joins $whereSql";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $totalRecords = $countStmt->fetchColumn();
        $totalPages = ceil($totalRecords / $limit);

        $sql = "SELECT c.*, p.name as package_name, ip.prefix_code 
                FROM customers c 
                $joins
                $whereSql 
                ORDER BY c.$sort $order LIMIT $limit OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('customers/pending', [
            'title' => 'Pending Customers',
            'path' => '/customer/pending',
            'customers' => $customers,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'sort' => $sort,
            'order' => $order,
            'q' => $q
        ]);
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
}
