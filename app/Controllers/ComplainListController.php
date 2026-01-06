<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

class ComplainListController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists()
    {
        $sql = "CREATE TABLE IF NOT EXISTS customer_complains (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            complain_type_id INT NOT NULL,
            assigned_to JSON, -- Stores array of employee IDs
            description TEXT,
            status ENUM('Pending', 'In Progress', 'Resolved') DEFAULT 'Pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
            FOREIGN KEY (complain_type_id) REFERENCES complain_types(id)
        )";
        $this->db->exec($sql);
    }

    public function index()
    {
        // Fetch complaints with related data
        $sql = "SELECT cc.*, c.full_name, c.mobile_no, c.area, ct.title as complain_title 
                FROM customer_complains cc
                JOIN customers c ON cc.customer_id = c.id
                JOIN complain_types ct ON cc.complain_type_id = ct.id
                ORDER BY cc.id DESC";

        $stmt = $this->db->query($sql);
        $complains = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch all employees to map IDs to Names for display
        $empStmt = $this->db->query("SELECT id, name FROM employees");
        $employees = $empStmt->fetchAll(PDO::FETCH_KEY_PAIR); // [id => name]

        $this->view('complains/index', [
            'title' => 'Complain List',
            'path' => '/complain-list',
            'complains' => $complains,
            'employees' => $employees
        ]);
    }

    public function create()
    {
        // Fetch Complain Types
        $types = $this->db->query("SELECT * FROM complain_types")->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Employees
        $employees = $this->db->query("SELECT id, name FROM employees")->fetchAll(PDO::FETCH_ASSOC);

        $this->view('complains/create', [
            'title' => 'New Complain',
            'path' => '/complain-list/create',
            'types' => $types,
            'employees' => $employees
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_id = $_POST['customer_id'] ?? null;
            $complain_type_id = $_POST['complain_type_id'] ?? null;
            $assigned_to = $_POST['assigned_to'] ?? []; // Array
            $description = $_POST['description'] ?? '';
            $status = $_POST['status'] ?? 'Pending';

            if ($customer_id && $complain_type_id) {
                $stmt = $this->db->prepare("INSERT INTO customer_complains 
                    (customer_id, complain_type_id, assigned_to, description, status) 
                    VALUES (?, ?, ?, ?, ?)");

                $stmt->execute([
                    $customer_id,
                    $complain_type_id,
                    json_encode($assigned_to),
                    $description,
                    $status
                ]);
            }

            header('Location: ' . url('complain-list'));
            exit;
        }
    }

    // API for Customer Search
    public function search()
    {
        header('Content-Type: application/json');
        $q = $_GET['q'] ?? '';

        if (strlen($q) > 0) {
            // Search by Name, ID, Mobile
            $stmt = $this->db->prepare("SELECT id, full_name, mobile_no, area, district, house_no, payment_id, email 
                                        FROM customers 
                                        WHERE full_name LIKE ? OR mobile_no LIKE ? OR id LIKE ? 
                                        LIMIT 10");
            $term = "%$q%";
            try {
                $stmt->execute([$term, $term, $term]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($results);
            } catch (\PDOException $e) {
                echo json_encode([]);
            }
        } else {
            echo json_encode([]);
        }
        exit;
    }

    public function edit($id)
    {
        // Fetch the complain
        $stmt = $this->db->prepare("SELECT * FROM customer_complains WHERE id = ?");
        $stmt->execute([$id]);
        $complain = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$complain) {
            header('Location: ' . url('complain-list'));
            exit;
        }

        // Fetch customer details
        $custStmt = $this->db->prepare("SELECT id, full_name, mobile_no, area, district, house_no, payment_id FROM customers WHERE id = ?");
        $custStmt->execute([$complain['customer_id']]);
        $customer = $custStmt->fetch(PDO::FETCH_ASSOC);

        // Fetch Complain Types
        $types = $this->db->query("SELECT * FROM complain_types")->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Employees
        $employees = $this->db->query("SELECT id, name FROM employees")->fetchAll(PDO::FETCH_ASSOC);

        $this->view('complains/edit', [
            'title' => 'Edit Complain',
            'path' => '/complain-list/edit',
            'complain' => $complain,
            'customer' => $customer,
            'types' => $types,
            'employees' => $employees
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $complain_type_id = $_POST['complain_type_id'] ?? null;
            $assigned_to = $_POST['assigned_to'] ?? [];
            $description = $_POST['description'] ?? '';
            $status = $_POST['status'] ?? 'Pending';

            if ($id && $complain_type_id) {
                $stmt = $this->db->prepare("UPDATE customer_complains 
                    SET complain_type_id = ?, assigned_to = ?, description = ?, status = ?
                    WHERE id = ?");

                $stmt->execute([
                    $complain_type_id,
                    json_encode($assigned_to),
                    $description,
                    $status,
                    $id
                ]);
            }

            header('Location: ' . url('complain-list'));
            exit;
        }
    }

    public function delete($id)
    {
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM customer_complains WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        header('Location: ' . url('complain-list'));
        exit;
    }
}
