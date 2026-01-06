# HK ISP Billing Software

A modern, PHP-based MVC application for managing Internet Service Provider operations, including customer management, billing visualization, and administrative controls.

## 🚀 Features

- **Dynamic Dashboard**: Real-time stats and interactive charts (Revenue, Customers by Area, Customer Status Overview).
- **Customer Management**:
    - **Unified Profile**: 2-column editable view for all personal, technical, and billing data.
    - **Status Workflow**: Manage customers across multiple states (Pending, Active, Inactive, Temporary Disable, Free).
    - **Advanced Search**: Live AJAX-powered searching.
    - **Pagination & Sorting**: Efficiently manage large datasets (15 records per page) with interactive table headers.
- **MVC Architecture**: Clean separation of concerns for maintainability.

## 📁 Project Structure

```text
├── app/                # Core Application Logic
│   ├── Controllers/    # Request handlers (PageController, CustomerController)
│   ├── Core/           # System core classes (App.php, Controller.php, Helpers.php)
│   └── ...            # Placeholder for Models, Middleware, Services
├── config/             # Configuration files
│   └── database.php    # PDO Database Connection
├── public/             # Public Entry Point (The only web-accessible folder)
│   ├── css/            # Stylesheets
│   ├── js/             # Client-side logic (Chart.js implementation)
│   ├── .htaccess       # URL rewriting for MVC routing
│   └── index.php       # Front Controller
├── resources/          # Presentation Layer
│   └── views/          # PHP View Templates
│       ├── customers/  # Customer-related views (index, show, create, pending, search)
│       └── partials/   # Reusable UI components (header, footer)
├── database_schema.sql # MySQL Database Structure
└── .htaccess           # Root redirection to /public
```

## 🛠️ Setup Instructions

### Prerequisites
- PHP 7.4+
- MySQL/MariaDB
- Apache with `mod_rewrite` enabled (e.g., XAMPP, WAMP)

### Installation
1. Clone the repository into your web root (e.g., `htdocs/HK ISP Billing`).
2. Import `database_schema.sql` into your MySQL database.
3. Update `config/database.php` with your database credentials.
4. Access the application via `http://localhost/HK%20ISP%20Billing/`.

## 💻 Tech Stack
- **Backend**: PHP (Custom MVC Framework)
- **Frontend**: Vanilla HTML5, CSS3, JavaScript
- **Visualization**: [Chart.js](https://www.chartjs.org/)
- **Icons**: [Font Awesome 6](https://fontawesome.com/)
- **Typography**: [Inter Font](https://fonts.google.com/specimen/Inter)

## ⚖️ License
Proprietary - HK ISP Billing Software
