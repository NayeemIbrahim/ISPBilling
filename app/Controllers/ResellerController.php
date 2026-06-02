<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

class ResellerController extends Controller
{
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->ensureTablesExist();
    }

    private function ensureTablesExist()
    {
        try {
            // 1. Reseller Packages Table
            $this->db->exec("CREATE TABLE IF NOT EXISTS `reseller_packages` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL,
                `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `reseller_id` INT NULL,
                `mikrotik_id` INT NULL,
                `mikrotik_profile` VARCHAR(255) DEFAULT NULL,
                `sort` INT DEFAULT 0,
                `description` TEXT,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");

            // Auto-alter if columns were missing from a previous execution
            try {
                $cols = $this->db->query("DESCRIBE reseller_packages")->fetchAll(PDO::FETCH_COLUMN);
                if (!in_array('reseller_id', $cols)) {
                    $this->db->exec("ALTER TABLE reseller_packages ADD COLUMN reseller_id INT NULL AFTER price");
                }
                if (!in_array('mikrotik_id', $cols)) {
                    $this->db->exec("ALTER TABLE reseller_packages ADD COLUMN mikrotik_id INT NULL AFTER reseller_id");
                }
                if (!in_array('sort', $cols)) {
                    $this->db->exec("ALTER TABLE reseller_packages ADD COLUMN sort INT DEFAULT 0 AFTER mikrotik_profile");
                }
            } catch (\Exception $e) {}

            // 2. Resellers Table
            $this->db->exec("CREATE TABLE IF NOT EXISTS `resellers` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `company_name` VARCHAR(255) NOT NULL,
                `contact_person` VARCHAR(255) DEFAULT NULL,
                `phone` VARCHAR(50) DEFAULT NULL,
                `email` VARCHAR(100) DEFAULT NULL,
                `address` TEXT,
                `balance` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `auto_deduct` TINYINT(1) DEFAULT 0,
                `enable_with_payment` TINYINT(1) DEFAULT 0,
                `advance_payment` TINYINT(1) DEFAULT 0,
                `mikrotik_id` INT DEFAULT NULL,
                `pppoe_prefix` VARCHAR(50) DEFAULT NULL,
                `child_percentage` DECIMAL(5,2) DEFAULT 0.00,
                `note` TEXT,
                `drive_link` VARCHAR(255) DEFAULT NULL,
                `status` ENUM('active', 'inactive') DEFAULT 'active',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");

            // Auto-alter resellers table for new fields
            try {
                $cols = $this->db->query("DESCRIBE resellers")->fetchAll(PDO::FETCH_COLUMN);
                
                $newCols = [
                    'web_address' => 'VARCHAR(255) DEFAULT NULL AFTER email',
                    'password' => 'VARCHAR(255) DEFAULT NULL AFTER web_address',
                    'country' => 'VARCHAR(100) DEFAULT NULL AFTER password',
                    'district' => 'VARCHAR(100) DEFAULT NULL AFTER country',
                    'thana' => 'VARCHAR(100) DEFAULT NULL AFTER district',
                    'area_1' => 'VARCHAR(255) DEFAULT NULL AFTER thana',
                    'area_2' => 'VARCHAR(255) DEFAULT NULL AFTER area_1',
                    'area_3' => 'VARCHAR(255) DEFAULT NULL AFTER area_2'
                ];

                foreach ($newCols as $col => $def) {
                    if (!in_array($col, $cols)) {
                        $this->db->exec("ALTER TABLE resellers ADD COLUMN $col $def");
                    }
                }
            } catch (\Exception $e) {}

            // 3. Reseller Transactions Table
            $this->db->exec("CREATE TABLE IF NOT EXISTS `reseller_transactions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `reseller_id` INT NOT NULL,
                `type` ENUM('deposit', 'withdraw', 'deduct', 'package_charge') NOT NULL,
                `amount` DECIMAL(10,2) NOT NULL,
                `balance_after` DECIMAL(10,2) NOT NULL,
                `note` TEXT,
                `date` DATE NOT NULL,
                `created_by` INT DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`reseller_id`) REFERENCES `resellers`(`id`) ON DELETE CASCADE
            )");

            // 4. Alter Customers table (Ignore errors if column already exists)
            try {
                $cols = $this->db->query("DESCRIBE customers")->fetchAll(PDO::FETCH_COLUMN);
                if (!in_array('reseller_id', $cols)) {
                    $this->db->exec("ALTER TABLE customers ADD COLUMN reseller_id INT NULL AFTER id");
                    // Add foreign key constraint separately to avoid some syntax errors
                    $this->db->exec("ALTER TABLE customers ADD CONSTRAINT fk_customer_reseller FOREIGN KEY (reseller_id) REFERENCES resellers(id) ON DELETE SET NULL");
                }
            } catch (\Exception $e) {
                // Ignore if it fails due to constraint existing
            }

        } catch (\Exception $e) {
            // Log silently or ignore
        }
    }

    public function index()
    {
        $search = $_GET['q'] ?? '';
        $whereSql = "";
        $params = [];

        if ($search) {
            $whereSql = "WHERE r.company_name LIKE ? OR r.phone LIKE ? OR r.email LIKE ? OR r.contact_person LIKE ?";
            $term = "%" . $search . "%";
            $params = [$term, $term, $term, $term];
        }

        $sql = "SELECT r.*,
                (SELECT COUNT(*) FROM customers c WHERE c.reseller_id = r.id AND c.status = 'active') as active_customers,
                (SELECT COUNT(*) FROM customers c WHERE c.reseller_id = r.id AND c.status = 'inactive') as disabled_customers,
                (SELECT COUNT(*) FROM customers c WHERE c.reseller_id = r.id) as total_customers
                FROM resellers r 
                $whereSql
                ORDER BY r.id DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $resellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Reseller List',
            'path' => '/reseller',
            'resellers' => $resellers,
            'searchQuery' => $search
        ];
        
        $this->view('reseller/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Reseller',
            'path' => '/reseller',
        ];
        
        $this->view('reseller/create', $data);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                if ($password !== $confirmPassword) {
                    die("Passwords do not match.");
                }
                
                // Hash the password securely
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO resellers (
                            company_name, address, contact_person, phone, web_address, email, 
                            password, country, district, thana, area_1, area_2, area_3
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $_POST['company_name'] ?? '',
                    $_POST['company_address'] ?? '',
                    $_POST['owner_name'] ?? '',
                    $_POST['mobile_no'] ?? '',
                    $_POST['web_address'] ?? '',
                    $_POST['email'] ?? '',
                    $hashedPassword,
                    $_POST['country'] ?? 'Bangladesh',
                    $_POST['district'] ?? '',
                    $_POST['thana'] ?? '',
                    $_POST['area_1'] ?? '',
                    $_POST['area_2'] ?? '',
                    $_POST['area_3'] ?? ''
                ]);
                
                header('Location: ' . url('reseller'));
                exit;
            } catch (\Exception $e) {
                die("Error saving reseller: " . $e->getMessage());
            }
        }
    }

    public function edit($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM resellers WHERE id = ?");
        $stmt->execute([$id]);
        $reseller = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reseller) {
            die("Reseller not found");
        }

        $data = [
            'title' => 'Edit Reseller',
            'path' => '/reseller',
            'reseller' => $reseller
        ];
        
        $this->view('reseller/edit', $data);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $sql = "UPDATE resellers SET 
                            company_name = ?, contact_person = ?, phone = ?, email = ?, address = ?, 
                            web_address = ?, country = ?, district = ?, thana = ?, area_1 = ?, area_2 = ?, area_3 = ?,
                            mikrotik_id = ?, pppoe_prefix = ?, child_percentage = ?, balance = ?, drive_link = ?, 
                            auto_deduct = ?, enable_with_payment = ?, advance_payment = ?
                        WHERE id = ?";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $_POST['company_name'] ?? '',
                    $_POST['owner_name'] ?? '',
                    $_POST['mobile_no'] ?? '',
                    $_POST['email'] ?? '',
                    $_POST['company_address'] ?? '',
                    $_POST['web_address'] ?? '',
                    $_POST['country'] ?? '',
                    $_POST['district'] ?? '',
                    $_POST['thana'] ?? '',
                    $_POST['area_1'] ?? '',
                    $_POST['area_2'] ?? '',
                    $_POST['area_3'] ?? '',
                    $_POST['mikrotik_profile'] === 'profile_1' ? 1 : ($_POST['mikrotik_profile'] === 'profile_2' ? 2 : null),
                    $_POST['pppoe_prefix'] ?? '',
                    (float)($_POST['child_percentage'] ?? 0),
                    (float)($_POST['balance'] ?? 0),
                    $_POST['drive_link'] ?? '',
                    isset($_POST['auto_deduct']) ? 1 : 0,
                    isset($_POST['enable_with_payment']) ? 1 : 0,
                    isset($_POST['advance_payment']) ? 1 : 0,
                    $id
                ]);
                
                header('Location: ' . url('reseller'));
                exit;
            } catch (\Exception $e) {
                die("Error updating reseller: " . $e->getMessage());
            }
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $stmt = $this->db->prepare("DELETE FROM resellers WHERE id = ?");
                $stmt->execute([$id]);
                header('Location: ' . url('reseller'));
                exit;
            } catch (\Exception $e) {
                die("Error deleting reseller: " . $e->getMessage());
            }
        }
    }

    public function transaction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $resellerId = $_POST['reseller_id'];
                $type = $_POST['type']; // 'deposit', 'withdraw', 'deduct'
                $amount = (float)$_POST['amount'];
                $note = $_POST['note'] ?? '';

                if ($amount <= 0) {
                    die("Amount must be greater than zero.");
                }

                $this->db->beginTransaction();

                // Get current balance
                $stmt = $this->db->prepare("SELECT balance FROM resellers WHERE id = ? FOR UPDATE");
                $stmt->execute([$resellerId]);
                $reseller = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$reseller) {
                    throw new \Exception("Reseller not found.");
                }

                $currentBalance = (float)$reseller['balance'];
                $newBalance = $currentBalance;

                if ($type === 'deposit') {
                    $newBalance += $amount;
                } elseif ($type === 'withdraw' || $type === 'deduct') {
                    $newBalance -= $amount;
                } else {
                    throw new \Exception("Invalid transaction type.");
                }

                // Update reseller balance
                $updateStmt = $this->db->prepare("UPDATE resellers SET balance = ? WHERE id = ?");
                $updateStmt->execute([$newBalance, $resellerId]);

                // Record transaction
                $transSql = "INSERT INTO reseller_transactions (reseller_id, type, amount, balance_after, note, date, created_by) 
                             VALUES (?, ?, ?, ?, ?, CURDATE(), ?)";
                $transStmt = $this->db->prepare($transSql);
                $userId = $_SESSION['user_id'] ?? null;
                $transStmt->execute([$resellerId, $type, $amount, $newBalance, $note, $userId]);

                $this->db->commit();
                
                header('Location: ' . url('reseller'));
                exit;
            } catch (\Exception $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
                die("Error processing transaction: " . $e->getMessage());
            }
        }
    }

    public function package()
    {
        $sql = "SELECT rp.*, r.company_name as reseller_name 
                FROM reseller_packages rp 
                LEFT JOIN resellers r ON rp.reseller_id = r.id 
                ORDER BY rp.sort ASC, rp.id DESC";
        $stmt = $this->db->query($sql);
        $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resellers = $this->db->query("SELECT id, company_name FROM resellers ORDER BY company_name ASC")->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Reseller Package',
            'path' => '/reseller/package',
            'packages' => $packages,
            'resellers' => $resellers
        ];
        
        $this->view('reseller/package', $data);
    }

    public function storePackage()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $sql = "INSERT INTO reseller_packages (name, price, reseller_id, mikrotik_id, mikrotik_profile, sort) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $_POST['name'] ?? '',
                    (float)($_POST['price'] ?? 0),
                    $_POST['reseller_id'] ? (int)$_POST['reseller_id'] : null,
                    $_POST['mikrotik_id'] ? (int)$_POST['mikrotik_id'] : null,
                    $_POST['mikrotik_profile'] ?? '',
                    (int)($_POST['sort'] ?? 0)
                ]);
                
                header('Location: ' . url('reseller/package'));
                exit;
            } catch (\Exception $e) {
                die("Error saving package: " . $e->getMessage());
            }
        }
    }

    public function updatePackage($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $sql = "UPDATE reseller_packages SET 
                            name = ?, price = ?, reseller_id = ?, mikrotik_id = ?, mikrotik_profile = ?, sort = ?
                        WHERE id = ?";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $_POST['name'] ?? '',
                    (float)($_POST['price'] ?? 0),
                    $_POST['reseller_id'] ? (int)$_POST['reseller_id'] : null,
                    $_POST['mikrotik_id'] ? (int)$_POST['mikrotik_id'] : null,
                    $_POST['mikrotik_profile'] ?? '',
                    (int)($_POST['sort'] ?? 0),
                    $id
                ]);
                
                header('Location: ' . url('reseller/package'));
                exit;
            } catch (\Exception $e) {
                die("Error updating package: " . $e->getMessage());
            }
        }
    }

    public function deletePackage($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $stmt = $this->db->prepare("DELETE FROM reseller_packages WHERE id = ?");
                $stmt->execute([$id]);
                header('Location: ' . url('reseller/package'));
                exit;
            } catch (\Exception $e) {
                die("Error deleting package: " . $e->getMessage());
            }
        }
    }

    public function balance()
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $resellerId = $_GET['reseller_id'] ?? '';

        $params = [$startDate, $endDate];
        $whereSql = "WHERE rt.date BETWEEN ? AND ?";

        if ($resellerId) {
            $whereSql .= " AND rt.reseller_id = ?";
            $params[] = $resellerId;
        }

        $sql = "SELECT rt.*, r.company_name as reseller_name, u.display_name as user_name 
                FROM reseller_transactions rt
                LEFT JOIN resellers r ON rt.reseller_id = r.id
                LEFT JOIN users u ON rt.created_by = u.id
                $whereSql
                ORDER BY rt.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resellers = $this->db->query("SELECT id, company_name FROM resellers ORDER BY company_name ASC")->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Reseller Balance',
            'path' => '/reseller/balance',
            'transactions' => $transactions,
            'resellers' => $resellers,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedReseller' => $resellerId
        ];
        
        $this->view('reseller/balance', $data);
    }

    public function balanceSummary()
    {
        $sql = "SELECT id, company_name, contact_person, phone, balance 
                FROM resellers 
                ORDER BY balance DESC";
        $stmt = $this->db->query($sql);
        $resellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Reseller Balance Summary',
            'path' => '/reseller/balance-summary',
            'resellers' => $resellers
        ];
        
        $this->view('reseller/balance_summary', $data);
    }
}
