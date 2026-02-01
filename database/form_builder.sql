-- Dynamic Form Builder Tables

CREATE TABLE IF NOT EXISTS customer_form_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    order_index INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS customer_form_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    field_key VARCHAR(100) NOT NULL,
    label VARCHAR(255) NOT NULL,
    placeholder VARCHAR(255) DEFAULT NULL,
    type ENUM('text', 'textarea', 'dropdown', 'number', 'date', 'email', 'tel', 'password', 'select') DEFAULT 'text',
    required BOOLEAN DEFAULT FALSE,
    is_visible BOOLEAN DEFAULT TRUE,
    is_standard BOOLEAN DEFAULT FALSE, -- To distinguish between core fields and custom ones
    order_index INT DEFAULT 0,
    options TEXT, -- JSON for dropdown/select options
    FOREIGN KEY (section_id) REFERENCES customer_form_sections(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS customer_meta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    field_key VARCHAR(100) NOT NULL,
    field_value TEXT,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- Seed initial sections based on current form
INSERT INTO customer_form_sections (name, order_index) VALUES ('Personal Information', 1);
INSERT INTO customer_form_sections (name, order_index) VALUES ('Address', 2);
INSERT INTO customer_form_sections (name, order_index) VALUES ('Technical Information', 3);
INSERT INTO customer_form_sections (name, order_index) VALUES ('Mikrotik Configuration', 4);
INSERT INTO customer_form_sections (name, order_index) VALUES ('Package & Billing', 5);
INSERT INTO customer_form_sections (name, order_index) VALUES ('Official Information', 6);

-- Seed initial fields for "Personal Information" (section_id 1)
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES (1, 'full_name', 'Customer Name', 'text', 1, 1, 1, 'Mr. John Doe');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES (1, 'email', 'Email', 'email', 0, 1, 2, 'john@example.com');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES (1, 'identification_no', 'Identification No', 'text', 0, 1, 3, 'NID/Birth Certificate');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES (1, 'mobile_no', 'Mobile No', 'tel', 1, 1, 4, '8801xxxxxxxxx');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES (1, 'alt_mobile_no', 'Alt Mobile No', 'tel', 0, 1, 5, '018xxxxxxxx');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES (1, 'professional_detail', 'Professional Detail', 'text', 0, 1, 6, 'Software Engineer');

-- Seed initial fields for "Address" (section_id 2)
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (2, 'district', 'District', 'text', 0, 1, 1);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (2, 'thana', 'Thana', 'text', 0, 1, 2);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (2, 'area', 'Area', 'text', 0, 1, 3);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (2, 'building_name', 'Building Name', 'text', 0, 1, 4);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (2, 'floor', 'Floor', 'text', 0, 1, 5);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (2, 'tj_box', 'TJ Box', 'text', 0, 1, 6);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (2, 'house_no', 'House Info / No', 'text', 0, 1, 7);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (2, 'latitude', 'Latitude', 'text', 0, 1, 8);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (2, 'longitude', 'Longitude', 'text', 0, 1, 9);

-- Technical Info (section_id 3)
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (3, 'fiber_code', 'Fiber Code', 'text', 0, 1, 1);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (3, 'onu_mac', 'ONU Info', 'text', 0, 1, 2);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (3, 'group_name', 'Group', 'text', 0, 1, 3);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (3, 'lazar_info', 'Lazar Info', 'text', 0, 1, 4);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (3, 'server_info', 'Server Info', 'text', 0, 1, 5);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, options) VALUES (3, 'connection_type_tech', 'Connection Type', 'select', 0, 1, 6, '["PPPoE", "Static"]');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (3, 'connection_date', 'Connection Date', 'date', 0, 1, 7);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (3, 'expire_date', 'Expire Date', 'date', 0, 1, 8);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, options) VALUES (3, 'auto_disable', 'Auto Temporary Disable', 'select', 0, 1, 9, '{"0": "Off", "1": "On"}');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, options) VALUES (3, 'auto_disable_month', 'Auto Temporary Month', 'select', 0, 1, 10, '{"0": "Current Month", "1": "1 Month", "2": "2 Month", "3": "3 Month"}');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (3, 'extra_days', 'Set Extra Day', 'select', 0, 1, 11);

-- Mikrotik Configuration (section_id 4)
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, options) VALUES (4, 'mikrotik_id', 'Mikrotik Router', 'select', 0, 1, 1, '{"1": "Main Router"}');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (4, 'pppoe_name', 'PPPoE Name', 'text', 0, 1, 2);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (4, 'pppoe_password', 'Password', 'text', 0, 1, 3);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (4, 'pppoe_profile', 'Profile', 'text', 0, 1, 4);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES (4, 'ip_address', 'IP Address', 'text', 0, 1, 5, '1.1.1.1');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES (4, 'mac_address', 'MAC Address', 'text', 0, 1, 6, '00:1e:ec:...');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES (4, 'bandwidth', 'Bandwidth', 'text', 0, 1, 7, '2M/4M');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (4, 'comment', 'Comment', 'text', 0, 1, 8);

-- Package & Billing (section_id 5)
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (5, 'package_id', 'Package', 'select', 1, 1, 1);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (5, 'monthly_rent', 'Monthly Rent', 'number', 1, 1, 2);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES (5, 'payment_id', 'Payment ID (Customer ID)', 'text', 0, 1, 3, 'Gateway ID');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (5, 'due_amount', 'Due', 'number', 0, 1, 4);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (5, 'additional_charge', 'Additional Charge', 'number', 0, 1, 5);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (5, 'discount', 'Discount', 'number', 0, 1, 6);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (5, 'advance_amount', 'Advance', 'number', 0, 1, 7);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (5, 'vat_percent', 'Vat ( % )', 'number', 0, 1, 8);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (5, 'total_amount', 'Total', 'number', 0, 1, 9);

-- Official Information (section_id 6)
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, options) VALUES (6, 'billing_type', 'Billing Type', 'select', 0, 1, 1, '["Pre Paid", "Post Paid"]');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, options) VALUES (6, 'connectivity_type', 'Type of Connectivity', 'select', 0, 1, 2, '["Shared", "Dedicated"]');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, options) VALUES (6, 'connection_type', 'Type of Connection', 'select', 0, 1, 3, '["Fiber", "Cat5"]');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, options) VALUES (6, 'client_type', 'Type of Client', 'select', 0, 1, 4, '["Home", "Corporate"]');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES (6, 'distribution_point', 'Dist. Location Point', 'text', 0, 1, 5, 'DC');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (6, 'connected_by', 'Connected By', 'select', 0, 1, 6);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES (6, 'reference_name', 'Reference Name', 'text', 0, 1, 7, 'Reference person name');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, placeholder) VALUES (6, 'security_deposit', 'Security Deposit', 'number', 0, 1, 8, '2000');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index, options) VALUES (6, 'status', 'Status', 'select', 0, 1, 9, '{"pending": "Pending", "active": "Active", "inactive": "Inactive", "temp_disable": "Temporary Disable", "free": "Free Customer"}');
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (6, 'description', 'Description', 'textarea', 0, 1, 10);
INSERT INTO customer_form_fields (section_id, field_key, label, type, required, is_standard, order_index) VALUES (6, 'note', 'Note', 'textarea', 0, 1, 11);
