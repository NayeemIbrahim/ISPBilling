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
        color: #64748b;
        background: #f1f5f9;
        padding: 8px;
        border-radius: 8px;
        font-size: 1rem;
    }

    .btn-group {
        display: flex;
        gap: 10px;
    }

    .btn-action-main {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
        border: 1px solid transparent;
    }

    .btn-print {
        background: #fff;
        border-color: #cbd5e1;
        color: #475569;
    }

    .btn-print:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #1e293b;
    }

    .btn-disable {
        background: #ef4444;
        color: #fff;
    }

    .btn-disable:hover {
        background: #dc2626;
        transform: translateY(-1px);
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

    .badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-block;
    }

    .badge.expired {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge.inactive {
        background: #f3f4f6;
        color: #4b5563;
    }

    .badge.disabled {
        background: #f3f4f6;
        color: #4b5563;
    }

    .btn-view {
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

    .btn-view:hover {
        background: #dbeafe;
    }

    @media print {

        .btn-group,
        .btn-view {
            display: none !important;
        }
    }
</style>

<div class="dashboard-container">
    <div class="report-card">
        <div class="report-header">
            <div class="report-title">
                <i class="fas fa-user-slash"></i>
                Inactive / Expired List
            </div>
            <div class="btn-group">
                <button onclick="processAutoDisable()" class="btn-action-main btn-disable">
                    <i class="fas fa-bolt"></i> Run Auto-Disable
                </button>
                <button onclick="window.print()" class="btn-action-main btn-print">
                    <i class="fas fa-print"></i> Print List
                </button>
            </div>
        </div>

        <div class="table-wrapper">
            <div style="overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th width="12%">ID</th>
                            <th width="25%">Customer</th>
                            <th width="15%">Mobile</th>
                            <th width="12%">Status</th>
                            <th width="15%">Expiry Date</th>
                            <th width="10%">Manual?</th>
                            <th width="11%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($customers)): ?>
                            <?php foreach ($customers as $c):
                                $isExpired = $c['expire_date'] && strtotime($c['expire_date']) < time();
                                $statusClass = $c['status'];
                                if ($isExpired && $c['status'] == 'active')
                                    $statusClass = 'expired';
                                ?>
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
                                    <td>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ($statusClass == 'expired') ? 'Expired' : ucfirst($c['status']) ?>
                                        </span>
                                    </td>
                                    <td style="<?= $isExpired ? 'color: #ef4444; font-weight: 600;' : '' ?>">
                                        <?= $c['expire_date'] ? date('d/m/Y', strtotime($c['expire_date'])) : 'N/A' ?>
                                    </td>
                                    <td>
                                        <?= ($c['auto_disable'] == 1) ? '<span style="color: #10b981;" title="Auto-disable enabled"><i class="fas fa-robot"></i></span>' : '<span style="color: #94a3b8;" title="Manual only"><i class="fas fa-user-cog"></i></span>' ?>
                                    </td>
                                    <td>
                                        <a href="<?= url('customers/show/' . $c['id']) ?>" class="btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                                    No inactive or expired customers found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function processAutoDisable() {
        if (!confirm('Are you sure you want to run the auto-disable process now? This will disable all active customers with expired dates and auto-disable enabled.')) return;

        const btn = event.currentTarget;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        fetch('<?= url('report/processAutoDisable') ?>')
            .then(res => res.json())
            .then(res => {
                alert(res.message);
                location.reload();
            })
            .catch(err => {
                alert('Error processing requests.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-bolt"></i> Run Auto-Disable';
            });
    }
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>