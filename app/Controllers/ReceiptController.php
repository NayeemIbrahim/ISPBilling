<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

class ReceiptController extends Controller
{
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->ensureTablesExist();
    }

    private function ensureTablesExist()
    {
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS `print_settings` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `header_style` VARCHAR(50) DEFAULT 'with_header',
                `layout` VARCHAR(50) DEFAULT '3',
                `receipt_text` VARCHAR(50) DEFAULT 'Thank you for connecting with us.',
                `signature_path` VARCHAR(255) DEFAULT NULL,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");

            // Seed default if empty
            $count = $this->db->query("SELECT COUNT(*) FROM `print_settings`")->fetchColumn();
            if ($count == 0) {
                $this->db->exec("INSERT INTO `print_settings` (header_style, layout, receipt_text) VALUES ('with_header', '3', 'Thank you for connecting with us.')");
            }
        } catch (\Exception $e) {
            // Silently fail or log
        }
    }

    /**
     * Area Wise Receipt View
     */
    public function areaWise()
    {
        // 1. Fetch Filter Options
        $areas = $this->db->query("SELECT DISTINCT area FROM customers WHERE area IS NOT NULL AND area != '' ORDER BY area")->fetchAll(PDO::FETCH_COLUMN);
        // 2. Build Query based on filters
        $area = $_GET['area'] ?? '';
        $package_id = $_GET['package_id'] ?? '';
        $status = $_GET['status'] ?? '';
        $connected_by = $_GET['connected_by'] ?? '';

        $query = "SELECT c.*, p.name as package_name, ip.prefix_code 
                  FROM customers c 
                  LEFT JOIN packages p ON c.package_id = p.id 
                  LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id 
                  WHERE 1=1";
        $params = [];

        if ($area) {
            $query .= " AND c.area = ?";
            $params[] = $area;
        }

        if ($package_id) {
            $query .= " AND c.package_id = ?";
            $params[] = $package_id;
        }

        if ($status) {
            $query .= " AND c.status = ?";
            $params[] = $status;
        }

        if ($connected_by) {
            $query .= " AND c.connected_by = ?";
            $params[] = $connected_by;
        }

        $query .= " ORDER BY c.full_name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch filter options
        $areas = $this->db->query("SELECT DISTINCT area FROM customers WHERE area IS NOT NULL AND area != '' ORDER BY area")->fetchAll(PDO::FETCH_COLUMN);
        $packages = $this->db->query("SELECT id, name FROM packages ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $employees = $this->db->query("SELECT id, display_name FROM users WHERE role = 'Employee' OR role = 'Admin' ORDER BY display_name ASC")->fetchAll(PDO::FETCH_ASSOC);

        // 3. Fetch Print Settings
        $printSettings = $this->db->query("SELECT * FROM print_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

        $this->view('receipt/area_wise', [
            'title' => 'Area Wise Receipt',
            'path' => '/receipt/area-wise',
            'customers' => $customers,
            'areas' => $areas,
            'packages' => $packages,
            'employees' => $employees,
            'selectedArea' => $area,
            'selectedPackage' => $package_id,
            'selectedStatus' => $status,
            'selectedEmployee' => $connected_by,
            'printSettings' => $printSettings
        ]);
    }

    /**
     * Customer Wise Receipt View
     */
    public function customerWise()
    {
        $search = $_GET['search'] ?? '';
        $month = $_GET['month'] ?? ''; // Not mandatory anymore
        $customers = [];
        
        if ($search) {
            $query = "SELECT c.*, p.name as package_name, ip.prefix_code 
                      FROM customers c 
                      LEFT JOIN packages p ON c.package_id = p.id 
                      LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id 
                      WHERE c.full_name LIKE ? 
                         OR c.mobile_no LIKE ? 
                         OR c.pppoe_name LIKE ? 
                         OR c.payment_id LIKE ? 
                         OR CONCAT(COALESCE(ip.prefix_code, ''), c.id) LIKE ? 
                      ORDER BY c.full_name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Fetch Print Settings
        $printSettings = $this->db->query("SELECT * FROM print_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

        $this->view('receipt/customer_wise', [
            'title' => 'Customer Wise Receipt',
            'path' => '/receipt/customer-wise',
            'search' => $search,
            'selectedMonth' => $month,
            'customers' => $customers,
            'printSettings' => $printSettings
        ]);
    }

    public function searchAjax()
    {
        $q = $_GET['q'] ?? '';
        if (strlen($q) < 2) {
            echo json_encode([]);
            return;
        }

        $query = "SELECT c.id, c.full_name, c.mobile_no, c.pppoe_name, c.area, c.monthly_rent, ip.prefix_code, p.name as package_name 
                  FROM customers c 
                  LEFT JOIN packages p ON c.package_id = p.id 
                  LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id 
                  WHERE c.full_name LIKE ? 
                     OR c.mobile_no LIKE ? 
                     OR c.pppoe_name LIKE ? 
                     OR c.payment_id LIKE ? 
                     OR CONCAT(COALESCE(ip.prefix_code, ''), c.id) LIKE ? 
                  ORDER BY c.full_name ASC LIMIT 50";
        $stmt = $this->db->prepare($query);
        $stmt->execute(["%$q%", "%$q%", "%$q%", "%$q%", "%$q%"]);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($customers);
    }

    /**
     * Batch Print Receipts
     */
    public function print()
    {
        $idsStr = $_GET['ids'] ?? '';
        $month = $_GET['month'] ?? '';
        if (!$idsStr) {
            die("No IDs provided.");
        }
        $ids = explode(',', $idsStr);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $query = "SELECT c.*, p.name as package_name, ip.prefix_code 
                  FROM customers c 
                  LEFT JOIN packages p ON c.package_id = p.id 
                  LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id 
                  WHERE c.id IN ($placeholders)";
        $stmt = $this->db->prepare($query);
        $stmt->execute($ids);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch received payments for each customer
        foreach ($customers as &$cust) {
            $received = 0.00;
            $paymentDate = null;
            $paymentMethod = 'N/A';
            $invoiceNo = '';

            // If a specific month is selected, try to get the payment made in that month
            if (!empty($month)) {
                $pStmt = $this->db->prepare("
                    SELECT amount, collection_date, payment_method, invoice_no 
                    FROM collections 
                    WHERE customer_id = ? AND DATE_FORMAT(collection_date, '%Y-%m') = ? 
                    ORDER BY id DESC LIMIT 1
                ");
                $pStmt->execute([$cust['id'], $month]);
                $payment = $pStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($payment) {
                    $received = floatval($payment['amount']);
                    $paymentDate = $payment['collection_date'];
                    $paymentMethod = $payment['payment_method'];
                    $invoiceNo = $payment['invoice_no'];
                }
            }

            // Fallback: If no payment found for selected month or no month selected, get the absolute last payment
            if ($received == 0) {
                $pStmt = $this->db->prepare("
                    SELECT amount, collection_date, payment_method, invoice_no 
                    FROM collections 
                    WHERE customer_id = ? 
                    ORDER BY id DESC LIMIT 1
                ");
                $pStmt->execute([$cust['id']]);
                $payment = $pStmt->fetch(PDO::FETCH_ASSOC);

                if ($payment) {
                    $received = floatval($payment['amount']);
                    $paymentDate = $payment['collection_date'];
                    $paymentMethod = $payment['payment_method'];
                    $invoiceNo = $payment['invoice_no'];
                }
            }

            // Store inside the customer array
            $cust['received_amount'] = $received;
            $cust['payment_date'] = $paymentDate;
            $cust['payment_method'] = $paymentMethod;
            $cust['payment_invoice_no'] = $invoiceNo;
        }

        $printSettings = $this->db->query("SELECT * FROM print_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

        $this->view('receipt/print', [
            'customers' => $customers,
            'settings' => $printSettings,
            'billingMonth' => $month
        ]);
    }

    /**
     * Print Individual Money Receipt for a specific collection transaction
     */
    public function collection()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("Collection transaction ID required.");
        }

        // Query collection and customer details together
        $query = "SELECT col.id as transaction_id, col.amount, col.payment_method, col.collection_date, col.invoice_no, 
                         col.next_expire_date, col.note, col.collected_by,
                         cust.id as customer_id, cust.full_name, cust.mobile_no, cust.status, cust.expire_date,
                         cust.payment_id, cust.pppoe_name, cust.monthly_rent, cust.due_amount, cust.connection_date,
                         cust.house_no, cust.area, cust.thana, cust.district,
                         emp.name as collected_by_name,
                         ip.prefix_code
                  FROM collections col 
                  JOIN customers cust ON col.customer_id = cust.id 
                  LEFT JOIN employees emp ON col.collected_by = emp.id
                  LEFT JOIN id_prefixes ip ON cust.prefix_id = ip.id
                  WHERE col.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $col = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$col) {
            die("Collection transaction not found.");
        }

        // Fetch print settings
        $printSettings = $this->db->query("SELECT * FROM print_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

        // Render view
        $this->view('receipt/print_collection', [
            'col' => $col,
            'settings' => $printSettings
        ]);
    }
}
