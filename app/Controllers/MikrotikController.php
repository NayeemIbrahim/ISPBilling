<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

class MikrotikController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->ensureTablesExist();
    }

    private function ensureTablesExist()
    {
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS `mikrotiks` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL,
                `ip_address` VARCHAR(100) NOT NULL,
                `username` VARCHAR(100) NOT NULL,
                `password` VARCHAR(255) DEFAULT NULL,
                `ssh_port` INT DEFAULT 22,
                `api_port` INT DEFAULT 8728,
                `status` ENUM('connected', 'disconnected') DEFAULT 'disconnected',
                `auto_backup` TINYINT(1) DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");
        } catch (\Exception $e) {
            // Silently fail or log
        }
    }

    public function index()
    {
        $db = $this->db;
        
        // Fetch all Mikrotiks
        $stmt = $db->query("SELECT * FROM mikrotiks ORDER BY created_at DESC");
        $mikrotiks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // For "Compare with Mikrotik" or User counts, we could also fetch them here.
        // For now, we will mock "Total Customer", "Static", "PPPoE" with dummy data or from customers table
        // assuming a mikrotik_id in customers table in the future.
        
        // Check if mikrotik_id exists in customers table
        $hasMikrotikId = false;
        try {
            $checkStmt = $db->query("SHOW COLUMNS FROM customers LIKE 'mikrotik_id'");
            if ($checkStmt->rowCount() > 0) {
                $hasMikrotikId = true;
            }
        } catch (\Exception $e) {}

        foreach ($mikrotiks as &$mikrotik) {
            $mikrotik['total_customer'] = 0;
            $mikrotik['static_customer'] = 0;
            $mikrotik['pppoe_customer'] = 0;
            
            if ($hasMikrotikId) {
                $countStmt = $db->prepare("SELECT COUNT(*) FROM customers WHERE mikrotik_id = ?");
                $countStmt->execute([$mikrotik['id']]);
                $mikrotik['total_customer'] = $countStmt->fetchColumn();
            }
        }

        $this->view('mikrotik/index', [
            'title' => 'Mikrotik Sync',
            'path' => '/mikrotik',
            'mikrotiks' => $mikrotiks
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = $this->db;
            
            $name = $_POST['name'] ?? '';
            $ip_address = $_POST['ip_address'] ?? '';
            $ssh_port = $_POST['ssh_port'] ?? 22;
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $stmt = $db->prepare("INSERT INTO mikrotiks (name, ip_address, ssh_port, username, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $ip_address, $ssh_port, $username, $password]);
            
            header('Location: ' . url('mikrotik'));
            exit;
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = $this->db;
            
            $name = $_POST['name'] ?? '';
            $ip_address = $_POST['ip_address'] ?? '';
            $ssh_port = $_POST['ssh_port'] ?? 22;
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $stmt = $db->prepare("UPDATE mikrotiks SET name = ?, ip_address = ?, ssh_port = ?, username = ?, password = ? WHERE id = ?");
            $stmt->execute([$name, $ip_address, $ssh_port, $username, $password, $id]);
            
            header('Location: ' . url('mikrotik'));
            exit;
        }
    }

    public function delete($id)
    {
        $db = $this->db;
        $stmt = $db->prepare("DELETE FROM mikrotiks WHERE id = ?");
        $stmt->execute([$id]);
        
        header('Location: ' . url('mikrotik'));
        exit;
    }

    public function toggleStatus($id)
    {
        $db = $this->db;
        $stmt = $db->prepare("SELECT status FROM mikrotiks WHERE id = ?");
        $stmt->execute([$id]);
        $currentStatus = $stmt->fetchColumn();
        
        $newStatus = ($currentStatus === 'connected') ? 'disconnected' : 'connected';
        
        $updateStmt = $db->prepare("UPDATE mikrotiks SET status = ? WHERE id = ?");
        $updateStmt->execute([$newStatus, $id]);
        
        header('Location: ' . url('mikrotik'));
        exit;
    }
    
    public function toggleBackup($id)
    {
        $db = $this->db;
        $stmt = $db->prepare("SELECT auto_backup FROM mikrotiks WHERE id = ?");
        $stmt->execute([$id]);
        $currentBackup = $stmt->fetchColumn();
        
        $newBackup = ($currentBackup == 1) ? 0 : 1;
        
        $updateStmt = $db->prepare("UPDATE mikrotiks SET auto_backup = ? WHERE id = ?");
        $updateStmt->execute([$newBackup, $id]);
        
        header('Location: ' . url('mikrotik'));
        exit;
    }
}
