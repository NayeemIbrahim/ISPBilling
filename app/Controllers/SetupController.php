<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

class SetupController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

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
}
