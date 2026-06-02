<?php

namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

class SmsController extends Controller
{
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->ensureTablesExist();
    }

    private function ensureTablesExist()
    {
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS `sms_settings` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `provider_name` VARCHAR(100) DEFAULT 'default',
                `api_url` VARCHAR(255) DEFAULT NULL,
                `api_key` VARCHAR(255) DEFAULT NULL,
                `sender_id` VARCHAR(100) DEFAULT NULL,
                `client_id` VARCHAR(100) DEFAULT NULL,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");

            $this->db->exec("CREATE TABLE IF NOT EXISTS `sms_templates` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `event_name` VARCHAR(100) NOT NULL UNIQUE,
                `message` TEXT,
                `is_active` TINYINT(1) DEFAULT 0,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");

            // Ensure at least one row exists in settings
            $stmt = $this->db->query("SELECT COUNT(*) FROM sms_settings");
            if ($stmt->fetchColumn() == 0) {
                $this->db->exec("INSERT INTO sms_settings (provider_name) VALUES ('default')");
            }
            
            // Seed templates
            $events = [
                'All Customer', 'Area Wise Customer Due List', 'Area Wise Customer List', 
                'Auto Temporary Disable Alert', 'Bill Generate', 'Collection', 
                'Collection (MFS) to Owner', 'Collection Delete', 'Collection Edit', 
                'Collection to Owner', 'Complain Employee', 'Complain List', 
                'Complain to Customer', 'Create Customer', 'Create Customer to Owner', 
                'Free Customer List', 'Inactive Customer List', 'Failed to Disable at Mikrotik', 
                'Temporary Disable Customer List', 'Reminder'
            ];
            
            $stmt = $this->db->prepare("INSERT IGNORE INTO sms_templates (event_name, is_active) VALUES (?, 0)");
            foreach ($events as $event) {
                $stmt->execute([$event]);
            }
        } catch (\Exception $e) {}
    }

    public function index()
    {
        $stmt = $this->db->query("SELECT * FROM sms_settings LIMIT 1");
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->db->query("SELECT * FROM sms_templates ORDER BY id ASC");
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'SMS Setup',
            'path' => '/sms',
            'settings' => $settings,
            'templates' => $templates
        ];
        
        $this->view('sms/setup', $data);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // If API settings are submitted
                if (isset($_POST['api_url'])) {
                    $sql = "UPDATE sms_settings SET 
                                api_url = ?, api_key = ?, sender_id = ?, client_id = ?
                            WHERE id = (SELECT id FROM (SELECT id FROM sms_settings LIMIT 1) AS t)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        $_POST['api_url'] ?? '',
                        $_POST['api_key'] ?? '',
                        $_POST['sender_id'] ?? '',
                        $_POST['client_id'] ?? ''
                    ]);
                }
                
                // If template toggles are submitted
                if (isset($_POST['update_toggles'])) {
                    $this->db->exec("UPDATE sms_templates SET is_active = 0");
                    if (!empty($_POST['settings']) && is_array($_POST['settings'])) {
                        $placeholders = str_repeat('?,', count($_POST['settings']) - 1) . '?';
                        $stmt = $this->db->prepare("UPDATE sms_templates SET is_active = 1 WHERE event_name IN ($placeholders)");
                        $stmt->execute($_POST['settings']);
                    }
                }
                
                header('Location: ' . url('sms?success=1'));
                exit;
            } catch (\Exception $e) {
                die("Error updating SMS settings: " . $e->getMessage());
            }
        }
    }

    public function editTemplate($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM sms_templates WHERE id = ?");
        $stmt->execute([$id]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($template);
        exit;
    }

    public function saveTemplate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $stmt = $this->db->prepare("UPDATE sms_templates SET message = ? WHERE id = ?");
            $stmt->execute([$_POST['message'] ?? '', $_POST['id']]);
            
            header('Location: ' . url('sms?success=1'));
            exit;
        }
    }
}
