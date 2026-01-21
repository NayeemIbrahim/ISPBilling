<?php
namespace App\Core;

/**
 * Base Controller Class
 * 
 * Provides basic view rendering, redirection, and JSON response functionality for all controllers.
 */
class Controller
{
    protected $db;

    /**
     * Load a Model (Manual include for simple MVC pattern).
     * 
     * @param string $model The model class name.
     * @return object An instance of the model.
     */
    public function model($model)
    {
        require_once __DIR__ . '/../Models/' . $model . '.php';
        return new $model();
    }

    /**
     * Render a view file with data.
     * 
     * @param string $view The path to the view file relative to resources/views/.
     * @param array $data Associative array of data to extract into variables for the view.
     */
    public function view($view, $data = [])
    {
        // Extract data array to variables
        if (!empty($data)) {
            extract($data);
        }

        // Define path
        $viewPath = __DIR__ . '/../../resources/views/' . $view . '.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View does not exist: " . $view);
        }
    }

    /**
     * Redirect to a specific URL or internal path.
     * 
     * @param string $url The target URL or relative path (e.g., 'dashboard').
     */
    public function redirect($url)
    {
        // Check if url is full path or relative
        if (strpos($url, 'http') === 0) {
            header("Location: " . $url);
        } else {
            // Remove leading slash to avoid double slash with helper
            $cleanUrl = ltrim($url, '/');
            header("Location: " . url($cleanUrl));
        }
        exit();
    }

    /**
     * Send a standardized JSON response and exit.
     * 
     * @param mixed $data The data to encode.
     * @param int $status The HTTP status code (default 200).
     */
    public function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Log user activity in the database.
     * 
     * @param string $type The type of activity (e.g., 'Login', 'Update Profile').
     * @param string $description Detailed description of the activity.
     */
    protected function logActivity($type, $description)
    {
        if (!isset($_SESSION['user_id']))
            return;

        try {
            if (!$this->db) {
                require_once __DIR__ . '/Database.php';
                $this->db = (new \Database())->getConnection();
            }

            $userId = $_SESSION['user_id'];
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

            $sql = "INSERT INTO user_activity (user_id, activity_type, description, ip_address) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $type, $description, $ip]);
        } catch (\Exception $e) {
            // Silently fail logging to avoid breaking main flow
        }
    }
}
