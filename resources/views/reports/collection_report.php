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
        color: #3b82f6;
        background: #eff6ff;
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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        padding: 25px 30px 0 30px;
    }

    .stat-card {
        padding: 20px;
        border-radius: 12px;
        border: 1px solid transparent;
    }

    .stat-card.green {
        background: #ecfdf5;
        border-color: #a7f3d0;
    }

    .stat-card.blue {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .stat-label {
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 4px;
        display: block;
    }

    .stat-desc {
        font-size: 0.75rem;
        opacity: 0.8;
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

    .custom-table tr:last-child td {
        border-bottom: none;
    }

    .custom-table tr:hover td {
        background: #f8fafc;
    }

    .col-amount {
        font-family: 'Inter', monospace;
        font-weight: 600;
        color: #059669 !important;
        text-align: right;
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
        font-weight: 500;
        color: #0f172a;
    }

    .user-sub {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 2px;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-block;
        text-align: center;
    }

    .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-disabled {
        background: #f3f4f6;
        color: #374151;
    }

    /* Address Grid */
    .address-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }
</style>

<div class="dashboard-container">
    <div class="report-card">
        <div class="report-header">
            <div class="report-title">
                <i class="fas fa-file-invoice-dollar"></i>
                Collection Report
            </div>
            <button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> Print Report</button>
        </div>

        <div class="filter-section">
            <form method="GET" action="<?= url('report/collectionReport') ?>" class="filter-form">
                <div class="filter-row">
                    <div class="form-group-custom">
                        <label>Start Date</label>
                        <input type="text" name="start_date" value="<?= $startDate ?>"
                            class="form-control-custom date-picker" placeholder="DD/MM/YYYY">
                    </div>
                    <div class="form-group-custom">
                        <label>End Date</label>
                        <input type="text" name="end_date" value="<?= $endDate ?>"
                            class="form-control-custom date-picker" placeholder="DD/MM/YYYY">
                    </div>

                    <div class="form-group-custom">
                        <label>Connected By</label>
                        <select name="connected_by" class="form-control-custom">
                            <option value="">All Agents</option>
                            <?php foreach ($options['connectedByList'] as $opt): ?>
                                <option value="<?= htmlspecialchars($opt) ?>" <?= $filters['connected_by'] == $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group-custom">
                        <label>Collected By</label>
                        <select name="collected_by" class="form-control-custom">
                            <option value="">All Collectors</option>
                            <?php foreach ($options['employees'] as $emp): ?>
                                <option value="<?= $emp['id'] ?>" <?= $filters['collected_by'] == $emp['id'] ? 'selected' : '' ?>><?= htmlspecialchars($emp['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="address-grid">
                    <select name="district" class="form-control-custom" aria-label="District">
                        <option value="">District</option>
                        <?php foreach ($options['districts'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>" <?= $filters['district'] == $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option><?php endforeach; ?>
                    </select>
                    <select name="thana" class="form-control-custom" aria-label="Thana">
                        <option value="">Thana</option>
                        <?php foreach ($options['thanas'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>" <?= $filters['thana'] == $opt ? 'selected' : '' ?>>
                                <?= htmlspecialchars($opt) ?>
                            </option><?php endforeach; ?>
                    </select>
                    <select name="area" class="form-control-custom" aria-label="Area">
                        <option value="">Area</option>
                        <?php foreach ($options['areas'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>" <?= $filters['area'] == $opt ? 'selected' : '' ?>>
                                <?= htmlspecialchars($opt) ?>
                            </option><?php endforeach; ?>
                    </select>
                    <select name="building_name" class="form-control-custom" aria-label="Building">
                        <option value="">Building</option>
                        <?php foreach ($options['buildings'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>" <?= $filters['building_name'] == $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option><?php endforeach; ?>
                    </select>
                    <select name="floor" class="form-control-custom" aria-label="Floor">
                        <option value="">Floor</option>
                        <?php foreach ($options['floors'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>" <?= $filters['floor'] == $opt ? 'selected' : '' ?>>
                                <?= htmlspecialchars($opt) ?>
                            </option><?php endforeach; ?>
                    </select>
                    <select name="house_no" class="form-control-custom" aria-label="House No">
                        <option value="">House No</option>
                        <?php foreach ($options['houses'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>" <?= $filters['house_no'] == $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option><?php endforeach; ?>
                    </select>

                    <div style="grid-column: span 1; display: flex; gap: 10px;">
                        <button type="submit" class="btn-filter">Filter</button>
                        <a href="<?= url('report/collectionReport') ?>" class="btn-reset">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card green">
                <div class="stat-label" style="color: #047857;">Total Revenue</div>
                <div class="stat-value" style="color: #065f46;"><?= number_format($totalCollected, 2) ?> <span
                        style="font-size: 1rem; font-weight: 500;">TK</span></div>
                <div class="stat-desc" style="color: #065f46;">Received between selected dates</div>
            </div>
            <div class="stat-card blue">
                <div class="stat-label" style="color: #1e40af;">Total Transactions</div>
                <div class="stat-value" style="color: #1e3a8a;"><?= count($collections) ?></div>
                <div class="stat-desc" style="color: #1e3a8a;">Count of individual payments</div>
            </div>
        </div>

        <div class="table-wrapper">
            <div style="overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th width="12%">Date</th>
                            <th width="12%">Payment ID</th>
                            <th width="12%">ID</th>
                            <th width="20%">Customer</th>
                            <th width="14%">Collected By</th>
                            <th width="12%" style="text-align: right;">Amount</th>
                            <th width="8%" style="text-align: center;">Status</th>
                            <th width="10%" style="text-align: right;">Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($collections)): ?>
                            <?php foreach ($collections as $col): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 500;"><?= date('d/m/Y', strtotime($col['collection_date'])) ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: #94a3b8;">
                                            <?= date('h:i A', strtotime($col['collection_date'])) ?>
                                        </div>
                                    </td>
                                    <td><span
                                            class="col-id"><?= htmlspecialchars($col['payment_id'] ?? $col['transaction_id']) ?></span>
                                    </td>
                                    <td><span
                                            class="col-id"><?= htmlspecialchars($col['prefix_code'] ?? '') ?><?= $col['customer_id'] ?></span>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <span class="user-name"><?= htmlspecialchars($col['full_name']) ?></span>
                                            <span class="user-sub"><i class="fas fa-phone-alt"
                                                    style="font-size: 0.6rem; opacity: 0.7;"></i>
                                                <?= htmlspecialchars($col['mobile_no']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <span class="user-name"
                                                style="font-size: 0.85rem;"><?= htmlspecialchars($col['collected_by_name'] ?? 'System') ?></span>
                                        </div>
                                    </td>
                                    <td class="col-amount"><?= number_format($col['amount'], 2) ?></td>
                                    <td style="text-align: center;">
                                        <span
                                            class="status-badge status-<?= strtolower($col['status']) ?>"><?= ucfirst($col['status']) ?></span>
                                    </td>
                                    <td style="text-align: right; font-feature-settings: 'tnum';">
                                        <?= $col['next_expire_date'] ? date('d/m/Y', strtotime($col['next_expire_date'])) : '<span style="color:#cbd5e1">-</span>' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: #64748b;">
                                    <div style="margin-bottom: 10px; font-size: 2rem; opacity: 0.3;"><i
                                            class="fas fa-search"></i></div>
                                    No records found matching your criteria.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>