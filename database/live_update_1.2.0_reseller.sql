-- LIVE UPDATE SCRIPT for Reseller Module
-- Version: 1.2.0

-- 1. Reseller Packages Table
CREATE TABLE IF NOT EXISTS `reseller_packages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `mikrotik_profile` VARCHAR(255) DEFAULT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Resellers Table
CREATE TABLE IF NOT EXISTS `resellers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `company_name` VARCHAR(255) NOT NULL,
    `contact_person` VARCHAR(255) DEFAULT NULL,
    `phone` VARCHAR(50) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `address` TEXT,
    `balance` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `auto_deduct` TINYINT(1) DEFAULT 0,
    `enable_with_payment` TINYINT(1) DEFAULT 0,
    `advance_payment` TINYINT(1) DEFAULT 0,
    `mikrotik_id` INT DEFAULT NULL,
    `pppoe_prefix` VARCHAR(50) DEFAULT NULL,
    `child_percentage` DECIMAL(5,2) DEFAULT 0.00,
    `note` TEXT,
    `drive_link` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Reseller Transactions Table
CREATE TABLE IF NOT EXISTS `reseller_transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `reseller_id` INT NOT NULL,
    `type` ENUM('deposit', 'withdraw', 'deduct', 'package_charge') NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `balance_after` DECIMAL(10,2) NOT NULL,
    `note` TEXT,
    `date` DATE NOT NULL,
    `created_by` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`reseller_id`) REFERENCES `resellers`(`id`) ON DELETE CASCADE
);

-- 4. Alter Customers table to add reseller_id (Optional linking)
ALTER TABLE `customers` 
ADD COLUMN IF NOT EXISTS `reseller_id` INT DEFAULT NULL AFTER `id`,
ADD CONSTRAINT `fk_customer_reseller` FOREIGN KEY (`reseller_id`) REFERENCES `resellers`(`id`) ON DELETE SET NULL;
