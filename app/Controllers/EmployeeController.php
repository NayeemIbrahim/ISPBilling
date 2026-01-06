<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

class EmployeeController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function index()
    {
        // 1. Auto-Migration
        $this->ensureTablesExist();

        // 2. Fetch Data
        $roles = $this->db->query("SELECT * FROM roles ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        $sqlEmployees = "SELECT e.*, r.name as role_name 
                         FROM employees e 
                         LEFT JOIN roles r ON e.role_id = r.id 
                         ORDER BY e.id DESC";
        $employees = $this->db->query($sqlEmployees)->fetchAll(PDO::FETCH_ASSOC);

        // 3. Define Menus for Permissions
        $menus = [
            'Dashboard',
            'Customers',
            'Mikrotik',
            'Reseller',
            'Setup',
            'Reports'
        ];

        // 4. Mikrotik List (Mock or Fetch if table exists, for now hardcoded or empty)
        // Assuming we might have a mikrotiks table later, but user asked for dropdown. 
        // We'll provide a static list or check if table exists. 
        // For now, I'll put some placeholders as per previous context 'Mikrotik' menu implies it exists.
        $mikrotiks = ['Router 1', 'Router 2', 'Main Router']; // Placeholder

        $this->view('setup/employee', [
            'title' => 'Employee Setup',
            'path' => '/setup/employee',
            'roles' => $roles,
            'employees' => $employees,
            'menus' => $menus,
            'mikrotiks' => $mikrotiks
        ]);
    }

    public function storeRole()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $name = $_POST['role_name'] ?? '';
            $description = $_POST['description'] ?? '';
            $permissions = isset($_POST['permissions']) ? json_encode($_POST['permissions']) : json_encode([]);

            if ($name) {
                if ($id) {
                    // Update
                    $stmt = $this->db->prepare("UPDATE roles SET name=:name, description=:description, permissions=:permissions WHERE id=:id");
                    $stmt->execute([
                        ':name' => $name,
                        ':description' => $description,
                        ':permissions' => $permissions,
                        ':id' => $id
                    ]);
                } else {
                    // Create
                    $stmt = $this->db->prepare("INSERT INTO roles (name, description, permissions) VALUES (:name, :description, :permissions)");
                    $stmt->execute([
                        ':name' => $name,
                        ':description' => $description,
                        ':permissions' => $permissions
                    ]);
                }
            }
        }
        header('Location: ' . url('employee')); // Redirect back
        exit;
    }

    public function deleteRole($id)
    {
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM roles WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        header('Location: ' . url('employee'));
        exit;
    }

    public function storeEmployee()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $mobile = $_POST['mobile'] ?? '';
            $password = $_POST['password'] ?? '';
            $retype_password = $_POST['retype_password'] ?? '';
            $role_id = $_POST['role_id'] ?? null;
            $mikrotik_access = $_POST['mikrotik_access'] ?? null;

            if ($name && $email) {
                if ($id) {
                    // Update
                    $sql = "UPDATE employees SET name=:name, email=:email, mobile=:mobile, role_id=:role_id, mikrotik_access=:mikrotik_access";
                    $params = [
                        ':name' => $name,
                        ':email' => $email,
                        ':mobile' => $mobile,
                        ':role_id' => $role_id,
                        ':mikrotik_access' => $mikrotik_access,
                        ':id' => $id
                    ];

                    // Only update password if provided
                    if (!empty($password) && ($password === $retype_password)) {
                        $sql .= ", password=:password";
                        $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
                    }

                    $sql .= " WHERE id=:id";
                    $stmt = $this->db->prepare($sql);
                    try {
                        $stmt->execute($params);
                    } catch (\PDOException $e) { /* Handle error */
                    }

                } else {
                    // Create
                    if ($password && ($password === $retype_password)) {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $this->db->prepare("INSERT INTO employees (name, email, mobile, password, role_id, mikrotik_access) VALUES (:name, :email, :mobile, :password, :role_id, :mikrotik_access)");
                        try {
                            $stmt->execute([
                                ':name' => $name,
                                ':email' => $email,
                                ':mobile' => $mobile,
                                ':password' => $hashed_password,
                                ':role_id' => $role_id,
                                ':mikrotik_access' => $mikrotik_access
                            ]);
                        } catch (\PDOException $e) { /* Handle error */
                        }
                    }
                }
            }
        }
        header('Location: ' . url('employee'));
        exit;
    }

    public function deleteEmployee($id)
    {
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM employees WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        header('Location: ' . url('employee'));
        exit;
    }

    private function ensureTablesExist()
    {
        try {
            // Roles Table
            $this->db->exec("CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                permissions TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            // Employees Table
            $this->db->exec("CREATE TABLE IF NOT EXISTS employees (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                mobile VARCHAR(20),
                password VARCHAR(255) NOT NULL,
                role_id INT,
                mikrotik_access VARCHAR(100),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
            )");
        } catch (\Exception $e) {
            // SIlent fail or log
        }
    }
}
