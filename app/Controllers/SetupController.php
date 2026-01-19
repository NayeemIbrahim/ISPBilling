<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

/**
 * SetupController
 * 
 * Handles general application setup, including package and merchant management.
 */
class SetupController extends Controller
{
    /**
     * SetupController constructor.
     */
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Handles package setup view and operations.
     * 
     * @return void
     */
    public function package()
    {
        // 1. Auto-Migration (Fix for missing tables)
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS merchants (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            $this->db->exec("CREATE TABLE IF NOT EXISTS packages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                price DECIMAL(10, 2) NOT NULL,
                description TEXT,
                merchant_id INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE SET NULL
            )");

            // Seed Merchants if empty
            $count = $this->db->query("SELECT COUNT(*) FROM merchants")->fetchColumn();
            if ($count == 0) {
                $this->db->exec("INSERT INTO merchants (name) VALUES ('HK ISP'), ('Bangla Link'), ('Airtel')");
            }

        } catch (\Exception $e) {
            // Log error or continue
        }

        $message = '';
        $messageType = '';

        // Handle POST Request (Create/Update Package)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $price = $_POST['price'] ?? 0;
            $description = $_POST['description'] ?? '';
            $merchant_id = $_POST['merchant_id'] ?? null;

            if ($name && $price) {
                try {
                    if ($id) {
                        // Update
                        $sql = "UPDATE packages SET name=:name, price=:price, description=:description, merchant_id=:merchant_id WHERE id=:id";
                        $stmt = $this->db->prepare($sql);
                        $stmt->execute([
                            ':name' => $name,
                            ':price' => $price,
                            ':description' => $description,
                            ':merchant_id' => $merchant_id ?: null,
                            ':id' => $id
                        ]);
                        $message = "Package updated successfully!";
                    } else {
                        // Create
                        $sql = "INSERT INTO packages (name, price, description, merchant_id) VALUES (:name, :price, :description, :merchant_id)";
                        $stmt = $this->db->prepare($sql);
                        $stmt->execute([
                            ':name' => $name,
                            ':price' => $price,
                            ':description' => $description,
                            ':merchant_id' => $merchant_id ?: null
                        ]);
                        $message = "Package created successfully!";
                    }
                    $messageType = "success";
                } catch (\PDOException $e) {
                    $message = "Error saving package: " . $e->getMessage();
                    $messageType = "error";
                }
            } else {
                $message = "Name and Price are required.";
                $messageType = "error";
            }
        }

        // Fetch Merchants for the dropdown
        $merchants = $this->db->query("SELECT * FROM merchants ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Packages for the list
        $sql = "SELECT p.*, m.name as merchant_name 
                FROM packages p 
                LEFT JOIN merchants m ON p.merchant_id = m.id 
                ORDER BY p.id DESC";
        $packages = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $this->view('setup/package', [
            'title' => 'Package Setup',
            'path' => '/setup/package',
            'merchants' => $merchants,
            'packages' => $packages,
            'message' => $message,
            'messageType' => $messageType
        ]);
    }
    /**
     * Handle Column Preview Setup.
     * 
     * @return void
     */
    public function columnPreview()
    {
        // 1. Auto-Migration for table_settings
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS table_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                table_name VARCHAR(50) NOT NULL UNIQUE,
                columns_json TEXT NOT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");
        } catch (\Exception $e) {
            // Log error
        }

        // 1. Determine which table we are editing
        $allowedTables = ['all_customers', 'pending_customers', 'complain_list', 'collection_report', 'customer_summary', 'due_list', 'inactive_list'];
        $currentTable = $_GET['table'] ?? 'all_customers';
        if (!in_array($currentTable, $allowedTables)) {
            $currentTable = 'all_customers';
        }

        $message = '';
        $messageType = '';

        // 2. Handle POST Request (Save Settings)
        if (isset($_GET['action']) && $_GET['action'] === 'reset') {
            $del = $this->db->prepare("DELETE FROM table_settings WHERE table_name = ?");
            $del->execute([$currentTable]);
            $message = "Settings reset to default for " . ucwords(str_replace('_', ' ', $currentTable));
            $messageType = "success";
        }
        // 2. Handle POST Request (Save Settings)
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $columns = $_POST['columns'] ?? [];
            // $columns is array of {key: "...", enabled: "1" or "0"}

            // Re-index to preserve order from the UI
            $toSave = [];
            if (is_array($columns)) {
                foreach ($columns as $idx => $val) {
                    // FIX: Use $val['key'] instead of loop index $key
                    if (isset($val['key'])) {
                        $toSave[] = [
                            'key' => $val['key'],
                            'label' => $val['label'] ?? ucfirst(str_replace('_', ' ', $val['key'])),
                            'enabled' => isset($val['enabled']) ? true : false
                        ];
                    }
                }
            }

            if (!empty($toSave)) {
                $json = json_encode($toSave);
                // Upsert
                $check = $this->db->prepare("SELECT id FROM table_settings WHERE table_name = ?");
                $check->execute([$currentTable]);
                if ($check->fetch()) {
                    $sql = "UPDATE table_settings SET columns_json = ?, updated_at = NOW() WHERE table_name = ?";
                } else {
                    $sql = "INSERT INTO table_settings (columns_json, table_name) VALUES (?, ?)";
                }
                $this->db->prepare($sql)->execute([$json, $currentTable]);

                $message = "Columns updated successfully for " . ucwords(str_replace('_', ' ', $currentTable));
                $messageType = "success";
            }
        }

        // 3. Get Definitions for the selected table
        $allPossibleColumns = $this->getColumnDefinitions($currentTable);

        // 4. Fetch existing settings
        $stmt = $this->db->prepare("SELECT columns_json FROM table_settings WHERE table_name = ?");
        $stmt->execute([$currentTable]);
        $json = $stmt->fetchColumn();

        if ($json) {
            $savedColumns = json_decode($json, true);

            // Merge: Keep saved order/status, append missing new columns
            $savedKeys = array_column($savedColumns, 'key');
            foreach ($allPossibleColumns as $defCol) {
                if (!in_array($defCol['key'], $savedKeys)) {
                    $savedColumns[] = $defCol;
                }
            }
        } else {
            $savedColumns = $allPossibleColumns;
        }

        $this->view('setup/column_preview', [
            'title' => 'Column Preview Setup',
            'path' => '/setup/column-preview',
            'allPossibleColumns' => $savedColumns,
            'currentTable' => $currentTable,
            'message' => $message,
            'messageType' => $messageType
        ]);
    }

    /**
     * Define column schemas for different tables.
     */
    private function getColumnDefinitions($table)
    {
        switch ($table) {
            case 'pending_customers':
                return [
                    ['key' => 'id', 'label' => 'ID', 'enabled' => true],
                    ['key' => 'full_name', 'label' => 'Name', 'enabled' => true],
                    ['key' => 'mobile_no', 'label' => 'Mobile', 'enabled' => true],
                    ['key' => 'area', 'label' => 'Area', 'enabled' => true],
                    ['key' => 'package_name', 'label' => 'Package', 'enabled' => true],
                    ['key' => 'created_at', 'label' => 'Request Date', 'enabled' => true],
                    ['key' => 'status', 'label' => 'Status', 'enabled' => true],
                    // Extras
                    ['key' => 'email', 'label' => 'Email', 'enabled' => false],
                    ['key' => 'address', 'label' => 'Address', 'enabled' => false],
                    ['key' => 'note', 'label' => 'Note', 'enabled' => false],
                ];

            case 'complain_list':
                return [
                    ['key' => 'id', 'label' => 'ID', 'enabled' => true],
                    ['key' => 'customer_info', 'label' => 'Customer Info', 'enabled' => true], // Composite of Name/Area/Mobile
                    ['key' => 'complain_title', 'label' => 'Issue', 'enabled' => true],
                    ['key' => 'description', 'label' => 'Description', 'enabled' => false],
                    ['key' => 'assigned_to', 'label' => 'Assigned To', 'enabled' => true],
                    ['key' => 'status', 'label' => 'Status', 'enabled' => true],
                    ['key' => 'created_at', 'label' => 'Date', 'enabled' => true],
                    // Context fields form customer (joins) could be added here
                    ['key' => 'mobile_no', 'label' => 'Mobile', 'enabled' => false],
                    ['key' => 'area', 'label' => 'Area', 'enabled' => false],
                ];

            case 'collection_report':
                return [
                    ['key' => 'collection_date', 'label' => 'Date', 'enabled' => true],
                    ['key' => 'payment_id', 'label' => 'Payment ID', 'enabled' => true],
                    ['key' => 'customer_id', 'label' => 'ID', 'enabled' => true],
                    ['key' => 'customer_name', 'label' => 'Customer', 'enabled' => true],
                    ['key' => 'collected_by', 'label' => 'Collected By', 'enabled' => true],
                    ['key' => 'amount', 'label' => 'Amount', 'enabled' => true],
                    ['key' => 'status', 'label' => 'Status', 'enabled' => true],
                    ['key' => 'next_expire_date', 'label' => 'Expiry Date', 'enabled' => true],
                    // Extras
                    ['key' => 'invoice_no', 'label' => 'Invoice No', 'enabled' => false],
                    ['key' => 'note', 'label' => 'Note', 'enabled' => false],
                    ['key' => 'connected_by', 'label' => 'Connected By', 'enabled' => false],
                ];

            case 'customer_summary':
                return [
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

            case 'due_list':
                return [
                    ['key' => 'id', 'label' => 'ID', 'enabled' => true],
                    ['key' => 'customer_info', 'label' => 'Customer', 'enabled' => true],
                    ['key' => 'mobile_no', 'label' => 'Mobile', 'enabled' => true],
                    ['key' => 'area', 'label' => 'Area', 'enabled' => true],
                    ['key' => 'monthly_rent', 'label' => 'Monthly Rent', 'enabled' => true],
                    ['key' => 'due_amount', 'label' => 'Due Amount', 'enabled' => true],
                ];

            case 'inactive_list':
                return [
                    ['key' => 'id', 'label' => 'ID', 'enabled' => true],
                    ['key' => 'customer_info', 'label' => 'Customer', 'enabled' => true],
                    ['key' => 'mobile_no', 'label' => 'Mobile', 'enabled' => true],
                    ['key' => 'status', 'label' => 'Status', 'enabled' => true],
                    ['key' => 'expire_date', 'label' => 'Expiry Date', 'enabled' => true],
                    ['key' => 'manual_auto_disable', 'label' => 'Manual / Auto', 'enabled' => true],
                ];

            case 'all_customers':
            default:
                return [
                    ['key' => 'id', 'label' => 'ID', 'enabled' => true],
                    ['key' => 'full_name', 'label' => 'Name', 'enabled' => true],
                    ['key' => 'mobile_no', 'label' => 'Mobile', 'enabled' => true],
                    ['key' => 'area', 'label' => 'Area', 'enabled' => true],
                    ['key' => 'package_name', 'label' => 'Package', 'enabled' => true],
                    ['key' => 'payment_id', 'label' => 'Payment ID', 'enabled' => true],
                    ['key' => 'due_amount', 'label' => 'Due', 'enabled' => true],
                    ['key' => 'status', 'label' => 'Status', 'enabled' => true],

                    // Personal
                    ['key' => 'email', 'label' => 'Email', 'enabled' => false],
                    ['key' => 'parents_name', 'label' => 'Parents Name', 'enabled' => false],
                    ['key' => 'spouse_name', 'label' => 'Spouse Name', 'enabled' => false],
                    ['key' => 'identification_no', 'label' => 'NID', 'enabled' => false],
                    ['key' => 'alt_mobile_no', 'label' => 'Alt Mobile', 'enabled' => false],
                    ['key' => 'contact_person', 'label' => 'Contact Person', 'enabled' => false],
                    ['key' => 'entry_date', 'label' => 'Entry Date', 'enabled' => false],

                    // Address
                    ['key' => 'district', 'label' => 'District', 'enabled' => false],
                    ['key' => 'thana', 'label' => 'Thana', 'enabled' => false],
                    ['key' => 'building_name', 'label' => 'Building', 'enabled' => false],
                    ['key' => 'floor', 'label' => 'Floor', 'enabled' => false],
                    ['key' => 'house_no', 'label' => 'House No', 'enabled' => false],
                    ['key' => 'tj_box', 'label' => 'TJ Box', 'enabled' => false],
                    ['key' => 'fiber_code', 'label' => 'Fiber Code', 'enabled' => false],
                    ['key' => 'onu_mac', 'label' => 'ONU MAC', 'enabled' => false],

                    // Server
                    ['key' => 'ip_address', 'label' => 'IP Address', 'enabled' => false],
                    ['key' => 'mac_address', 'label' => 'MAC Address', 'enabled' => false],
                    ['key' => 'pppoe_name', 'label' => 'PPPoE Name', 'enabled' => false],
                    ['key' => 'pppoe_password', 'label' => 'PPPoE Password', 'enabled' => false],
                    ['key' => 'pppoe_profile', 'label' => 'PPPoE Profile', 'enabled' => false],
                    ['key' => 'connection_date', 'label' => 'Conn. Date', 'enabled' => false],
                    ['key' => 'expire_date', 'label' => 'Expire Date', 'enabled' => false],

                    // Billing
                    ['key' => 'monthly_rent', 'label' => 'Monthly Rent', 'enabled' => false],
                    ['key' => 'total_amount', 'label' => 'Total Amount', 'enabled' => false],
                    ['key' => 'billing_type', 'label' => 'Billing Type', 'enabled' => false],
                    ['key' => 'vat_percent', 'label' => 'VAT %', 'enabled' => false],
                    ['key' => 'discount', 'label' => 'Discount', 'enabled' => false],

                    // Official
                    ['key' => 'client_type', 'label' => 'Client Type', 'enabled' => false],
                    ['key' => 'connectivity_type', 'label' => 'Connectivity', 'enabled' => false],
                    ['key' => 'distribution_point', 'label' => 'Dist. Point', 'enabled' => false],
                    ['key' => 'connected_by', 'label' => 'Connected By', 'enabled' => false],
                    ['key' => 'note', 'label' => 'Note', 'enabled' => false],
                ];
        }
    }
}
