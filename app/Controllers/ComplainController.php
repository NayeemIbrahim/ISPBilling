<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

class ComplainController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists()
    {
        $sql = "CREATE TABLE IF NOT EXISTS complain_types (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);
    }

    public function index()
    {
        $stmt = $this->db->query("SELECT * FROM complain_types ORDER BY id ASC");
        $complains = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('setup/complain', [
            'title' => 'Complain Setup',
            'path' => '/complain',
            'complains' => $complains
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($title)) {
                // handle error or redirect
                header('Location: ' . url('complain'));
                exit;
            }

            if ($id) {
                // Update
                $stmt = $this->db->prepare("UPDATE complain_types SET title = :title, description = :description WHERE id = :id");
                $stmt->execute([':title' => $title, ':description' => $description, ':id' => $id]);
            } else {
                // Create
                $stmt = $this->db->prepare("INSERT INTO complain_types (title, description) VALUES (:title, :description)");
                $stmt->execute([':title' => $title, ':description' => $description]);
            }

            header('Location: ' . url('complain'));
            exit;
        }
    }

    public function delete($id)
    {
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM complain_types WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        header('Location: ' . url('complain'));
        exit;
    }
}
