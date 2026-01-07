<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

class CollectionController extends Controller
{
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->initDatabase();
    }

    private function initDatabase()
    {
        $sql = "CREATE TABLE IF NOT EXISTS collections (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            payment_method VARCHAR(50) NOT NULL,
            invoice_no VARCHAR(100) NULL,
            collection_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            next_expire_date DATE NULL,
            collected_by INT NULL,
            note TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id)
        )";
        $this->db->exec($sql);
    }

    public function amount()
    {
        $this->view('collection/amount', [
            'title' => 'Amount Collection',
            'path' => '/collection/amount'
        ]);
    }

    public function search()
    {
        header('Content-Type: application/json');
        $q = $_GET['q'] ?? '';

        if (strlen($q) < 2) {
            echo json_encode([]);
            return;
        }

        $sql = "SELECT c.id, c.full_name, c.mobile_no, c.pppoe_name, ip.prefix_code 
                FROM customers c 
                LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id 
                WHERE (LOWER(c.full_name) LIKE ? 
                   OR c.mobile_no LIKE ? 
                   OR LOWER(c.pppoe_name) LIKE ? 
                   OR CONCAT(LOWER(COALESCE(ip.prefix_code,'')), c.id) LIKE ?) 
                LIMIT 10";

        $term = "%" . strtolower($q) . "%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$term, $term, $term, $term]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($results);
    }

    public function getCustomerInfo($id)
    {
        header('Content-Type: application/json');

        $sql = "SELECT c.*, ip.prefix_code, p.name as package_name 
                FROM customers c 
                LEFT JOIN id_prefixes ip ON c.prefix_id = ip.id 
                LEFT JOIN packages p ON c.package_id = p.id 
                WHERE c.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer) {
            // Fetch recent collection for this customer
            $historyStmt = $this->db->prepare("SELECT amount, collection_date, payment_method, invoice_no FROM collections WHERE customer_id = ? ORDER BY id DESC LIMIT 1");
            $historyStmt->execute([$id]);
            $lastPayment = $historyStmt->fetch(PDO::FETCH_ASSOC);
            $customer['last_payment'] = $lastPayment;

            echo json_encode(['status' => 'success', 'data' => $customer]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Customer not found']);
        }
    }

    public function edit()
    {
        $this->view('collection/edit', [
            'title' => 'Edit Collection',
            'path' => '/collection/edit'
        ]);
    }

    public function getHistory($id)
    {
        header('Content-Type: application/json');

        $sql = "SELECT c.*, e.name as collected_by_name 
                FROM collections c 
                LEFT JOIN employees e ON c.collected_by = e.id 
                WHERE c.customer_id = ? 
                ORDER BY c.collection_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'success', 'data' => $history]);
    }

    public function deleteRecord()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];

            try {
                $this->db->beginTransaction();

                // 1. Get the collection record info before deleting
                $stmt = $this->db->prepare("SELECT * FROM collections WHERE id = ?");
                $stmt->execute([$id]);
                $col = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$col)
                    throw new \Exception("Record not found");

                $customer_id = $col['customer_id'];
                $amount = $col['amount'];

                // 2. Delete the record
                $delStmt = $this->db->prepare("DELETE FROM collections WHERE id = ?");
                $delStmt->execute([$id]);

                // 3. Revert customer balance (Increase due_amount by the deleted payment amount)
                $updStmt = $this->db->prepare("UPDATE customers SET due_amount = due_amount + ? WHERE id = ?");
                $updStmt->execute([$amount, $customer_id]);

                // 4. Revert expire_date
                // Fetch the NEW last collection's next_expire_date
                $lastColStmt = $this->db->prepare("SELECT next_expire_date FROM collections WHERE customer_id = ? ORDER BY id DESC LIMIT 1");
                $lastColStmt->execute([$customer_id]);
                $lastCol = $lastColStmt->fetch(PDO::FETCH_ASSOC);

                if ($lastCol) {
                    $newExpire = $lastCol['next_expire_date'];
                } else {
                    // No collections left. Revert to connection_date or null?
                    // Let's try to fetch connection_date
                    $custStmt = $this->db->prepare("SELECT connection_date FROM customers WHERE id = ?");
                    $custStmt->execute([$customer_id]);
                    $cust = $custStmt->fetch(PDO::FETCH_ASSOC);
                    $newExpire = $cust['connection_date'] ?? null;
                }

                $this->db->prepare("UPDATE customers SET expire_date = ? WHERE id = ?")->execute([$newExpire, $customer_id]);

                $this->db->commit();
                echo json_encode(['status' => 'success', 'message' => 'Record deleted and balance reverted.']);
            } catch (\Exception $e) {
                if ($this->db->inTransaction())
                    $this->db->rollBack();
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_id = $_POST['customer_id'];
            $amount = $_POST['amount'];
            $payment_method = $_POST['payment_method'];
            $invoice_no = $_POST['invoice_no'] ?? null;
            $next_expire_date = $_POST['next_expire_date'] ?? null;
            $note = $_POST['note'] ?? '';

            // Auto-assign collector if logged in
            $collected_by = $_SESSION['user_id'] ?? $_SESSION['employee_id'] ?? null;

            try {
                $this->db->beginTransaction();

                // 1. Insert Collection Record
                $sql = "INSERT INTO collections (customer_id, amount, payment_method, invoice_no, next_expire_date, note, collected_by) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$customer_id, $amount, $payment_method, $invoice_no, $next_expire_date, $note, $collected_by]);

                // 2. Update Customer's Balance, Expire Date and Status
                // Subtract amount from due_amount
                $updateSql = "UPDATE customers SET 
                                due_amount = due_amount - ?, 
                                expire_date = ?, 
                                status = 'active' 
                              WHERE id = ?";
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->execute([$amount, $next_expire_date, $customer_id]);

                $this->db->commit();

                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Collection successful']);
            } catch (\Exception $e) {
                if ($this->db->inTransaction())
                    $this->db->rollBack();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }
    }
}
