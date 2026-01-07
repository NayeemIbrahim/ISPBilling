<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

/**
 * ComplainController
 * 
 * Manages complain types/categories for the setup section.
 */
class ComplainController extends Controller
{
    /**
     * ComplainController constructor.
     */
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->createTableIfNotExists();
    }

    /**
     * Ensures the complain_types table exists.
     * 
     * @return void
     */
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

    /**
     * Display the complain setup view.
     * 
     * @return void
     */
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

    /**
     * Store or update a complain type.
     * 
     * @return void Redirects back.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($title)) {
                return $this->redirect('/complain');
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

            return $this->redirect('/complain');
        }
    }

    /**
     * Delete a complain type.
     * 
     * @param int $id The complain type ID.
     * @return void Redirects back.
     */
    public function delete($id)
    {
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM complain_types WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        return $this->redirect('/complain');
    }
}
