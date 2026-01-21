<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

class AuthController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();

        if (!$this->db) {
            return;
        }

        // Auto-Migration for users table
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                display_name VARCHAR(255) NOT NULL,
                username VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                role ENUM('Super Admin', 'Admin', 'Employee') DEFAULT 'Employee',
                status ENUM('pending', 'active', 'inactive') DEFAULT 'pending',
                phone VARCHAR(20) DEFAULT NULL,
                address TEXT DEFAULT NULL,
                bio TEXT DEFAULT NULL,
                profile_picture VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");

            // Ensure missing columns exist in users table
            $columns = $this->db->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);
            $newColumns = [
                'phone' => "VARCHAR(20) DEFAULT NULL AFTER status",
                'address' => "TEXT DEFAULT NULL AFTER phone",
                'bio' => "TEXT DEFAULT NULL AFTER address",
                'profile_picture' => "VARCHAR(255) DEFAULT NULL AFTER bio"
            ];

            foreach ($newColumns as $col => $definition) {
                if (!in_array($col, $columns)) {
                    $this->db->exec("ALTER TABLE users ADD COLUMN $col $definition");
                }
            }

            // Auto-Migration for user_activity table
            $this->db->exec("CREATE TABLE IF NOT EXISTS user_activity (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                activity_type VARCHAR(100) NOT NULL,
                description TEXT,
                ip_address VARCHAR(45),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )");

            // Seed Super Admin if not exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = 'superadmin'");
            $stmt->execute();
            if (!$stmt->fetch()) {
                $hash = password_hash('superadmin', PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (display_name, username, password, email, role, status) 
                        VALUES ('Super Admin', 'superadmin', ?, 'nayeemibrahim46@gmail.com', 'Super Admin', 'active')";
                $this->db->prepare($sql)->execute([$hash]);
            }
        } catch (\Exception $e) {
            // Log error
        }
    }

    /**
     * Default index (redirect to login)
     */
    public function index()
    {
        return $this->redirect('/auth/login');
    }


    /**
     * Show Login Form
     */
    public function login()
    {
        if (isset($_SESSION['user_id'])) {
            return $this->redirect('/dashboard');
        }

        $this->view('auth/login', [
            'title' => 'Login - ISP Billing',
            'no_header' => true
        ]);
    }

    /**
     * Handle Authentication
     */
    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/auth/login');
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                $_SESSION['error'] = "Your account is " . $user['status'] . ". Please contact Super Admin.";
                return $this->redirect('/auth/login');
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['display_name'] = $user['display_name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_picture'] = $user['profile_picture'];

            $this->logActivity('Login', 'User successfully logged in.');

            return $this->redirect('/dashboard');
        }

        $_SESSION['error'] = "Invalid username or password.";
        return $this->redirect('/auth/login');
    }

    /**
     * Show Registration Form
     */
    public function register()
    {
        $this->view('auth/register', [
            'title' => 'Register - ISP Billing',
            'no_header' => true
        ]);
    }

    /**
     * Handle Registration
     */
    public function storeRegistration()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/auth/register');
        }

        $displayName = $_POST['display_name'] ?? '';
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = "Passwords do not match.";
            return $this->redirect('/auth/register');
        }

        // Check if exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Username or Email already exists.";
            return $this->redirect('/auth/register');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (display_name, username, email, password, role, status) 
                VALUES (?, ?, ?, ?, 'Employee', 'pending')";

        try {
            $this->db->prepare($sql)->execute([$displayName, $username, $email, $hash]);
            $_SESSION['success'] = "Registration submitted. Please wait for Super Admin approval.";
            return $this->redirect('/auth/login');
        } catch (\Exception $e) {
            $_SESSION['error'] = "Registration failed: " . $e->getMessage();
            return $this->redirect('/auth/register');
        }
    }

    /**
     * Handle Logout
     */
    public function logout()
    {
        $this->logActivity('Logout', 'User logged out.');
        session_destroy();
        return $this->redirect('/auth/login');
    }
}
