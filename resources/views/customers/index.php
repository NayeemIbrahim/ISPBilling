<?php include __DIR__ . '/../partials/header.php'; ?>

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
</style>

<main class="dashboard-container">
    <div class="card">
        <div class="search-header"
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>All Customers <span style="font-size:16px; color:#666; font-weight:normal;">(Total:
                    <?= $totalRecords ?? 0 ?>)</span></h2>

            <form method="GET" action="<?= url('customer') ?>" class="search-box"
                style="display:flex; gap:10px; align-items:center;">
                <!-- Status Checkboxes -->
                <div style="display:flex; gap:10px; font-size:14px;" class="no-print">
                    <label style="cursor:pointer;">
                        <input type="checkbox" name="status[]" value="active" onchange="this.form.submit()"
                            <?= in_array('active', $statuses ?? []) ? 'checked' : '' ?>> Active
                    </label>
                    <label style="cursor:pointer;">
                        <input type="checkbox" name="status[]" value="inactive" onchange="this.form.submit()"
                            <?= in_array('inactive', $statuses ?? []) ? 'checked' : '' ?>> Inactive
                    </label>
                    <label style="cursor:pointer;">
                        <input type="checkbox" name="status[]" value="temp_disable" onchange="this.form.submit()"
                            <?= in_array('temp_disable', $statuses ?? []) ? 'checked' : '' ?>> T. Disable
                    </label>
                </div>

                <input type="text" name="q" placeholder="Search..." value="<?= htmlspecialchars($q ?? '') ?>"
                    style="padding: 8px; width: 200px; border: 1px solid #ccc; border-radius: 4px;" class="no-print">
                <button type="submit"
                    style="padding: 8px 15px; background:#3b82f6; color:white; border:none; border-radius:4px; cursor:pointer;"
                    class="no-print">Search</button>
                <?php if (!empty($q) || !empty($statuses)): ?>
                    <a href="<?= url('customer') ?>" style="color:#ef4444; text-decoration:none; font-size:14px;"
                        class="no-print">Clear</a>
                <?php endif; ?>

                <div class="column-selector-wrapper no-print">
                    <button type="button" class="btn-secondary" id="colPickerBtn" style="padding: 8px 15px;">
                        <i class="fas fa-columns"></i> Columns
                    </button>
                    <div class="column-picker-dropdown" id="colPickerDropdown" style="left: auto; right: 0;">
                        <label><input type="checkbox" class="col-toggle" data-col="0" checked> ID</label>
                        <label><input type="checkbox" class="col-toggle" data-col="1" checked> Name</label>
                        <label><input type="checkbox" class="col-toggle" data-col="2" checked> Mobile</label>
                        <label><input type="checkbox" class="col-toggle" data-col="3" checked> Area</label>
                        <label><input type="checkbox" class="col-toggle" data-col="4" checked> Package</label>
                        <label><input type="checkbox" class="col-toggle" data-col="5" checked> Payment ID</label>
                        <label><input type="checkbox" class="col-toggle" data-col="6" checked> Due</label>
                        <label><input type="checkbox" class="col-toggle" data-col="7" checked> Status</label>
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
            return url('customer?' . http_build_query($params));
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
                        <th class="sort-header" onclick="location.href='<?= sortUrl('id', $sort, $order) ?>'">
                            ID
                            <div class="sort-arrows">
                                <i class="fas fa-caret-up <?= arrowClass('id', 'ASC', $sort, $order) ?>"></i>
                                <i class="fas fa-caret-down <?= arrowClass('id', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th class="sort-header" onclick="location.href='<?= sortUrl('full_name', $sort, $order) ?>'">
                            Name
                            <div class="sort-arrows">
                                <i class="fas fa-caret-up <?= arrowClass('full_name', 'ASC', $sort, $order) ?>"></i>
                                <i class="fas fa-caret-down <?= arrowClass('full_name', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th class="sort-header" onclick="location.href='<?= sortUrl('mobile_no', $sort, $order) ?>'">
                            Mobile
                            <div class="sort-arrows">
                                <i class="fas fa-caret-up <?= arrowClass('mobile_no', 'ASC', $sort, $order) ?>"></i>
                                <i class="fas fa-caret-down <?= arrowClass('mobile_no', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th class="sort-header" onclick="location.href='<?= sortUrl('area', $sort, $order) ?>'">
                            Area
                            <div class="sort-arrows">
                                <i class="fas fa-caret-up <?= arrowClass('area', 'ASC', $sort, $order) ?>"></i>
                                <i class="fas fa-caret-down <?= arrowClass('area', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th class="sort-header" onclick="location.href='<?= sortUrl('package_name', $sort, $order) ?>'">
                            Package
                            <div class="sort-arrows">
                                <i class="fas fa-caret-up <?= arrowClass('package_name', 'ASC', $sort, $order) ?>"></i>
                                <i
                                    class="fas fa-caret-down <?= arrowClass('package_name', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th class="sort-header" onclick="location.href='<?= sortUrl('payment_id', $sort, $order) ?>'">
                            Payment ID
                            <div class="sort-arrows">
                                <i class="fas fa-caret-up <?= arrowClass('payment_id', 'ASC', $sort, $order) ?>"></i>
                                <i class="fas fa-caret-down <?= arrowClass('payment_id', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th class="sort-header" onclick="location.href='<?= sortUrl('due_amount', $sort, $order) ?>'">
                            Due
                            <div class="sort-arrows">
                                <i class="fas fa-caret-up <?= arrowClass('due_amount', 'ASC', $sort, $order) ?>"></i>
                                <i class="fas fa-caret-down <?= arrowClass('due_amount', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th class="sort-header" onclick="location.href='<?= sortUrl('status', $sort, $order) ?>'">
                            Status
                            <div class="sort-arrows">
                                <i class="fas fa-caret-up <?= arrowClass('status', 'ASC', $sort, $order) ?>"></i>
                                <i class="fas fa-caret-down <?= arrowClass('status', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?= htmlspecialchars($customer['prefix_code'] ?? '') ?><?= $customer['id'] ?></td>
                                <td>
                                    <strong><?= $customer['full_name'] ?></strong><br>
                                    <small style="color:#666"><?= $customer['company_name'] ?></small>
                                </td>
                                <td><?= $customer['mobile_no'] ?></td>
                                <td><?= $customer['area'] ?></td>
                                <td><?= $customer['package_name'] ?></td>
                                <td><?= $customer['payment_id'] ?? '-' ?></td>
                                <td><?= $customer['due_amount'] ?></td>
                                <td>
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
                                </td>
                                <td class="no-print">
                                    <a href="<?= url('customer/show/' . $customer['id']) ?>" class="btn-table"
                                        style="background:#3b82f6; text-decoration:none;">View/Edit</a>
                                    <form action="<?= url('customer/delete/' . $customer['id']) ?>" method="POST"
                                        style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                        <button type="submit" class="btn-table" style="background:#ef4444;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align:center;">No customers found.</td>
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
                            return url('customer?' . http_build_query($params));
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
    document.addEventListener('DOMContentLoaded', function () {
        const pickerBtn = document.getElementById('colPickerBtn');
        const pickerDropdown = document.getElementById('colPickerDropdown');
        const toggles = document.querySelectorAll('.col-toggle');
        const table = document.getElementById('customerTable');
        const STORAGE_KEY = 'customer_list_cols';

        // Toggle dropdown
        pickerBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            pickerDropdown.classList.toggle('active');
        });

        document.addEventListener('click', () => {
            pickerDropdown.classList.remove('active');
        });

        pickerDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Load saved preferences
        let preferences = JSON.parse(localStorage.getItem(STORAGE_KEY)) || {};

        toggles.forEach(checkbox => {
            const colIndex = checkbox.dataset.col;
            if (preferences[colIndex] === false) {
                checkbox.checked = false;
                toggleColumn(colIndex, false);
            }

            checkbox.addEventListener('change', function () {
                toggleColumn(colIndex, this.checked);
                preferences[colIndex] = this.checked;
                localStorage.setItem(STORAGE_KEY, JSON.stringify(preferences));
            });
        });

        function toggleColumn(index, show) {
            const rows = table.rows;
            for (let i = 0; i < rows.length; i++) {
                const cell = rows[i].cells[index];
                if (cell) {
                    if (show) {
                        cell.classList.remove('col-hidden');
                    } else {
                        cell.classList.add('col-hidden');
                    }
                }
            }
        }
    });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>