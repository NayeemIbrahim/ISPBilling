<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="dashboard-container">
    <div class="card-box">
        <div class="card-header">
            <span><i class="fas fa-file-invoice-dollar"></i> Collection Report</span>
            <button onclick="window.print()" class="btn-collect" style="padding: 5px 15px; font-size: 14px;">Print Report</button>
        </div>

        <!-- Filter Form -->
        <div style="margin-top: 20px; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
            <form method="GET" action="<?= url('report/collectionReport') ?>" style="display: flex; gap: 15px; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: block; font-size: 13px; color: #64748b; margin-bottom: 5px;">Start Date</label>
                    <input type="date" name="start_date" value="<?= $startDate ?>" class="form-control" style="width: 150px;">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: block; font-size: 13px; color: #64748b; margin-bottom: 5px;">End Date</label>
                    <input type="date" name="end_date" value="<?= $endDate ?>" class="form-control" style="width: 150px;">
                </div>
                <button type="submit" class="btn-collect" style="padding: 8px 20px;">Filter Records</button>
                <a href="<?= url('report/collectionReport') ?>" style="padding: 8px 15px; color: #64748b; text-decoration: none; font-size: 14px;">Reset</a>
            </form>
        </div>

        <div class="stats-summary" style="display: flex; gap: 20px; margin-top: 20px;">
            <div style="flex: 1; padding: 20px; background: #ecfdf5; border-radius: 12px; border: 1px solid #10b981;">
                <p style="color: #065f46; font-size: 14px; margin-bottom: 5px;">Total Collections</p>
                <h2 style="color: #059669; margin: 0; font-size: 28px;"><?= number_format($totalCollected, 2) ?> TK</h2>
                <p style="font-size: 12px; color: #059669; margin-top: 5px;">Showing records from <?= $startDate ?> to <?= $endDate ?></p>
            </div>
            <div style="flex: 1; padding: 20px; background: #eff6ff; border-radius: 12px; border: 1px solid #3b82f6;">
                <p style="color: #1e40af; font-size: 14px; margin-bottom: 5px;">Record Count</p>
                <h2 style="color: #2563eb; margin: 0; font-size: 28px;"><?= count($collections) ?></h2>
                <p style="font-size: 12px; color: #2563eb; margin-top: 5px;">Individual payments processed</p>
            </div>
        </div>

        <div class="table-container" style="margin-top: 20px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Invoice</th>
                        <th>Expiry Set</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($collections)): ?>
                        <?php foreach ($collections as $col): ?>
                            <tr>
                                <td><?= date('d M Y, h:i A', strtotime($col['collection_date'])) ?></td>
                                <td><?= htmlspecialchars($col['full_name']) ?></td>
                                <td><?= htmlspecialchars($col['mobile_no']) ?></td>
                                <td style="font-weight: bold; color: #059669;"><?= number_format($col['amount'], 2) ?> TK</td>
                                <td><span class="badge active" style="background: #e0f2fe; color: #0369a1; border: none;"><?= $col['payment_method'] ?></span></td>
                                <td><?= htmlspecialchars($col['invoice_no'] ?? 'N/A') ?></td>
                                <td><?= $col['next_expire_date'] ?? 'N/A' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; padding: 20px;">No collection records found for this period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
