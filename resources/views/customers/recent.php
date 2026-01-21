<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/export_scripts.php'; ?>

<style>
    .sort-header {
        cursor: pointer;
        user-select: none;
        position: relative;
        padding-right: 20px !important;
    }

    .sort-header:hover {
        background: #f1f5f9;
    }

    .sort-arrows {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 10px;
        display: flex;
        flex-direction: column;
        color: #cbd5e1;
    }

    .sort-arrows .active {
        color: #3b82f6;
    }

    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 0;
        margin-top: 10px;
        border-top: 1px solid #e2e8f0;
    }

    .pagination-info {
        font-size: 14px;
        color: #64748b;
    }

    .pagination-links {
        display: flex;
        gap: 5px;
    }

    .page-link {
        padding: 5px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        text-decoration: none;
        color: #1e293b;
        font-size: 14px;
        transition: all 0.2s;
    }

    .page-link:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .page-link.active {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    .page-link.disabled {
        color: #cbd5e1;
        pointer-events: none;
        background: #f8fafc;
    }

    .export-option {
        padding: 8px 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #475569;
    }

    .export-option:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    /* Date Range Picker Styles */
    .date-range-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        background: white;
        border: 1px solid #e2e8f0;
        padding: 4px 12px;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .date-input-group {
        position: relative;
        display: flex;
        align-items: center;
    }

    .date-filter-input {
        border: none;
        outline: none;
        padding: 6px 10px;
        font-size: 14px;
        color: #1e293b;
        width: 140px;
        background: transparent;
        font-weight: 500;
        font-family: inherit;
    }

    .date-filter-input::placeholder {
        color: #94a3b8;
    }

    .date-icon {
        position: absolute;
        left: 0;
        color: #64748b;
        font-size: 12px;
        pointer-events: none;
    }

    .separator {
        color: #94a3b8;
        font-size: 13px;
        font-weight: 500;
    }
</style>

<main class="dashboard-container">
    <div class="card">
        <div class="search-header"
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
            <h2>Recent Customers <span style="font-size:16px; color:#666; font-weight:normal;">(Total:
                    <?= $totalRecords ?? 0 ?>)</span></h2>

            <form method="GET" action="<?= url('customer/recent') ?>" class="search-box"
                style="display:flex; gap:10px; align-items:center; flex-wrap: wrap;">

                <!-- Date Filter -->
                <div style="display:flex; align-items:center; gap:8px;" class="no-print">
                    <div style="display:flex; align-items:center; gap:5px;">
                        <span style="color:#64748b; font-size:14px; font-weight:500;">From:</span>
                        <div class="date-input-group"
                            style="background:white; border:1px solid #e2e8f0; border-radius:6px; padding:4px 8px;">
                            <input type="text" name="start_date" value="<?= htmlspecialchars($startDate) ?>"
                                class="date-filter date-filter-input" placeholder="dd/mm/yyyy"
                                style="width:140px; padding-left:0;">
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; gap:5px;">
                        <span style="color:#64748b; font-size:14px; font-weight:500;">To:</span>
                        <div class="date-input-group"
                            style="background:white; border:1px solid #e2e8f0; border-radius:6px; padding:4px 8px;">
                            <input type="text" name="end_date" value="<?= htmlspecialchars($endDate) ?>"
                                class="date-filter date-filter-input" placeholder="dd/mm/yyyy"
                                style="width:140px; padding-left:0;">
                        </div>
                    </div>
                </div>

                <input type="text" name="q" placeholder="Search..." value="<?= htmlspecialchars($q ?? '') ?>"
                    style="padding: 8px; width: 200px; border: 1px solid #ccc; border-radius: 4px;" class="no-print">

                <button type="submit"
                    style="padding: 8px 15px; background:#3b82f6; color:white; border:none; border-radius:4px; cursor:pointer;"
                    class="no-print">Filter</button>

                <?php if (!empty($q) || $startDate != date('Y-m-01') || $endDate != date('Y-m-d')): ?>
                    <a href="<?= url('customer/recent') ?>" style="color:#ef4444; text-decoration:none; font-size:14px;"
                        class="no-print">Reset</a>
                <?php endif; ?>

                <!-- Export Dropdown -->
                <div class="column-selector-wrapper no-print">
                    <button type="button" class="btn-secondary" id="exportBtn" style="padding: 8px 15px;">
                        <i class="fas fa-file-export"></i> Export
                    </button>
                    <div class="column-picker-dropdown" id="exportDropdown"
                        style="left: auto; right: 0; min-width: 140px;">
                        <div class="export-option" onclick="exportTable('customerTable', 'excel', 'Recent_Customers')">
                            <i class="fas fa-file-excel" style="color: #16a34a;"></i> Excel
                        </div>
                        <div class="export-option" onclick="exportTable('customerTable', 'csv', 'Recent_Customers')">
                            <i class="fas fa-file-csv" style="color: #0d9488;"></i> CSV
                        </div>
                        <div class="export-option"
                            onclick="exportTable('customerTable', 'pdf', 'Recent_Customers', [])">
                            <i class="fas fa-file-pdf" style="color: #ef4444;"></i> PDF
                        </div>
                    </div>
                </div>

                <button type="button" onclick="window.print()" class="btn-secondary no-print"
                    style="padding: 8px 15px;">
                    <i class="fas fa-print"></i> Print
                </button>
            </form>
        </div>

        <?php
        // Helper for sort URLs (Persist search & filter)
        function sortUrl($column, $currentSort, $currentOrder)
        {
            $newOrder = ($column === $currentSort && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
            $params = $_GET;
            $params['sort'] = $column;
            $params['order'] = $newOrder;
            $params['page'] = 1; // Reset to page 1 on sort
            return url('customer/recent?' . http_build_query($params));
        }

        // Helper for arrow classes
        function arrowClass($column, $direction, $currentSort, $currentOrder)
        {
            return ($column === $currentSort && strtoupper($direction) === $currentOrder) ? 'active' : '';
        }
        ?>

        <div class="table-container">
            <table class="data-table" id="customerTable">
                <thead>
                    <tr>
                        <?php foreach ($tableColumns as $col): ?>
                            <?php
                            $key = $col['key'];
                            $label = $col['label'];
                            ?>
                            <th class="sort-header" onclick="location.href='<?= sortUrl($key, $sort, $order) ?>'">
                                <?= htmlspecialchars($label) ?>
                                <div class="sort-arrows">
                                    <i class="fas fa-caret-up <?= arrowClass($key, 'ASC', $sort, $order) ?>"></i>
                                    <i class="fas fa-caret-down <?= arrowClass($key, 'DESC', $sort, $order) ?>"></i>
                                </div>
                            </th>
                        <?php endforeach; ?>
                        <th class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <?php foreach ($tableColumns as $col): ?>
                                    <?php $key = $col['key']; ?>
                                    <td>
                                        <?php if ($key === 'id'): ?>
                                            <?= htmlspecialchars($customer['prefix_code'] ?? '') ?>                 <?= $customer['id'] ?>
                                        <?php elseif ($key === 'full_name'): ?>
                                            <strong><?= htmlspecialchars($customer['full_name']) ?></strong><br>
                                            <small style="color:#666"><?= htmlspecialchars($customer['company_name'] ?? '') ?></small>
                                        <?php elseif ($key === 'status'): ?>
                                            <?php
                                            $statusVal = $customer['status'] ?? 'active';
                                            $color = 'green';
                                            $label = 'Active';
                                            if ($statusVal === 'pending') {
                                                $color = 'orange';
                                                $label = 'Pending';
                                            } elseif ($statusVal === 'inactive') {
                                                $color = 'red';
                                                $label = 'Inactive';
                                            } elseif ($statusVal === 'temp_disable') {
                                                $color = 'gray';
                                                $label = 'T. Disable';
                                            } elseif ($statusVal === 'free') {
                                                $color = 'blue';
                                                $label = 'Free';
                                            }
                                            ?>
                                            <span style="color:<?= $color ?>; font-weight:bold;"><?= $label ?></span>
                                        <?php elseif (in_array($key, ['monthly_rent', 'due_amount', 'total_amount', 'additional_charge', 'discount', 'advance_amount', 'vat_percent', 'security_deposit'])): ?>
                                            <?= number_format($customer[$key] ?? 0, 2) ?>
                                        <?php elseif (in_array($key, ['created_at', 'connection_date', 'expire_date', 'entry_date'])): ?>
                                            <?php
                                            $dateVal = $customer[$key === 'entry_date' ? 'created_at' : $key] ?? null;
                                            echo $dateVal ? date('d M Y', strtotime($dateVal)) : '-';
                                            ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($customer[$key] ?? '-') ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>

                                <td class="no-print">
                                    <a href="<?= url('customer/show/' . $customer['id']) ?>" class="btn-table"
                                        style="background:#3b82f6; text-decoration:none;">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= count($tableColumns) + 1 ?>" style="text-align:center;">No recent customers
                                found within this date range.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-container no-print">
                <div class="pagination-info">
                    Showing Page <?= $currentPage ?> of <?= $totalPages ?>
                </div>
                <div class="pagination-links">
                    <?php
                    // Helper for page URLs
                    if (!function_exists('pageUrl')) {
                        function pageUrl($pageNum)
                        {
                            $params = $_GET;
                            $params['page'] = $pageNum;
                            return url('customer/recent?' . http_build_query($params));
                        }
                    }
                    ?>

                    <a href="<?= pageUrl(1) ?>" class="page-link <?= $currentPage == 1 ? 'disabled' : '' ?>"
                        title="First Page">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    <a href="<?= pageUrl($currentPage - 1) ?>" class="page-link <?= $currentPage == 1 ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-left"></i>
                    </a>

                    <?php
                    // Determine range of pages to show
                    $start = max(1, $currentPage - 2);
                    $end = min($totalPages, $currentPage + 2);

                    for ($i = $start; $i <= $end; $i++):
                        ?>
                        <a href="<?= pageUrl($i) ?>" class="page-link <?= $currentPage == $i ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <a href="<?= pageUrl($currentPage + 1) ?>"
                        class="page-link <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="<?= pageUrl($totalPages) ?>"
                        class="page-link <?= $currentPage == $totalPages ? 'disabled' : '' ?>" title="Last Page">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
    // Export Dropdown Logic
    document.addEventListener('DOMContentLoaded', function () {
        const exportBtn = document.getElementById('exportBtn');
        const exportDropdown = document.getElementById('exportDropdown');

        if (exportBtn && exportDropdown) {
            exportBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                exportDropdown.classList.toggle('active');
            });

            document.addEventListener('click', () => {
                exportDropdown.classList.remove('active');
            });
        }
    });

    // Custom Date Picker for Recent Page (d/m/Y display)
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr(".date-filter", {
            dateFormat: "Y-m-d", // Send to server
            altInput: true,
            altFormat: "d/m/Y",  // Display to user
            allowInput: true
        });
    });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>