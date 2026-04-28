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
        min-width: 200px;
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

    .loader-inline {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin-left: 5px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div class="dashboard-container">
    <div class="report-card">
        <div class="report-header">
            <div class="report-title">
                <i class="fas fa-user-tag"></i>
                Customer Wise Receipt
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
            <div class="filter-form">
                <div class="filter-row">
                    <div class="form-group-custom" style="flex: 1; min-width: 300px;">
                        <label>Search Customer (Name, Mobile, Username, or ID)</label>
                        <div style="position: relative;">
                            <input type="text" id="instant-search" class="form-control-custom" placeholder="Type info to search instantly..." value="<?= htmlspecialchars($search) ?>" autocomplete="off" style="width: 100%;">
                            <div id="search-spinner" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); display: none;">
                                <i class="fas fa-circle-notch fa-spin" style="color: #3b82f6;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-custom">
                        <label>Billing Month (Optional)</label>
                        <input type="month" id="billing-month" class="form-control-custom" value="<?= htmlspecialchars($selectedMonth) ?>">
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="<?= url('receipt/customer-wise') ?>" class="btn-reset">
                            <i class="fas fa-undo"></i> Clear
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="padding: 0; margin: 0;">
            <div class="col-lg-7" style="padding: 0; border-right: 1px solid #f1f5f9;">
                <div style="overflow-x: auto;">
                    <table class="custom-table" id="customerTable">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">
                                    <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)" style="width: 16px; height: 16px; cursor: pointer;">
                                </th>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>PPPoE Name</th>
                                <th>Rent</th>
                            </tr>
                        </thead>
                        <tbody id="customer-list">
                            <?php if (empty($customers)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 100px 20px; color: #94a3b8;">
                                        <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 16px; display: block; opacity: 0.1;"></i>
                                        Search and select a customer.
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
                        <p>Select customer and click <strong>Show Receipt</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let searchTimeout;

document.getElementById('instant-search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const q = this.value.trim();
    const spinner = document.getElementById('search-spinner');
    const list = document.getElementById('customer-list');

    if (q.length < 2) {
        // Optional: clear list or keep old results
        return;
    }

    spinner.style.display = 'block';

    searchTimeout = setTimeout(() => {
        fetch(`<?= url('receipt/search-ajax') ?>?q=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(data => {
                spinner.style.display = 'none';
                list.innerHTML = '';
                if (data.length === 0) {
                    list.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:50px;">No records found.</td></tr>';
                    return;
                }
                data.forEach(c => {
                    const tr = document.createElement('tr');
                    tr.style.cursor = 'pointer';
                    tr.onclick = (e) => toggleRow(e, tr);
                    tr.innerHTML = `
                        <td style="text-align: center;" onclick="event.stopPropagation()">
                            <input type="checkbox" class="cust-checkbox" value="${c.id}" style="width: 16px; height: 16px; cursor: pointer;">
                        </td>
                        <td><span class="col-id">${c.prefix_code || ''}${c.id}</span></td>
                        <td style="font-weight: 600; color: #1e293b;">${c.full_name}</td>
                        <td style="color: #64748b;">${c.pppoe_name || ''}</td>
                        <td style="font-weight: 700; font-family: monospace;">${parseFloat(c.monthly_rent).toFixed(2)}</td>
                    `;
                    list.appendChild(tr);
                });
            })
            .catch(err => {
                spinner.style.display = 'none';
                console.error(err);
            });
    }, 300);
});

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
