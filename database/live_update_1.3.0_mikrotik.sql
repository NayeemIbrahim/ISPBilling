CREATE TABLE IF NOT EXISTS `mikrotiks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `ip_address` VARCHAR(100) NOT NULL,
    `username` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) DEFAULT NULL,
    `ssh_port` INT DEFAULT 22,
    `api_port` INT DEFAULT 8728,
    `status` ENUM('connected', 'disconnected') DEFAULT 'disconnected',
    `auto_backup` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
