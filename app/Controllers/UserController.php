<?php
namespace App\Controllers;

use App\Core\Controller;
use Database;
use PDO;

class UserController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    /**
     * List all users (Super Admin only - checked in Middleware)
     */
    public function index()
    {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('users/index', [
            'title' => 'User Management',
            'path' => '/users',
            'users' => $users
        ]);
    }

    /**
     * Update user status (Approve/Reject/etc)
     */
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/user');
        }

        $userId = $_POST['user_id'] ?? null;
        $status = $_POST['status'] ?? '';

        if ($userId && in_array($status, ['active', 'inactive', 'pending'])) {
            $stmt = $this->db->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->execute([$status, $userId]);
            $_SESSION['success'] = "User status updated successfully.";
        }

        return $this->redirect('/user');
    }

    /**
     * Update user role
     */
    public function updateRole()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/user');
        }

        $userId = $_POST['user_id'] ?? null;
        $role = $_POST['role'] ?? '';

        if ($userId && in_array($role, ['Super Admin', 'Admin', 'Employee'])) {
            $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$role, $userId]);
            $_SESSION['success'] = "User role updated successfully.";
        }

        return $this->redirect('/user');
    }

    /**
     * Show/Update current user profile
     */
    public function profile()
    {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $message = '';
        $messageType = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $displayName = $_POST['display_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
            $bio = $_POST['bio'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($displayName && $email) {
                try {
                    if ($password) {
                        if ($password !== $confirmPassword) {
                            throw new \Exception("Passwords do not match.");
                        }
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $sql = "UPDATE users SET display_name = ?, email = ?, phone = ?, address = ?, bio = ?, password = ? WHERE id = ?";
                        $this->db->prepare($sql)->execute([$displayName, $email, $phone, $address, $bio, $hash, $userId]);
                    } else {
                        $sql = "UPDATE users SET display_name = ?, email = ?, phone = ?, address = ?, bio = ? WHERE id = ?";
                        $this->db->prepare($sql)->execute([$displayName, $email, $phone, $address, $bio, $userId]);
                    }

                    $_SESSION['display_name'] = $displayName;
                    $message = "Profile updated successfully!";
                    $messageType = "success";

                    $this->logActivity('Update Profile', "User updated their profile information.");

                    // Refresh user data
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (\Exception $e) {
                    $message = "Error updating profile: " . $e->getMessage();
                    $messageType = "error";
                }
            }
        }

        $this->view('users/profile', [
            'title' => 'My Profile',
            'path' => '/profile',
            'user' => $user,
            'message' => $message,
            'messageType' => $messageType
        ]);
    }

    /**
     * Show/Update current user password
     */
    public function changePassword()
    {
        $userId = $_SESSION['user_id'];
        $message = '';
        $messageType = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($currentPassword && $newPassword && $confirmPassword) {
                try {
                    // Verify current password
                    $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user && password_verify($currentPassword, $user['password'])) {
                        if ($newPassword === $confirmPassword) {
                            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                            $sql = "UPDATE users SET password = ? WHERE id = ?";
                            $this->db->prepare($sql)->execute([$hash, $userId]);

                            $message = "Password changed successfully!";
                            $messageType = "success";
                            $this->logActivity('Change Password', "User successfully changed their password.");
                        } else {
                            $message = "New passwords do not match.";
                            $messageType = "error";
                        }
                    } else {
                        $message = "Current password is incorrect.";
                        $messageType = "error";
                    }
                } catch (\Exception $e) {
                    $message = "Error changing password: " . $e->getMessage();
                    $messageType = "error";
                }
            }
        }

        $this->view('users/change_password', [
            'title' => 'Change Password',
            'path' => '/change-password',
            'message' => $message,
            'messageType' => $messageType
        ]);
    }

    /**
     * View user activity logs
     */
    public function activity()
    {
        $userId = $_SESSION['user_id'];

        $sql = "SELECT * FROM user_activity WHERE user_id = ? ORDER BY created_at DESC LIMIT 50";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('users/activity', [
            'title' => 'My Activity',
            'path' => '/activity',
            'activities' => $activities
        ]);
    }
}
