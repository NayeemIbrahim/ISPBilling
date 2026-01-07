<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

/**
 * PrefixController
 * 
 * Manages ID prefixes for customers and ensures database consistency.
 */
class PrefixController extends Controller
{
    /**
     * PrefixController constructor.
     */
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->initDatabase();
    }

    /**
     * Initializes the id_prefixes table and migrates customers if needed.
     * 
     * @return void
     */
    private function initDatabase()
    {
        // 1. Create id_prefixes table
        $sql = "CREATE TABLE IF NOT EXISTS id_prefixes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            prefix_code VARCHAR(20) NOT NULL,
            is_default BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);

        // 2. Add prefix_id to customers table if not exists
        try {
            $this->db->query("SELECT prefix_id FROM customers LIMIT 1");
        } catch (\PDOException $e) {
            $this->db->exec("ALTER TABLE customers ADD COLUMN prefix_id INT NULL");
            $this->db->exec("ALTER TABLE customers ADD FOREIGN KEY (prefix_id) REFERENCES id_prefixes(id)");
        }

        // 3. Seed default prefix if empty and link customers
        $stmt = $this->db->query("SELECT id FROM id_prefixes WHERE is_default = TRUE LIMIT 1");
        $defaultId = $stmt->fetchColumn();

        if (!$defaultId) {
            // Check if any prefixes exist at all
            $stmt = $this->db->query("SELECT id FROM id_prefixes LIMIT 1");
            $anyId = $stmt->fetchColumn();

            if (!$anyId) {
                $this->db->exec("INSERT INTO id_prefixes (prefix_code, is_default) VALUES ('HK_', TRUE)");
                $defaultId = $this->db->lastInsertId();
            } else {
                $defaultId = $anyId;
                $this->db->exec("UPDATE id_prefixes SET is_default = TRUE WHERE id = $defaultId");
            }
        }

        // Always ensure no NULL prefix_id in customers
        if ($defaultId) {
            $this->db->exec("UPDATE customers SET prefix_id = $defaultId WHERE prefix_id IS NULL");
        }
    }

    /**
     * Display the ID prefix setup view.
     * 
     * @return void
     */
    public function index()
    {
        $sql = "SELECT ip.*, COUNT(c.id) as customer_count 
                FROM id_prefixes ip 
                LEFT JOIN customers c ON ip.id = c.prefix_id 
                GROUP BY ip.id 
                ORDER BY ip.id ASC";
        $stmt = $this->db->query($sql);
        $prefixes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('setup/prefix', [
            'title' => 'ID Prefix Setup',
            'path' => '/prefix',
            'prefixes' => $prefixes
        ]);
    }

    /**
     * Store or update an ID prefix.
     * 
     * @return void Redirects back.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $prefix_code = $_POST['prefix_code'] ?? '';

            if ($id) {
                // Update
                $stmt = $this->db->prepare("UPDATE id_prefixes SET prefix_code = :prefix_code WHERE id = :id");
                $stmt->execute([':prefix_code' => $prefix_code, ':id' => $id]);
            } else {
                // Create
                $stmt = $this->db->prepare("INSERT INTO id_prefixes (prefix_code) VALUES (:prefix_code)");
                $stmt->execute([':prefix_code' => $prefix_code]);
            }

            return $this->redirect('/prefix');
        }
    }

    /**
     * Set a prefix as the default for new customers.
     * 
     * @param int $id The prefix ID.
     * @return void Redirects back.
     */
    public function setDefault($id)
    {
        if ($id) {
            // Reset all
            $this->db->exec("UPDATE id_prefixes SET is_default = FALSE");
            // Set new default
            $stmt = $this->db->prepare("UPDATE id_prefixes SET is_default = TRUE WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        return $this->redirect('/prefix');
    }
}
