<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
    .report-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        margin-top: 20px;
        overflow: hidden;
    }

    .report-header {
        padding: 25px 30px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
    }

    .report-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .report-title i {
        color: #2563eb;
        background: #eff6ff;
        padding: 8px;
        border-radius: 8px;
        font-size: 1rem;
    }

    .filter-section {
        padding: 25px 30px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }

    .filter-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .filter-row {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .form-group-custom {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group-custom label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        font-weight: 600;
    }

    .form-control-custom {
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #334155;
        background: #fff;
        transition: all 0.2s;
        min-width: 150px;
    }

    .form-control-custom:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-filter {
        background: #3b82f6;
        color: #fff;
        border: none;
        padding: 9px 20px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-filter:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }

    .btn-reset {
        padding: 9px 16px;
        color: #64748b;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        transition: all 0.2s;
        height: 38px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-reset:hover {
        background: #f1f5f9;
        color: #475569;
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .custom-table th {
        background: #f8fafc;
        padding: 12px 15px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
        text-align: left;
    }

    .custom-table td {
        padding: 14px 15px;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        background: #fff;
    }

    .custom-table tr:hover td {
        background: #f8fafc;
    }

    .col-id {
        font-family: 'Inter', monospace;
        background: #f1f5f9;
        padding: 4px 8px;
        border-radius: 4px;
        color: #475569;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-status {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .preview-card {
        border: 2px solid #e0f2fe;
        background: #f0f9ff;
        border-radius: 12px;
        overflow: hidden;
        position: sticky;
        top: 80px;
    }

    .preview-header {
        background: #e0f2fe;
        padding: 15px 20px;
        color: #0369a1;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<div class="dashboard-container">
    <div class="report-card">
        <div class="report-header">
            <div class="report-title">
                <i class="fas fa-map-marker-alt"></i>
                Area Wise Receipt
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="button" class="btn-filter" style="background: #3b82f6; min-width: 140px;" onclick="showSelectedReceipt()">
                    <i class="fas fa-eye"></i> Show Receipt
                </button>
                <button type="button" class="btn-filter" style="background: #10b981; min-width: 100px;" onclick="printSelected()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" action="<?= url('receipt/area-wise') ?>" class="filter-form">
                <div class="filter-row">
                    <div class="form-group-custom">
                        <label>Area</label>
                        <select name="area" class="form-control-custom">
                            <option value="">All Areas</option>
                            <?php foreach ($areas as $area): ?>
                                <option value="<?= htmlspecialchars($area) ?>" <?= $selectedArea == $area ? 'selected' : '' ?>><?= htmlspecialchars($area) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group-custom">
                        <label>Package</label>
                        <select name="package_id" class="form-control-custom">
                            <option value="">All Packages</option>
                            <?php foreach ($packages as $pkg): ?>
                                <option value="<?= $pkg['id'] ?>" <?= $selectedPackage == $pkg['id'] ? 'selected' : '' ?>><?= htmlspecialchars($pkg['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group-custom">
                        <label>Status</label>
                        <select name="status" class="form-control-custom">
                            <option value="">All Statuses</option>
                            <option value="active" <?= $selectedStatus == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $selectedStatus == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="pending" <?= $selectedStatus == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="temp_disable" <?= $selectedStatus == 'temp_disable' ? 'selected' : '' ?>>Temp Disable</option>
                            <option value="free" <?= $selectedStatus == 'free' ? 'selected' : '' ?>>Free</option>
                        </select>
                    </div>
                    <div class="form-group-custom">
                        <label>Select Employee</label>
                        <select name="connected_by" class="form-control-custom">
                            <option value="">All Employees</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= htmlspecialchars($emp['display_name']) ?>" <?= $selectedEmployee == $emp['display_name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($emp['display_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group-custom">
                        <label>Billing Month</label>
                        <input type="month" id="billing-month" name="month" class="form-control-custom" value="<?= $_GET['month'] ?? date('Y-m') ?>">
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-filter"></i> Apply
                        </button>
                        <a href="<?= url('receipt/area-wise') ?>" class="btn-reset">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="row" style="padding: 0; margin: 0;">
            <div class="col-lg-7" style="padding: 0; border-right: 1px solid #f1f5f9;">
                <div style="overflow-x: auto;">
                    <table class="custom-table" id="receiptTable">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">
                                    <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)" style="width: 16px; height: 16px; cursor: pointer;">
                                </th>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>PPPoE Name</th>
                                <th>Rent</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($customers)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 100px 20px; color: #94a3b8;">
                                        <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 16px; display: block; opacity: 0.1;"></i>
                                        No customers found for these filters.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($customers as $cust): ?>
                                    <tr style="cursor: pointer;" onclick="toggleRow(event, this)">
                                        <td style="text-align: center;" onclick="event.stopPropagation()">
                                            <input type="checkbox" class="cust-checkbox" value="<?= $cust['id'] ?>" style="width: 16px; height: 16px; cursor: pointer;">
                                        </td>
                                        <td><span class="col-id"><?= htmlspecialchars(($cust['prefix_code'] ?? '') . $cust['id']) ?></span></td>
                                        <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($cust['full_name']) ?></td>
                                        <td style="color: #64748b;"><?= htmlspecialchars($cust['pppoe_name']) ?></td>
                                        <td style="font-weight: 700; font-family: monospace;"><?= number_format($cust['monthly_rent'], 2) ?></td>
                                        <td>
                                            <?php
                                            $statusStyle = $cust['status'] == 'active' ? 'background: #f0fdf4; color: #16a34a;' : 
                                                          ($cust['status'] == 'pending' ? 'background: #fffbeb; color: #d97706;' : 'background: #fef2f2; color: #dc2626;');
                                            ?>
                                            <span class="badge-status" style="<?= $statusStyle ?>">
                                                <?= ucfirst($cust['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-5" style="padding: 25px;">
                <div class="preview-card" id="previewContainer">
                    <div class="preview-header">
                        <i class="fas fa-file-invoice"></i>
                        Receipt Preview
                    </div>
                    <div id="receiptPreviewContent" style="padding: 20px; min-height: 400px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #64748b; text-align: center;">
                        <i class="fas fa-arrow-left" style="font-size: 2.5rem; margin-bottom: 15px; opacity: 0.1;"></i>
                        <p>Select customers and click <strong>Show Receipt</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSelectAll(source) {
    const checkboxes = document.getElementsByClassName('cust-checkbox');
    for (let i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
        const row = checkboxes[i].closest('tr');
        if (row) {
            if (source.checked) row.style.background = '#f8fafc';
            else row.style.background = '';
        }
    }
}

function toggleRow(event, row) {
    const checkbox = row.querySelector('.cust-checkbox');
    if (checkbox) {
        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) row.style.background = '#f8fafc';
        else row.style.background = '';
    }
}

function getSelectedIds() {
    const checkboxes = document.getElementsByClassName('cust-checkbox');
    const selectedIds = [];
    for (let i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            selectedIds.push(checkboxes[i].value);
        }
    }
    return selectedIds;
}

function showSelectedReceipt() {
    const ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Please select at least one customer.');
        return;
    }

    const month = document.getElementById('billing-month').value;
    const preview = document.getElementById('receiptPreviewContent');
    preview.innerHTML = '<div style="text-align: center;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #3b82f6;"></i><p style="margin-top:10px;">Loading Preview...</p></div>';

    const iframeUrl = '<?= url("receipt/print") ?>?ids=' + ids.join(',') + '&month=' + month + '&preview=1';
    preview.innerHTML = `
        <iframe src="${iframeUrl}" style="width: 100%; height: 550px; border: none; border-radius: 8px; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.05);"></iframe>
        <div style="margin-top: 15px; width: 100%;">
            <button onclick="printSelected()" class="btn-filter" style="background: #10b981; width: 100%; justify-content: center;">
                <i class="fas fa-print"></i> Open Print Window
            </button>
        </div>
    `;
}

function printSelected() {
    const ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Please select at least one customer.');
        return;
    }

    const month = document.getElementById('billing-month').value;
    window.open('<?= url("receipt/print") ?>?ids=' + ids.join(',') + '&month=' + month, '_blank');
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
