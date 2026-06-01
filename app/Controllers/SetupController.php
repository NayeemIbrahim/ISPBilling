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
        if (!$this->db) {
            die("Database connection failed. Please check your config/database.php settings.");
        }

        // 1. Auto-Migration (Fix for missing tables)
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                display_name VARCHAR(255) NOT NULL,
                username VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                role ENUM('Super Admin', 'Admin', 'Employee') DEFAULT 'Employee',
                status ENUM('pending', 'active', 'inactive') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");

            // Seed Super Admin if not exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = 'superadmin'");
            $stmt->execute();
            if (!$stmt->fetch()) {
                $hash = password_hash('superadmin', PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (display_name, username, password, email, role, status) 
                        VALUES ('Super Admin', 'superadmin', ?, 'nayeemibrahim46@gmail.com', 'Super Admin', 'active')";
                $this->db->prepare($sql)->execute([$hash]);
            }

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
        $allowedTables = ['all_customers', 'pending_customers', 'recent_customers', 'complain_list', 'collection_report', 'customer_summary', 'due_list', 'inactive_list'];
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
     * Handle Customer Form Setup.
     * 
     * @return void
     */
    public function customerForm()
    {
        $sections = $this->db->query("SELECT * FROM customer_form_sections ORDER BY order_index ASC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($sections as &$section) {
            $stmt = $this->db->prepare("
                SELECT f.*, 
                (SELECT COUNT(*) FROM customer_meta m WHERE m.field_key = f.field_key AND m.field_value IS NOT NULL AND m.field_value != '') as has_data
                FROM customer_form_fields f 
                WHERE f.section_id = ? 
                ORDER BY f.order_index ASC
            ");
            $stmt->execute([$section['id']]);
            $section['fields'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $this->view('setup/customer_form', [
            'title' => 'Customer Form Setup',
            'path' => '/setup/customer-form',
            'sections' => $sections
        ]);
    }

    /**
     * Save Customer Form Configuration.
     * 
     * @return void
     */
    public function saveCustomerForm()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['sections'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            return;
        }

        try {
            $this->db->beginTransaction();

            // Clear existing non-standard fields and all sections? 
            // Or just update. Simpler to sync by ID.

            // For now, let's just handle the updates of order and visibility.
            // If we want to support adding/deleting, we need more logic.

            foreach ($data['sections'] as $sIndex => $sData) {
                // Update or Insert section
                if (isset($sData['id']) && $sData['id'] > 0) {
                    $stmt = $this->db->prepare("UPDATE customer_form_sections SET name = ?, order_index = ? WHERE id = ?");
                    $stmt->execute([$sData['name'], $sIndex, $sData['id']]);
                    $sectionId = $sData['id'];
                } else {
                    $stmt = $this->db->prepare("INSERT INTO customer_form_sections (name, order_index) VALUES (?, ?)");
                    $stmt->execute([$sData['name'], $sIndex]);
                    $sectionId = $this->db->lastInsertId();
                }

                if (isset($sData['fields'])) {
                    foreach ($sData['fields'] as $fIndex => $fData) {
                        if (isset($fData['id']) && $fData['id'] > 0) {
                            $stmt = $this->db->prepare("UPDATE customer_form_fields SET section_id = ?, label = ?, placeholder = ?, type = ?, required = ?, is_visible = ?, order_index = ?, options = ? WHERE id = ?");
                            $stmt->execute([
                                $sectionId,
                                $fData['label'],
                                $fData['placeholder'] ?? null,
                                $fData['type'],
                                $fData['required'] ? 1 : 0,
                                $fData['is_visible'] ? 1 : 0,
                                $fIndex,
                                isset($fData['options']) ? json_encode($fData['options']) : null,
                                $fData['id']
                            ]);
                        } else {
                            $stmt = $this->db->prepare("INSERT INTO customer_form_fields (section_id, field_key, label, placeholder, type, required, is_visible, order_index, options) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->execute([
                                $sectionId,
                                (!empty($fData['field_key'])) ? $fData['field_key'] : 'custom_' . bin2hex(random_bytes(4)) . '_' . $fIndex,
                                $fData['label'],
                                $fData['placeholder'] ?? null,
                                $fData['type'],
                                $fData['required'] ? 1 : 0,
                                $fData['is_visible'] ? 1 : 0,
                                $fIndex,
                                isset($fData['options']) ? json_encode($fData['options']) : null
                            ]);
                        }
                    }
                }
            }

            // Handle deletions if IDs were provided in a 'deleted' array
            if (isset($data['deleted_sections'])) {
                foreach ($data['deleted_sections'] as $id) {
                    $this->db->prepare("DELETE FROM customer_form_sections WHERE id = ?")->execute([$id]);
                }
            }
            if (isset($data['deleted_fields'])) {
                foreach ($data['deleted_fields'] as $id) {
                    // Safety check: Don't delete if field has data
                    $stmtCheck = $this->db->prepare("SELECT field_key FROM customer_form_fields WHERE id = ?");
                    $stmtCheck->execute([$id]);
                    $fKey = $stmtCheck->fetchColumn();

                    if ($fKey) {
                        $checkMeta = $this->db->prepare("SELECT COUNT(*) FROM customer_meta WHERE field_key = ? AND field_value IS NOT NULL AND field_value != ''");
                        $checkMeta->execute([$fKey]);
                        if ($checkMeta->fetchColumn() > 0) {
                            continue; // Skip deletion if data exists
                        }
                    }
                    $this->db->prepare("DELETE FROM customer_form_fields WHERE id = ? AND is_standard = 0")->execute([$id]);
                }
            }

            $this->db->commit();
            echo json_encode(['status' => 'success']);
        } catch (\Exception $e) {
            $this->db->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Handle Print Preview Setup.
     * 
     * @return void
     */
    public function printPreview()
    {
        // 1. Auto-Migration for print_settings
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS print_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                header_style VARCHAR(50) DEFAULT 'with_header',
                layout VARCHAR(50) DEFAULT '3',
                receipt_text VARCHAR(50) DEFAULT 'Thank you for connecting with us.',
                signature_path VARCHAR(255) DEFAULT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");

            // Seed default if empty
            $count = $this->db->query("SELECT COUNT(*) FROM print_settings")->fetchColumn();
            if ($count == 0) {
                $this->db->exec("INSERT INTO print_settings (header_style, layout, receipt_text) VALUES ('with_header', '3', 'Thank you for connecting with us.')");
            }
        } catch (\Exception $e) {
            // Log error
        }

        $message = '';
        $messageType = '';

        // 2. Handle POST Request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'save_settings') {
                $header_style = $_POST['header_style'] ?? 'with_header';
                $layout = $_POST['layout'] ?? '1';
                $receipt_text = substr($_POST['receipt_text'] ?? '', 0, 50); // Enforce max 50

                $stmt = $this->db->prepare("UPDATE print_settings SET header_style = ?, layout = ?, receipt_text = ? WHERE id = 1");
                if ($stmt->execute([$header_style, $layout, $receipt_text])) {
                    $message = "Settings updated successfully.";
                    $messageType = "success";
                } else {
                    $message = "Failed to update settings.";
                    $messageType = "error";
                }
            } elseif ($action === 'upload_signature') {
                if (isset($_FILES['signature']) && $_FILES['signature']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../../../public/uploads/signatures/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileTmpPath = $_FILES['signature']['tmp_name'];
                    $fileName = $_FILES['signature']['name'];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));
                    
                    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($fileExtension, $allowedExts)) {
                        $newFileName = 'signature_' . time() . '.' . $fileExtension;
                        $destPath = $uploadDir . $newFileName;
                        
                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            // Save relative path
                            $relativePath = 'uploads/signatures/' . $newFileName;
                            $this->db->prepare("UPDATE print_settings SET signature_path = ? WHERE id = 1")->execute([$relativePath]);
                            $message = "Signature uploaded successfully.";
                            $messageType = "success";
                        } else {
                            $message = "Error moving the uploaded file.";
                            $messageType = "error";
                        }
                    } else {
                        $message = "Invalid file type. Only JPG, PNG, GIF are allowed.";
                        $messageType = "error";
                    }
                } else {
                    $message = "Please select a valid image file.";
                    $messageType = "error";
                }
            }
        }

        // 3. Fetch Settings
        $settings = $this->db->query("SELECT * FROM print_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

        // 4. Fetch a sample customer for the live preview
        $previewCustomer = $this->db->query("SELECT * FROM customers ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

        $this->view('setup/print_preview', [
            'title' => 'Print Preview Setup',
            'path' => '/setup/print-preview',
            'settings' => $settings,
            'previewCustomer' => $previewCustomer,
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
            case 'recent_customers':
                return [
                    ['key' => 'id', 'label' => 'ID', 'enabled' => true],
                    ['key' => 'full_name', 'label' => 'Name', 'enabled' => true],
                    ['key' => 'mobile_no', 'label' => 'Mobile', 'enabled' => true],
                    ['key' => 'area', 'label' => 'Area', 'enabled' => true],
                    ['key' => 'package_name', 'label' => 'Package', 'enabled' => true],
                    ['key' => 'payment_id', 'label' => 'Payment ID', 'enabled' => true],
                    ['key' => 'due_amount', 'label' => 'Due', 'enabled' => true],
                    ['key' => 'status', 'label' => 'Status', 'enabled' => true],
                    ['key' => 'created_at', 'label' => 'Date Added', 'enabled' => true],

                    // Personal
                    ['key' => 'email', 'label' => 'Email', 'enabled' => false],
                    ['key' => 'parents_name', 'label' => 'Parents Name', 'enabled' => false],
                    ['key' => 'spouse_name', 'label' => 'Spouse Name', 'enabled' => false],
                    ['key' => 'identification_no', 'label' => 'NID', 'enabled' => false],
                    ['key' => 'alt_mobile_no', 'label' => 'Alt Mobile', 'enabled' => false],
                    ['key' => 'contact_person', 'label' => 'Contact Person', 'enabled' => false],

                    // Address
                    ['key' => 'district', 'label' => 'District', 'enabled' => false],
                    ['key' => 'thana', 'label' => 'Thana', 'enabled' => false],
                    ['key' => 'building_name', 'label' => 'Building', 'enabled' => false],
                    ['key' => 'floor', 'label' => 'Floor', 'enabled' => false],
                    ['key' => 'house_no', 'label' => 'House No', 'enabled' => false],
                    ['key' => 'tj_box', 'label' => 'TJ Box', 'enabled' => false],
                    ['key' => 'fiber_code', 'label' => 'Fiber Code', 'enabled' => false],
                    ['key' => 'onu_mac', 'label' => 'ONU MAC', 'enabled' => false],

                    // Other
                    ['key' => 'ip_address', 'label' => 'IP Address', 'enabled' => false],
                    ['key' => 'mac_address', 'label' => 'MAC Address', 'enabled' => false],
                    ['key' => 'pppoe_name', 'label' => 'PPPoE Name', 'enabled' => false],
                    ['key' => 'connection_date', 'label' => 'Conn. Date', 'enabled' => false],
                    ['key' => 'expire_date', 'label' => 'Expiry Date', 'enabled' => false],
                    ['key' => 'monthly_rent', 'label' => 'Monthly Rent', 'enabled' => false],
                    ['key' => 'total_amount', 'label' => 'Total Amount', 'enabled' => false],
                    ['key' => 'billing_type', 'label' => 'Billing Type', 'enabled' => false],
                    ['key' => 'discount', 'label' => 'Discount', 'enabled' => false],
                    ['key' => 'advance_amount', 'label' => 'Advance', 'enabled' => false],
                    ['key' => 'additional_charge', 'label' => 'Additional Charge', 'enabled' => false],
                    ['key' => 'vat_percent', 'label' => 'VAT %', 'enabled' => false],
                    ['key' => 'security_deposit', 'label' => 'Security Deposit', 'enabled' => false],
                    ['key' => 'connected_by', 'label' => 'Connected By', 'enabled' => false],
                    ['key' => 'note', 'label' => 'Note', 'enabled' => false],
                ];

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

                    // Billing Extras
                    ['key' => 'billing_type', 'label' => 'Billing Type', 'enabled' => false],
                    ['key' => 'monthly_rent', 'label' => 'Monthly Rent', 'enabled' => false],
                    ['key' => 'due_amount', 'label' => 'Due Amount', 'enabled' => false],
                    ['key' => 'discount', 'label' => 'Discount', 'enabled' => false],
                    ['key' => 'advance_amount', 'label' => 'Advance', 'enabled' => false],
                    ['key' => 'additional_charge', 'label' => 'Additional Charge', 'enabled' => false],
                    ['key' => 'vat_percent', 'label' => 'VAT %', 'enabled' => false],
                    ['key' => 'security_deposit', 'label' => 'Security Deposit', 'enabled' => false],
                    ['key' => 'total_amount', 'label' => 'Total Amount', 'enabled' => false],
                    ['key' => 'expire_date', 'label' => 'Expiry Date', 'enabled' => false],
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
                    ['key' => 'advance_amount', 'label' => 'Advance', 'enabled' => false],
                    ['key' => 'additional_charge', 'label' => 'Additional Charge', 'enabled' => false],
                    ['key' => 'security_deposit', 'label' => 'Security Deposit', 'enabled' => false],

                    // Official
                    ['key' => 'client_type', 'label' => 'Client Type', 'enabled' => false],
                    ['key' => 'connectivity_type', 'label' => 'Connectivity', 'enabled' => false],
                    ['key' => 'distribution_point', 'label' => 'Dist. Point', 'enabled' => false],
                    ['key' => 'connected_by', 'label' => 'Connected By', 'enabled' => false],
                    ['key' => 'note', 'label' => 'Note', 'enabled' => false],
                ];
        }
    }

    /**
     * Private helper to execute payment migrations on-the-fly.
     */
    private function paymentSettingsMigrationCheck()
    {
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS payment_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                gateway_name VARCHAR(50) NOT NULL UNIQUE,
                receive_number VARCHAR(20) NOT NULL,
                status TINYINT(1) DEFAULT 1,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");

            $count = $this->db->query("SELECT COUNT(*) FROM payment_settings")->fetchColumn();
            if ($count == 0) {
                $this->db->exec("INSERT INTO payment_settings (gateway_name, receive_number, status) VALUES 
                    ('bkash', '01700000000', 1),
                    ('nagad', '01800000000', 1),
                    ('rocket', '01900000000', 1)
                ");
            }

            $this->db->exec("CREATE TABLE IF NOT EXISTS auto_payment_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                gateway VARCHAR(50) NOT NULL,
                sender_number VARCHAR(20) NOT NULL,
                receiver_number VARCHAR(20) NOT NULL,
                amount DECIMAL(10, 2) NOT NULL,
                trx_id VARCHAR(100) NOT NULL,
                reference VARCHAR(100) DEFAULT NULL,
                status ENUM('success', 'unmatched', 'error') DEFAULT 'unmatched',
                customer_id INT NULL,
                error_message TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
            )");
        } catch (\Exception $e) {
            // Silently handle
        }
    }

    /**
     * General settings page for bKash, Nagad, Rocket receive numbers and logged transaction histories.
     */
    public function paymentSettings()
    {
        // 1. Run Migrations
        $this->paymentSettingsMigrationCheck();

        $message = '';
        $messageType = '';

        // 2. Handle POST Settings update
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bkash_number = $_POST['bkash_number'] ?? '';
            $bkash_status = isset($_POST['bkash_status']) ? 1 : 0;

            $nagad_number = $_POST['nagad_number'] ?? '';
            $nagad_status = isset($_POST['nagad_status']) ? 1 : 0;

            $rocket_number = $_POST['rocket_number'] ?? '';
            $rocket_status = isset($_POST['rocket_status']) ? 1 : 0;

            try {
                $stmt = $this->db->prepare("UPDATE payment_settings SET receive_number = ?, status = ? WHERE gateway_name = ?");
                $stmt->execute([$bkash_number, $bkash_status, 'bkash']);
                $stmt->execute([$nagad_number, $nagad_status, 'nagad']);
                $stmt->execute([$rocket_number, $rocket_status, 'rocket']);

                $message = "Payment Settings updated successfully!";
                $messageType = "success";
            } catch (\PDOException $e) {
                $message = "Failed to save settings: " . $e->getMessage();
                $messageType = "error";
            }
        }

        // 3. Fetch Settings
        $settings = [];
        $rows = $this->db->query("SELECT * FROM payment_settings")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $settings[$row['gateway_name']] = $row;
        }

        // 4. Fetch Logs with Resolved Customer details
        $logs = $this->db->query("
            SELECT l.*, c.full_name as customer_name 
            FROM auto_payment_logs l 
            LEFT JOIN customers c ON l.customer_id = c.id 
            ORDER BY l.id DESC 
            LIMIT 100
        ")->fetchAll(PDO::FETCH_ASSOC);

        // 5. Render View
        $this->view('setup/payment_settings', [
            'title' => 'Payment Settings Setup',
            'path' => '/setup/payment-settings',
            'settings' => $settings,
            'logs' => $logs,
            'message' => $message,
            'messageType' => $messageType
        ]);
    }

    /**
     * Webhook Endpoint for Automatic Payment Callbacks (bKash, Nagad, Rocket)
     */
    public function mfsCallback()
    {
        header('Content-Type: application/json');

        // 1. Get raw input data (for JSON payloads)
        $rawInput = file_get_contents('php://input');
        $jsonData = json_decode($rawInput, true);

        // Parse fields from JSON or standard POST
        $gateway = trim(strtolower($jsonData['gateway'] ?? $_POST['gateway'] ?? ''));
        $sender = trim($jsonData['sender'] ?? $_POST['sender'] ?? '');
        $receiver = trim($jsonData['receiver'] ?? $_POST['receiver'] ?? '');
        $amount = floatval($jsonData['amount'] ?? $_POST['amount'] ?? 0);
        $trx_id = trim($jsonData['trx_id'] ?? $_POST['trx_id'] ?? '');
        $reference = trim($jsonData['reference'] ?? $_POST['reference'] ?? '');

        // 2. Validate essential fields
        if (empty($gateway) || empty($trx_id) || $amount <= 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid transaction payload. Missing gateway, trx_id, or valid amount.'
            ]);
            return;
        }

        // 3. Ensure tables exist
        $this->paymentSettingsMigrationCheck();

        // 4. Validate that the receiver number matches our settings and that the gateway is active
        $stmt = $this->db->prepare("SELECT * FROM payment_settings WHERE gateway_name = ?");
        $stmt->execute([$gateway]);
        $gatewaySetting = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$gatewaySetting) {
            echo json_encode([
                'status' => 'error',
                'message' => "Gateway '$gateway' is not supported."
            ]);
            return;
        }

        if (intval($gatewaySetting['status']) !== 1) {
            // Log transaction attempt for audit, but mark as error due to inactive gateway
            $insLog = $this->db->prepare("INSERT INTO auto_payment_logs (gateway, sender_number, receiver_number, amount, trx_id, reference, status, error_message) VALUES (?, ?, ?, ?, ?, ?, 'error', ?)");
            $insLog->execute([$gateway, $sender, $receiver, $amount, $trx_id, $reference, "Gateway channel is currently disabled."]);
            
            echo json_encode([
                'status' => 'error',
                'message' => "Gateway '$gateway' is currently disabled in settings."
            ]);
            return;
        }

        // Check if TrxID has already been processed to prevent double crediting
        $chkTrx = $this->db->prepare("SELECT id FROM collections WHERE invoice_no = ?");
        $chkTrx->execute([$trx_id]);
        if ($chkTrx->fetch()) {
            echo json_encode([
                'status' => 'error',
                'message' => "Duplicate Transaction. TrxID '$trx_id' has already been processed."
            ]);
            return;
        }

        // Check logs to make sure this trx_id isn't already logged as success
        $chkLog = $this->db->prepare("SELECT id FROM auto_payment_logs WHERE trx_id = ? AND status = 'success'");
        $chkLog->execute([$trx_id]);
        if ($chkLog->fetch()) {
            echo json_encode([
                'status' => 'error',
                'message' => "Duplicate Transaction. TrxID '$trx_id' was previously successfully processed."
            ]);
            return;
        }

        // 5. Heuristic Sequential Customer Matcher (User priority order)
        $customer = null;
        $matchMethod = '';

        // Match Step 1: Match by exact Payment ID (Gateway ID) in the Reference
        if (!empty($reference)) {
            $stmt = $this->db->prepare("
                SELECT c.*, p.name as package_name, ip.prefix_code 
                FROM customers c 
                LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id 
                LEFT JOIN packages p ON c.package_id = p.id 
                WHERE c.payment_id = ?
            ");
            $stmt->execute([$reference]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($customer) {
                $matchMethod = "Payment ID: " . $reference;
            }
        }

        // Match Step 2: Match by Customer ID in the Reference (parsed or raw)
        if (!$customer && !empty($reference)) {
            // Look up all prefixes from the system
            $prefixes = $this->db->query("SELECT prefix_code FROM id_prefixes")->fetchAll(PDO::FETCH_COLUMN);
            $parsedId = $reference;
            foreach ($prefixes as $p) {
                $pClean = trim($p, '_- ');
                if (!empty($pClean) && stripos($reference, $pClean) === 0) {
                    $parsedId = substr($reference, strlen($pClean));
                    $parsedId = trim($parsedId, '_- ');
                    break;
                }
            }

            if (is_numeric($parsedId)) {
                $stmt = $this->db->prepare("
                    SELECT c.*, p.name as package_name, ip.prefix_code 
                    FROM customers c 
                    LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id 
                    LEFT JOIN packages p ON c.package_id = p.id 
                    WHERE c.id = ?
                ");
                $stmt->execute([$parsedId]);
                $customer = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($customer) {
                    $matchMethod = "Customer ID: " . $parsedId;
                }
            }
        }

        // Match Step 3: Match by PPPoE Username in the Reference
        if (!$customer && !empty($reference)) {
            $stmt = $this->db->prepare("
                SELECT c.*, p.name as package_name, ip.prefix_code 
                FROM customers c 
                LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id 
                LEFT JOIN packages p ON c.package_id = p.id 
                WHERE LOWER(c.pppoe_name) = ?
            ");
            $stmt->execute([strtolower($reference)]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($customer) {
                $matchMethod = "PPPoE Username: " . $reference;
            }
        }

        // Match Step 4: Match by Mobile Numbers of the Customer (using Reference or Sender number)
        if (!$customer) {
            $phonesToTry = array_filter(array_unique([$reference, $sender]));
            foreach ($phonesToTry as $phone) {
                if (empty($phone) || strlen($phone) < 8) continue;
                
                $cleanPhone = $phone;
                if (strpos($phone, '88') === 0) {
                    $cleanPhone = substr($phone, 2);
                }

                $stmt = $this->db->prepare("
                    SELECT c.*, p.name as package_name, ip.prefix_code 
                    FROM customers c 
                    LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id 
                    LEFT JOIN packages p ON c.package_id = p.id 
                    WHERE c.mobile_no = ? OR c.alt_mobile_no = ? OR c.mobile_no = ? OR c.alt_mobile_no = ?
                ");
                $stmt->execute([$phone, $phone, $cleanPhone, $cleanPhone]);
                $customer = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($customer) {
                    $matchMethod = "Mobile Match (" . $phone . ")";
                    break;
                }
            }
        }

        // 6. Execute billing update if customer matched
        if ($customer) {
            try {
                $this->db->beginTransaction();

                $customer_id = $customer['id'];
                $monthlyRent = floatval($customer['monthly_rent']);
                $currentExpireStr = $customer['expire_date'];
                $autoDisableMonth = intval($customer['auto_disable_month'] ?? 0);
                $extraDays = intval($customer['extra_days'] ?? 0);

                // Calculate expiry date:
                // Rule: If paid amount < monthly rent, don't extend expiry, just reduce due
                $baseDate = !empty($currentExpireStr) ? new \DateTime($currentExpireStr) : new \DateTime();
                
                if ($amount >= $monthlyRent && $monthlyRent > 0) {
                    $monthsToAdd = floor($amount / $monthlyRent);
                    $baseDate->modify("+$monthsToAdd month");

                    if ($autoDisableMonth > 0) {
                        $baseDate->modify("+$autoDisableMonth month");
                    }
                    if ($extraDays > 0) {
                        $baseDate->modify("+$extraDays day");
                    }
                }
                $next_expire_date = $baseDate->format('Y-m-d');

                // 6.1 Insert into collections table
                $note = "Auto Paid via " . ucfirst($gateway) . ". Sender: $sender, Ref: $reference (Matched via $matchMethod)";
                $colSql = "INSERT INTO collections (customer_id, amount, payment_method, invoice_no, next_expire_date, note, collected_by) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
                $colStmt = $this->db->prepare($colSql);
                // Mark collected_by as NULL (processed automatically)
                $colStmt->execute([$customer_id, $amount, ucfirst($gateway) . ' (Auto)', $trx_id, $next_expire_date, $note, null]);

                // 6.2 Update customer's balance, expire date, and status to active
                $updSql = "UPDATE customers SET 
                            due_amount = due_amount - ?, 
                            expire_date = ?, 
                            status = 'active' 
                           WHERE id = ?";
                $updStmt = $this->db->prepare($updSql);
                $updStmt->execute([$amount, $next_expire_date, $customer_id]);

                // 6.3 Log success in auto_payment_logs
                $logSql = "INSERT INTO auto_payment_logs (gateway, sender_number, receiver_number, amount, trx_id, reference, status, customer_id) 
                           VALUES (?, ?, ?, ?, ?, ?, 'success', ?)";
                $this->db->prepare($logSql)->execute([$gateway, $sender, $receiver, $amount, $trx_id, $reference, $customer_id]);

                $this->db->commit();

                // Format response
                echo json_encode([
                    'status' => 'success',
                    'message' => "Payment of $amount TK successfully credited to customer '{$customer['full_name']}'!",
                    'customer_name' => $customer['full_name'],
                    'monthly_rent' => $monthlyRent,
                    'new_due' => floatval($customer['due_amount']) - $amount,
                    'new_expiry' => $next_expire_date
                ]);

            } catch (\Exception $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }

                // Log error
                $logSql = "INSERT INTO auto_payment_logs (gateway, sender_number, receiver_number, amount, trx_id, reference, status, customer_id, error_message) 
                           VALUES (?, ?, ?, ?, ?, ?, 'error', ?, ?)";
                $this->db->prepare($logSql)->execute([$gateway, $sender, $receiver, $amount, $trx_id, $reference, $customer['id'], $e->getMessage()]);

                echo json_encode([
                    'status' => 'error',
                    'message' => 'Exception during payment processing: ' . $e->getMessage()
                ]);
            }

        } else {
            // No matching customer found (Unmatched)
            $logSql = "INSERT INTO auto_payment_logs (gateway, sender_number, receiver_number, amount, trx_id, reference, status, error_message) 
                       VALUES (?, ?, ?, ?, ?, ?, 'unmatched', ?)";
            $this->db->prepare($logSql)->execute([$gateway, $sender, $receiver, $amount, $trx_id, $reference, "Could not match reference '$reference' or sender '$sender' to any customer."]);

            echo json_encode([
                'status' => 'unmatched',
                'message' => "Payment of $amount TK logged. However, no active customer matched the reference '$reference' or sender '$sender'."
            ]);
        }
    }
}

