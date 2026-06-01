-- LIVE UPDATE SCRIPT for MFS Automated Payments feature
-- Run this on your live database to set up table structures

CREATE TABLE IF NOT EXISTS `payment_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `gateway_name` VARCHAR(50) NOT NULL UNIQUE,
    `receive_number` VARCHAR(20) NOT NULL,
    `status` TINYINT(1) DEFAULT 1,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed default values
INSERT IGNORE INTO `payment_settings` (`gateway_name`, `receive_number`, `status`) VALUES 
('bkash', '01700000000', 1),
('nagad', '01800000000', 1),
('rocket', '01900000000', 1);

CREATE TABLE IF NOT EXISTS `auto_payment_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `gateway` VARCHAR(50) NOT NULL,
    `sender_number` VARCHAR(20) NOT NULL,
    `receiver_number` VARCHAR(20) NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `trx_id` VARCHAR(100) NOT NULL,
    `reference` VARCHAR(100) DEFAULT NULL,
    `status` ENUM('success', 'unmatched', 'error') DEFAULT 'unmatched',
    `customer_id` INT NULL,
    `error_message` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL
);
