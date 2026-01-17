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

    <style>.sort-header {
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

    /* Import Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2000;
    }

    .modal-content {
        background: white;
        padding: 25px;
        border-radius: 12px;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 15px;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #64748b;
    }

    .mapping-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .mapping-table th {
        text-align: left;
        padding: 10px;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        color: #475569;
        font-weight: 600;
    }

    .mapping-table td {
        padding: 10px;
        border-bottom: 1px solid #f1f5f9;
    }

    .mapping-select {
        width: 100%;
        padding: 8px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
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

                <!-- Export Dropdown -->
                <div class="column-selector-wrapper no-print">
                    <button type="button" class="btn-secondary" id="exportBtn" style="padding: 8px 15px;">
                        <i class="fas fa-file-export"></i> Export
                    </button>
                    <div class="column-picker-dropdown" id="exportDropdown"
                        style="left: auto; right: 0; min-width: 140px;">
                        <div class="export-option" onclick="exportTable('customerTable', 'excel', 'All_Customers')">
                            <i class="fas fa-file-excel" style="color: #16a34a;"></i> Excel
                        </div>
                        <div class="export-option" onclick="exportTable('customerTable', 'csv', 'All_Customers')">
                            <i class="fas fa-file-csv" style="color: #0d9488;"></i> CSV
                        </div>
                        <div class="export-option" onclick="exportTable('customerTable', 'pdf', 'All_Customers', [8])">
                            <i class="fas fa-file-pdf" style="color: #ef4444;"></i> PDF
                        </div>
                    </div>
                </div>

                <!-- Import Button -->
                <button type="button" class="btn-secondary no-print"
                    onclick="document.getElementById('importFile').click()" style="padding: 8px 15px;">
                    <i class="fas fa-file-import"></i> Import
                </button>
                <input type="file" id="importFile" accept=".xlsx, .xls" style="display: none;"
                    onchange="openImportModal(this)">

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

<!-- Import Mapping Modal -->
<div class="modal-overlay" id="importModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Map Excel Columns</div>
            <button class="btn-close" onclick="closeModal()">&times;</button>
        </div>
        <div style="margin-bottom: 15px; color: #64748b; font-size: 0.9rem;">
            Match your Excel headers (left) to the database fields (right). Unmatched columns will be ignored.
        </div>
        <table class="mapping-table">
            <thead>
                <tr>
                    <th>Excel Header</th>
                    <th>Database Field</th>
                    <th>Preview (Row 1)</th>
                </tr>
            </thead>
            <tbody id="mappingBody"></tbody>
        </table>
        <div style="margin-top: 25px; text-align: right; gap: 10px; display: flex; justify-content: flex-end;">
            <button class="btn-secondary" onclick="closeModal()">Cancel</button>
            <button class="btn-collect" id="confirmImportBtn" onclick="processImport()">
                Start Import
            </button>
        </div>
    </div>
</div>

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

    // --- Interactive Import Logic ---

    // DB Fields Config
    const dbFields = [
        { value: '', label: '-- Ignore --' },
        { value: 'full_name', label: 'Customer Name *' },
        { value: 'mobile_no', label: 'Mobile No *' },
        { value: 'payment_id', label: 'Payment ID / User ID' },
        { value: 'identification_no', label: 'Identification No' },
        { value: 'email', label: 'Email' },
        { value: 'district', label: 'District' },
        { value: 'thana', label: 'Thana' },
        { value: 'area', label: 'Area' },
        { value: 'house_no', label: 'House/Flat/Address' },
        { value: 'monthly_rent', label: 'Monthly Rent' },
        { value: 'due_amount', label: 'Due Amount' },
        { value: 'advance_amount', label: 'Advance Amount' },
        { value: 'additional_charge', label: 'Additional Charge' },
        { value: 'vat_percent', label: 'VAT (%)' },
        { value: 'discount', label: 'Discount' },
        { value: 'pppoe_name', label: 'PPPoE Username' },
        { value: 'pppoe_password', label: 'PPPoE Password' },
        { value: 'ip_address', label: 'IP Address' },
        { value: 'mac_address', label: 'MAC Address' },
        { value: 'package_id', label: 'Package Name' },
        { value: 'status', label: 'Status' },
        { value: 'comment', label: 'Comment/Remarks' }
    ];

    // Alias Map for Auto-Detection
    const fieldAliases = {
        'full_name': ['name', 'customer', 'client'],
        'mobile_no': ['mobile', 'phone', 'cell', 'contact'],
        'payment_id': ['payment', 'id', 'user_id', 'client_id'],
        'email': ['mail', 'e-mail'],
        'area': ['location', 'zone'],
        'monthly_rent': ['rent', 'bill', 'price', 'amount'],
        'due_amount': ['due', 'outstanding'],
        'advance_amount': ['advance', 'prepaid'],
        'additional_charge': ['additional', 'charge', 'extra'],
        'vat_percent': ['vat', 'tax'],
        'package_id': ['package', 'plan'],
        'ip_address': ['ip', 'address'],
        'pppoe_name': ['pppoe', 'username', 'user']
    };

    let globalImportData = [];
    let globalHeaders = [];

    function openImportModal(input) {
        if (!input.files || input.files.length === 0) return;

        importExcel(input, function (data, error) {
            input.value = ''; // Reset for re-selection

            if (error) {
                alert('Error reading file: ' + error.message);
                return;
            }
            if (!data || data.length === 0) {
                alert('File is empty.');
                return;
            }

            globalImportData = data;
            // Extract headers from first row keys
            globalHeaders = Object.keys(data[0]);

            renderMappingTable();
            document.getElementById('importModal').style.display = 'flex';
        });
    }

    function closeModal() {
        document.getElementById('importModal').style.display = 'none';
        globalImportData = [];
    }

    function renderMappingTable() {
        const tbody = document.getElementById('mappingBody');
        tbody.innerHTML = '';

        const firstRow = globalImportData[0];

        globalHeaders.forEach((header, index) => {
            const tr = document.createElement('tr');

            // Auto-detect match
            let matchedField = '';
            const headerLower = header.toLowerCase().trim();

            for (const [dbField, aliases] of Object.entries(fieldAliases)) {
                // Check direct match
                if (dbField === headerLower || dbField.replace('_', ' ') === headerLower) {
                    matchedField = dbField;
                    break;
                }
                // Check aliases
                if (aliases.some(alias => headerLower.includes(alias))) {
                    matchedField = dbField;
                    break;
                }
            }

            // Create Select Options
            let optionsHtml = '';
            dbFields.forEach(f => {
                const selected = (f.value === matchedField) ? 'selected' : '';
                optionsHtml += `<option value="${f.value}" ${selected}>${f.label}</option>`;
            });

            tr.innerHTML = `
                <td><strong>${header}</strong></td>
                <td>
                    <select class="mapping-select" data-header="${header}">
                        ${optionsHtml}
                    </select>
                </td>
                <td style="color: #64748b; font-size: 0.9em;">
                    ${firstRow[header] || '-'}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function processImport() {
        const selects = document.querySelectorAll('.mapping-select');
        const map = {}; // Header -> DB Field

        selects.forEach(sel => {
            if (sel.value) {
                map[sel.dataset.header] = sel.value;
            }
        });

        // check requirements
        const mappedValues = Object.values(map);
        if (!mappedValues.includes('full_name') || !mappedValues.includes('mobile_no')) {
            alert('Error: You must map "Customer Name" and "Mobile No" fields.');
            return;
        }

        const btn = document.getElementById('confirmImportBtn');
        const originalText = btn.innerText;
        btn.innerText = 'Importing...';
        btn.disabled = true;

        // Transform Data
        const transformedData = globalImportData.map(row => {
            const newRow = {};
            for (const [header, dbField] of Object.entries(map)) {
                newRow[dbField] = row[header];
            }
            return newRow;
        });

        // Send to Server
        fetch('<?= url('customer/import') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ customers: transformedData })
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Import Results:\n' + response.message);
                    if (response.errors && response.errors.length > 0) {
                        console.log(response.errors);
                    }
                    btn.innerText = originalText;
                    btn.disabled = false;
                }
            })
            .catch(err => {
                alert('Server Error during import.');
                console.error(err);
                btn.innerText = originalText;
                btn.disabled = false;
            });
    }
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>