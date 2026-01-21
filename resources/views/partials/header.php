<?php
// Define path helper
$path = $data['path'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'ISP Billing' ?> - HK ISP Billing</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('favicon.png') ?>">
    <!-- Adjust paths with asset() helper -->
    <link rel="stylesheet" href="<?= asset('css/style.css?v=' . time()) ?>">
    <link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/customer.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>

    <!-- Top Navigation Bar -->
    <nav class="top-nav">
        <div class="nav-brand">HK ISP</div>

        <ul class="nav-menu">
            <li><a href="<?= url('dashboard') ?>" class="<?= $path === '/dashboard' ? 'active' : '' ?>">Home</a></li>

            <li class="dropdown">
                <a href="#" class="<?= strpos($path, '/customer') !== false ? 'active' : '' ?>">Customer</a>
                <div class="dropdown-content">
                    <a href="<?= url('customer/create') ?>">Create Customer</a>
                    <a href="<?= url('customer') ?>">All Customers</a>
                    <a href="<?= url('customer/search') ?>">Search Customer</a>
                    <a href="<?= url('customer/pending') ?>">Pending Customer</a>
                    <a href="<?= url('complain-list') ?>">Complain List</a>
                </div>
            </li>

            <li class="dropdown">
                <a href="#">Receipt</a>
                <div class="dropdown-content">
                    <a href="#">Area Wise Receipt</a>
                    <a href="#">Customer Wise Receipt</a>
                    <a href="#">Update Bill</a>
                </div>
            </li>

            <li class="dropdown">
                <a href="#">Collection</a>
                <div class="dropdown-content">
                    <a href="<?= url('collection/amount') ?>">Amount Collection</a>
                    <a href="<?= url('collection/edit') ?>">Edit Collection</a>
                    <a href="<?= url('report/collectionReport') ?>">Collection Report</a>
                </div>
            </li>

            <li class="dropdown">
                <a href="#">Report</a>
                <div class="dropdown-content">
                    <a href="<?= url('report/dueList') ?>">Due List</a>
                    <a href="<?= url('report/inactiveList') ?>">Inactive List</a>
                    <a href="<?= url('report/customerSummary') ?>">Customer Summary</a>
                </div>
            </li>

            <li class="dropdown">
                <a href="#">Setup</a>
                <div class="dropdown-content">
                    <a href="#">OLT Setup</a>
                    <a href="#">Address Setup</a>
                    <a href="<?= url('setup/column-preview') ?>">Column Preview Setup</a>
                    <a href="#">Print Preview Setup</a>
                    <a href="<?= url('complain') ?>">Complain Setup</a>
                    <a href="<?= url('employee') ?>">Employee Setup</a>
                    <a href="<?= url('setup/package') ?>">Package Setup</a>
                    <a href="#">Payment Settings Setup</a>
                    <a href="#">SMS Setup</a>
                    <a href="<?= url('prefix') ?>">ID Prefix Setup</a>
                </div>
            </li>

            <li class="dropdown">
                <a href="#">Mikrotik</a>
                <div class="dropdown-content">
                    <a href="#">Router Config</a>
                    <a href="#">User List</a>
                </div>
            </li>

            <li class="dropdown">
                <a href="#">Reseller</a>
                <div class="dropdown-content">
                    <a href="#">Reseller List</a>
                    <a href="#">Reseller Package</a>
                    <a href="#">Reseller Balance</a>
                    <a href="#">Reseller Balance Summary</a>
                </div>
            </li>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Super Admin'): ?>
                <li><a href="<?= url('user') ?>" class="<?= $path === '/users' ? 'active' : '' ?>">Users</a></li>
            <?php endif; ?>
        </ul>

        <style>
            .nav-profile.dropdown {
                position: relative;
            }

            .profile-trigger {
                text-decoration: none;
                color: #1e293b;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 6px 12px;
                border-radius: 50px;
                transition: all 0.2s ease;
                background: rgba(241, 245, 249, 0.5);
            }

            .profile-trigger:hover {
                background: #f1f5f9;
            }

            /* Disable hover for profile dropdown specifically */
            .nav-profile.dropdown:hover .dropdown-content {
                display: none;
            }

            .nav-profile.dropdown.active .dropdown-content {
                display: block;
                animation: fadeIn 0.1s ease-out;
            }

            .profile-avatar {
                width: 32px;
                height: 32px;
                background: var(--primary);
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
                font-weight: 700;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .profile-dropdown {
                right: 0;
                min-width: 240px;
                padding: 8px !important;
                border-radius: 16px !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
                margin-top: 8px !important;
            }

            .dropdown-header {
                padding: 12px 16px;
                margin-bottom: 8px;
                border-bottom: 1px solid #f1f5f9;
            }

            .header-name {
                display: block;
                font-weight: 700;
                color: #1e293b;
                font-size: 0.95rem;
                line-height: 1.2;
            }

            .header-role {
                display: block;
                font-size: 0.75rem;
                color: #64748b;
                margin-top: 2px;
                font-weight: 500;
            }

            .profile-dropdown a {
                display: block !important;
                padding: 10px 16px !important;
                border-radius: 8px !important;
                color: #475569 !important;
                font-weight: 500 !important;
                font-size: 0.9rem !important;
                text-decoration: none !important;
                transition: all 0.2s ease !important;
            }

            .profile-dropdown a:hover {
                background: #f1f5f9 !important;
                color: var(--primary) !important;
                transform: none !important;
            }

            .dropdown-divider {
                height: 1px;
                background: #f1f5f9;
                margin: 4px 0;
            }
        </style>

        <div class="nav-profile dropdown" id="userDropdown">
            <a href="javascript:void(0)" class="profile-trigger" onclick="toggleDropdown(event)">
                <div class="profile-avatar">
                    <?php
                    $name = $_SESSION['display_name'] ?? 'Guest';
                    echo strtoupper(substr($name, 0, 1));
                    ?>
                </div>
                <span><?= htmlspecialchars($name) ?></span>
            </a>
            <div class="dropdown-content profile-dropdown">
                <div class="dropdown-header">
                    <span class="header-name"><?= htmlspecialchars($name) ?></span>
                    <span class="header-role"><?= htmlspecialchars($_SESSION['role'] ?? 'User') ?></span>
                </div>

                <a href="<?= url('user/profile') ?>">My Profile</a>
                <a href="<?= url('user/activity') ?>">Activity Logs</a>
                <a href="<?= url('user/changePassword') ?>">Change Password</a>

                <div class="dropdown-divider"></div>

                <a href="<?= url('auth/logout') ?>" class="logout-item">Logout</a>
            </div>
        </div>

        <script>
            function toggleDropdown(e) {
                e.preventDefault();
                e.stopPropagation();
                document.getElementById('userDropdown').classList.toggle('active');
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                const dropdown = document.getElementById('userDropdown');
                if (dropdown && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });
        </script>
    </nav>