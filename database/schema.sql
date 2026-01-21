CREATE DATABASE IF NOT EXISTS hk_isp_billing;
USE hk_isp_billing;

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Personal Information
    full_name VARCHAR(255) NOT NULL,
    company_name VARCHAR(255),
    contact_person VARCHAR(255),
    email VARCHAR(255),
    identification_no VARCHAR(100),
    mobile_no VARCHAR(20) NOT NULL,
    alt_mobile_no VARCHAR(20),
    professional_detail VARCHAR(255),
    
    -- Address
    district VARCHAR(100),
    thana VARCHAR(100),
    area VARCHAR(100),
    building_name VARCHAR(100),
    floor VARCHAR(50),
    tj_box VARCHAR(50),
    house_no VARCHAR(50),
    
    -- Technical Info
    fiber_code VARCHAR(100),
    onu_mac VARCHAR(100),
    group_name VARCHAR(100),
    lazar_info VARCHAR(100),
    server_info VARCHAR(100),
    connection_date DATE,
    
    -- Mikrotik Info
    mikrotik_id INT,
    ip_address VARCHAR(50),
    mac_address VARCHAR(50),
    bandwidth VARCHAR(50),
    comment TEXT,
    
    -- Package & Billing
    package_name VARCHAR(100),
    monthly_rent DECIMAL(10, 2) DEFAULT 0,
    due_amount DECIMAL(10, 2) DEFAULT 0,
    additional_charge DECIMAL(10, 2) DEFAULT 0,
    discount DECIMAL(10, 2) DEFAULT 0,
    advance_amount DECIMAL(10, 2) DEFAULT 0,
    vat_percent DECIMAL(5, 2) DEFAULT 0,
    total_amount DECIMAL(10, 2) DEFAULT 0,
    
    -- Official Info
    billing_type ENUM('Prepaid', 'Postpaid') DEFAULT 'Prepaid',
    connectivity_type ENUM('Shared', 'Dedicated') DEFAULT 'Shared',
    connection_type ENUM('Fiber', 'Cat5') DEFAULT 'Fiber',
    client_type ENUM('Home', 'Corporate') DEFAULT 'Home',
    distribution_point VARCHAR(100),
    description TEXT,
    note TEXT,
    connected_by VARCHAR(100),
    security_deposit DECIMAL(10, 2) DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
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
);

CREATE TABLE IF NOT EXISTS user_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

