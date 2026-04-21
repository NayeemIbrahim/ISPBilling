<?php
/**
 * Database Wrapper (PDO)
 * 
 * Provides a persistent connection to the database using parameters 
 * defined in the config/database.php file.
 */

class Database
{
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn;

    /**
     * Get the database connection.
     * 
     * @return PDO|null The PDO connection object or null on failure.
     */
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            // Ensure UTF-8 and exception-based error handling
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // In a real-world scenario, you might want to log this error 
            // instead of displaying it.
            die("Connection Error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
