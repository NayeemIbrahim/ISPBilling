-- LIVE UPDATE SCRIPT for Dynamic Form Builder feature
-- Run this on your live database (billing.nefconit.com)

-- 1. Create Tables
CREATE TABLE IF NOT EXISTS `customer_form_sections` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `order_index` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `customer_form_fields` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `section_id` INT,
    `field_key` VARCHAR(100) NOT NULL UNIQUE,
    `label` VARCHAR(255) NOT NULL,
    `placeholder` VARCHAR(255) DEFAULT NULL,
    `type` ENUM('text', 'textarea', 'dropdown', 'number', 'date', 'email', 'tel', 'password', 'select') DEFAULT 'text',
    `required` TINYINT(1) DEFAULT 0,
    `is_visible` TINYINT(1) DEFAULT 1,
    `is_standard` TINYINT(1) DEFAULT 0,
    `order_index` INT DEFAULT 0,
    `options` TEXT,
    FOREIGN KEY (`section_id`) REFERENCES `customer_form_sections`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `customer_meta` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL,
    `field_key` VARCHAR(100) NOT NULL,
    `field_value` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (`customer_id`),
    INDEX (`field_key`)
);

-- 2. Clean up existing (if any accidental runs during development)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE customer_form_fields;
TRUNCATE TABLE customer_form_sections;
SET FOREIGN_KEY_CHECKS = 1;

-- 3. Seed Sections
INSERT INTO customer_form_sections (id, name, order_index) VALUES 
(1, 'Personal Information', 0),
(2, 'Address', 1),
(3, 'Technical Information', 2),
(4, 'Mikrotik Configuration', 3),
(5, 'Package & Billing', 4),
(6, 'Official Information', 5);

-- 4. Seed Fields

-- Section 1: Personal Information
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES 
(1, 'full_name', 'Customer Name', 'text', 1, 1, 0, 'Mr. John Doe'),
(1, 'email', 'Email Address', 'email', 0, 1, 1, 'john@example.com'),
(1, 'identification_no', 'Identification No', 'text', 0, 1, 2, 'NID/Birth Certificate'),
(1, 'mobile_no', 'Mobile No', 'tel', 1, 1, 3, '8801xxxxxxxxx'),
(1, 'alt_mobile_no', 'Alt Mobile No', 'tel', 0, 1, 4, '018xxxxxxxx'),
(1, 'professional_detail', 'Professional Detail', 'text', 0, 1, 5, 'Software Engineer');

-- Section 2: Address
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES 
(2, 'district', 'District', 'text', 0, 1, 0),
(2, 'thana', 'Thana', 'text', 0, 1, 1),
(2, 'area', 'Area', 'text', 0, 1, 2),
(2, 'building_name', 'Building Name', 'text', 0, 1, 3),
(2, 'floor', 'Floor', 'text', 0, 1, 4),
(2, 'tj_box', 'TJ Box', 'text', 0, 1, 5),
(2, 'house_no', 'House Info / No', 'text', 0, 1, 6),
(2, 'latitude', 'Latitude', 'text', 0, 1, 7),
(2, 'longitude', 'Longitude', 'text', 0, 1, 8);

-- Section 3: Technical Information
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, options) VALUES 
(3, 'fiber_code', 'Fiber Code', 'text', 0, 1, 0, NULL),
(3, 'onu_mac', 'ONU Info', 'text', 0, 1, 1, NULL),
(3, 'group_name', 'Group', 'text', 0, 1, 2, NULL),
(3, 'lazar_info', 'Lazar Info', 'text', 0, 1, 3, NULL),
(3, 'server_info', 'Server Info', 'text', 0, 1, 4, NULL),
(3, 'connection_type_tech', 'Connection Type', 'select', 0, 1, 5, '["PPPoE", "Static"]'),
(3, 'connection_date', 'Connection Date', 'date', 0, 1, 6, NULL),
(3, 'expire_date', 'Expire Date', 'date', 0, 1, 7, NULL),
(3, 'auto_disable', 'Auto Temporary Disable', 'select', 0, 1, 8, '{"0": "Off", "1": "On"}'),
(3, 'auto_disable_month', 'Auto Temporary Month', 'select', 0, 1, 9, '{"0": "Current Month", "1": "1 Month", "2": "2 Month", "3": "3 Month"}'),
(3, 'extra_days', 'Set Extra Day', 'select', 0, 1, 10, NULL);

-- Section 4: Mikrotik Configuration
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, options, placeholder) VALUES 
(4, 'mikrotik_id', 'Mikrotik Router', 'select', 0, 1, 0, '{"1": "Main Router"}', 'Main Router'),
(4, 'pppoe_name', 'PPPoE Name', 'text', 0, 1, 1, NULL, NULL),
(4, 'pppoe_password', 'Password', 'text', 0, 1, 2, NULL, NULL),
(4, 'pppoe_profile', 'Profile', 'text', 0, 1, 3, NULL, NULL),
(4, 'ip_address', 'IP Address', 'text', 0, 1, 4, NULL, '1.1.1.1'),
(4, 'mac_address', 'MAC Address', 'text', 0, 1, 5, NULL, '00:1e:ec:...'),
(4, 'bandwidth', 'Bandwidth', 'text', 0, 1, 6, NULL, '2M/4M'),
(4, 'comment', 'Comment', 'text', 0, 1, 7, NULL, NULL);

-- Section 5: Package & Billing
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES 
(5, 'package_id', 'Package', 'select', 1, 1, 0, NULL),
(5, 'monthly_rent', 'Monthly Rent', 'number', 1, 1, 1, NULL),
(5, 'payment_id', 'Payment ID (Customer ID)', 'text', 0, 1, 2, 'Gateway ID'),
(5, 'due_amount', 'Due', 'number', 0, 1, 3, NULL),
(5, 'additional_charge', 'Additional Charge', 'number', 0, 1, 4, NULL),
(5, 'discount', 'Discount', 'number', 0, 1, 5, NULL),
(5, 'advance_amount', 'Advance', 'number', 0, 1, 6, NULL),
(5, 'vat_percent', 'Vat ( % )', 'number', 0, 1, 7, NULL),
(5, 'total_amount', 'Total', 'number', 0, 1, 8, NULL);

-- Section 6: Official Information
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, options, placeholder) VALUES 
(6, 'billing_type', 'Billing Type', 'select', 0, 1, 0, '["Pre Paid", "Post Paid"]', 'Pre Paid'),
(6, 'connectivity_type', 'Type of Connectivity', 'select', 0, 1, 1, '["Shared", "Dedicated"]', 'Shared'),
(6, 'connection_type', 'Type of Connection', 'select', 0, 1, 2, '["Fiber", "Cat5"]', 'Fiber'),
(6, 'client_type', 'Type of Client', 'select', 0, 1, 3, '["Home", "Corporate"]', 'Home'),
(6, 'distribution_point', 'Dist. Location Point', 'text', 0, 1, 4, NULL, 'DC'),
(6, 'connected_by', 'Connected By', 'select', 0, 1, 5, NULL, 'Select Employee'),
(6, 'reference_name', 'Reference Name', 'text', 0, 1, 6, NULL, 'Reference person name'),
(6, 'security_deposit', 'Security Deposit', 'number', 0, 1, 7, NULL, '2000'),
(6, 'status', 'Status', 'select', 0, 1, 8, '{"pending": "Pending", "active": "Active", "inactive": "Inactive", "temp_disable": "Temporary Disable", "free": "Free Customer"}', 'Pending'),
(6, 'description', 'Description', 'textarea', 0, 1, 9, NULL, NULL),
(6, 'note', 'Note', 'textarea', 0, 1, 10, NULL, NULL);
