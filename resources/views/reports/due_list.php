<?php include __DIR__ . '/../partials/header.php'; ?>

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
        color: #ef4444;
        background: #fef2f2;
        padding: 8px;
        border-radius: 8px;
        font-size: 1rem;
    }

    .btn-print {
        background: #fff;
        border: 1px solid #cbd5e1;
        color: #475569;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-print:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #1e293b;
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
        min-width: 140px;
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
    }

    .btn-reset:hover {
        background: #f1f5f9;
        color: #475569;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        padding: 25px 30px;
        background: #fff;
    }

    .summary-card {
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: transform 0.2s;
    }

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .summary-icon.red {
        background: #fef2f2;
        color: #ef4444;
    }

    .summary-icon.blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    .summary-info {
        display: flex;
        flex-direction: column;
    }

    .summary-label {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 500;
    }

    .summary-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
    }

    .chart-container {
        padding: 0 30px 25px 30px;
        height: 300px;
    }

    .table-wrapper {
        padding: 25px 30px;
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
        padding: 16px 15px;
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
        padding: 4px 8px !important;
        border-radius: 4px;
        color: #475569 !important;
        font-size: 0.8rem !important;
        font-weight: 500;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 600;
        color: #0f172a;
    }

    .user-sub {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 2px;
    }

    .badge-due {
        background: #fee2e2;
        color: #991b1b;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        background: #eff6ff;
        color: #2563eb;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-action:hover {
        background: #dbeafe;
    }

    @media print {

        .filter-section,
        .btn-print,
        .btn-action,
        .chart-container {
            display: none !important;
        }
    }
</style>

<div class="dashboard-container">
    <div class="report-card">
        <div class="report-header">
            <div class="report-title">
                <i class="fas fa-exclamation-triangle"></i>
                Due List Report
            </div>
            <div style="display: flex; gap: 10px;">
                <button onclick="syncBalances()" class="btn-print" id="syncBtn">
                    <i class="fas fa-sync"></i> Sync All Balances
                </button>
                <div class="column-selector-wrapper no-print">
                    <button type="button" class="btn-print" id="colPickerBtn">
                        <i class="fas fa-columns"></i> Columns
                    </button>
                    <div class="column-picker-dropdown" id="colPickerDropdown" style="left: auto; right: 0;">
                        <label><input type="checkbox" class="col-toggle" data-col="0" checked> ID</label>
                        <label><input type="checkbox" class="col-toggle" data-col="1" checked> Customer</label>
                        <label><input type="checkbox" class="col-toggle" data-col="2" checked> Mobile</label>
                        <label><input type="checkbox" class="col-toggle" data-col="3" checked> Area</label>
                        <label><input type="checkbox" class="col-toggle" data-col="4" checked> Monthly Rent</label>
                        <label><input type="checkbox" class="col-toggle" data-col="5" checked> Due Amount</label>
                    </div>
                </div>
                <button onclick="window.print()" class="btn-print">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" action="<?= url('report/dueList') ?>" class="filter-form">
                <div class="filter-row">
                    <div class="form-group-custom">
                        <label>Filter by Expiry (Start)</label>
                        <input type="text" name="start_date" value="<?= $startDate ?>"
                            class="form-control-custom date-picker" placeholder="Optional">
                    </div>
                    <div class="form-group-custom">
                        <label>Filter by Expiry (End)</label>
                        <input type="text" name="end_date" value="<?= $endDate ?>"
                            class="form-control-custom date-picker" placeholder="Optional">
                    </div>
                    <div class="form-group-custom">
                        <label>Agent</label>
                        <select name="connected_by" class="form-control-custom">
                            <option value="">All Agents</option>
                            <?php foreach ($options['connectedByList'] as $opt): ?>
                                <option value="<?= htmlspecialchars($opt) ?>" <?= $filters['connected_by'] == $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group-custom">
                        <label>Area</label>
                        <select name="area" class="form-control-custom">
                            <option value="">All Areas</option>
                            <?php foreach ($options['areas'] as $opt): ?>
                                <option value="<?= htmlspecialchars($opt) ?>" <?= $filters['area'] == $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn-filter">Apply Filters</button>
                        <a href="<?= url('report/dueList') ?>" class="btn-reset">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-icon red"><i class="fas fa-money-bill-wave"></i></div>
                <div class="summary-info">
                    <span class="summary-label">Total Outstanding Due</span>
                    <span class="summary-value"><?= number_format($totalDue, 2) ?> TK</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon blue"><i class="fas fa-users"></i></div>
                <div class="summary-info">
                    <span class="summary-label">Customers with Due</span>
                    <span class="summary-value"><?= count($customers) ?></span>
                </div>
            </div>
        </div>

        <?php if (!empty($chartData['labels'])): ?>
            <div class="chart-container">
                <canvas id="dueChart"></canvas>
            </div>
        <?php endif; ?>

        <div class="table-wrapper">
            <div style="overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th width="10%">ID</th>
                            <th width="25%">Customer</th>
                            <th width="15%">Mobile</th>
                            <th width="15%">Area</th>
                            <th width="12%" style="text-align: right;">Monthly Rent</th>
                            <th width="12%" style="text-align: right;">Due Amount</th>
                            <th width="11%" class="no-print">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($customers)): ?>
                            <?php foreach ($customers as $c): ?>
                                <tr>
                                    <td><span
                                            class="col-id"><?= htmlspecialchars($c['prefix_code'] ?? '') ?><?= $c['id'] ?></span>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <span class="user-name"><?= htmlspecialchars($c['full_name']) ?></span>
                                            <span
                                                class="user-sub"><?= htmlspecialchars($c['pppoe_name'] ?? 'No PPPoE') ?></span>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($c['mobile_no']) ?></td>
                                    <td><?= htmlspecialchars($c['area'] ?? 'N/A') ?></td>
                                    <td style="text-align: right; font-weight: 500; font-family: monospace;">
                                        <?= number_format($c['monthly_rent'], 2) ?>
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: #ef4444; font-family: monospace;">
                                        <?= number_format($c['due_amount'], 2) ?>
                                    </td>
                                    <td class="no-print">
                                        <a href="<?= url('customer/show/' . $c['id']) ?>" class="btn-action">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                                    No due records found for this period.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function syncBalances() {
        if (!confirm('This will recalculate all customer balances from their connection dates and update the database permanently. Are you sure?')) return;

        const btn = document.getElementById('syncBtn');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';

        fetch('<?= url('report/syncBalances') ?>')
            .then(res => res.json())
            .then(res => {
                alert(res.message);
                location.reload();
            })
            .catch(err => {
                alert('Error syncing balances.');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
    }

    <?php if (!empty($chartData['labels'])): ?>
        const ctx = document.getElementById('dueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartData['labels']) ?>,
                datasets: [{
                    label: 'Due Amount by Area',
                    data: <?= json_encode($chartData['values']) ?>,
                    backgroundColor: 'rgba(239, 68, 68, 0.6)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { display: false }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    <?php endif; ?>

    document.addEventListener('DOMContentLoaded', function () {
        const pickerBtn = document.getElementById('colPickerBtn');
        const pickerDropdown = document.getElementById('colPickerDropdown');
        const toggles = document.querySelectorAll('.col-toggle');
        const table = document.querySelector('.custom-table');
        const STORAGE_KEY = 'due_list_cols';

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