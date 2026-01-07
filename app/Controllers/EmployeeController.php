<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

/**
 * EmployeeController
 * 
 * Manages employee records, roles, and permissions.
 */
class EmployeeController extends Controller
{
    /**
     * EmployeeController constructor.
     */
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Display the employee and roles management view.
     * 
     * @return void
     */
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

        // 4. Mikrotik List
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

    /**
     * Store or update an employee role.
     * 
     * @return void Redirects back.
     */
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
        return $this->redirect('/employee');
    }

    /**
     * Delete an employee role.
     * 
     * @param int $id The role ID.
     * @return void Redirects back.
     */
    public function deleteRole($id)
    {
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM roles WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        return $this->redirect('/employee');
    }

    /**
     * Store or update an employee record.
     * 
     * @return void Redirects back.
     */
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
                    } catch (\PDOException $e) {
                        // Error handled by silent fail for now
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
                        } catch (\PDOException $e) {
                            // Error handled by silent fail for now
                        }
                    }
                }
            }
        }
        return $this->redirect('/employee');
    }

    /**
     * Delete an employee record.
     * 
     * @param int $id The employee ID.
     * @return void Redirects back.
     */
    public function deleteEmployee($id)
    {
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM employees WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        return $this->redirect('/employee');
    }

    /**
     * Ensures required tables (roles, employees) exist in the database.
     * 
     * @return void
     */
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
            // Silent fail
        }
    }
}
