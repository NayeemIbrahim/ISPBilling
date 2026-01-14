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
                <a href="#">Admin</a>
                <div class="dropdown-content">
                    <a href="#">Employee List</a>
                    <a href="#">Package Settings</a>
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
        </ul>

        <div class="nav-profile">
            <span>Admin User</span>
            <a href="<?= url('logout') ?>" class="btn-sm">Logout</a>
        </div>
    </nav>