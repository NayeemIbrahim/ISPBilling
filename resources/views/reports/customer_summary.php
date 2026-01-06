<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="dashboard-container">
    <div class="card-box">
        <div class="card-header">
            <span><i class="fas fa-chart-pie"></i> Customer Summary</span>
            <button onclick="window.print()" class="btn-collect" style="padding: 5px 15px; font-size: 14px;">Print
                Summary</button>
        </div>

        <div class="stats-grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
            <div class="stat-card"
                style="padding: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; text-align: center;">
                <p style="color: #64748b; font-size: 14px; margin-bottom: 10px;">Total Customers</p>
                <h2 style="color: #1e293b; margin: 0; font-size: 32px;"><?= $summary['total'] ?></h2>
            </div>
            <div class="stat-card"
                style="padding: 20px; background: #ecfdf5; border: 1px solid #10b981; border-radius: 12px; text-align: center;">
                <p style="color: #065f46; font-size: 14px; margin-bottom: 10px;">Active Customers</p>
                <h2 style="color: #059669; margin: 0; font-size: 32px;"><?= $summary['active'] ?></h2>
            </div>
            <div class="stat-card"
                style="padding: 20px; background: #fff1f2; border: 1px solid #f43f5e; border-radius: 12px; text-align: center;">
                <p style="color: #9f1239; font-size: 14px; margin-bottom: 10px;">Disabled / Suspended</p>
                <h2 style="color: #e11d48; margin: 0; font-size: 32px;"><?= $summary['disabled'] ?></h2>
            </div>
            <div class="stat-card"
                style="padding: 20px; background: #eff6ff; border: 1px solid #3b82f6; border-radius: 12px; text-align: center;">
                <p style="color: #1e40af; font-size: 14px; margin-bottom: 10px;">Total Collections</p>
                <h2 style="color: #2563eb; margin: 0; font-size: 32px;">
                    <?= number_format($summary['total_collected'] ?? 0, 2) ?> TK</h2>
            </div>
            <div class="stat-card"
                style="padding: 20px; background: #fefce8; border: 1px solid #eab308; border-radius: 12px; text-align: center;">
                <p style="color: #854d0e; font-size: 14px; margin-bottom: 10px;">Total Outstanding Due</p>
                <h2 style="color: #ca8a04; margin: 0; font-size: 32px;">
                    <?= number_format($summary['total_due'] ?? 0, 2) ?> TK</h2>
            </div>
        </div>

        <div
            style="margin-top: 40px; padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
            <h3>Quick Links</h3>
            <ul style="list-style: none; padding: 0; display: flex; gap: 20px; margin-top: 15px;">
                <li><a href="<?= url('report/dueList') ?>" style="color: #3b82f6; text-decoration: none;"><i
                            class="fas fa-exclamation-triangle"></i> View Due List</a></li>
                <li><a href="<?= url('report/inactiveList') ?>" style="color: #3b82f6; text-decoration: none;"><i
                            class="fas fa-user-slash"></i> View Inactive List</a></li>
                <li><a href="<?= url('report/collectionReport') ?>" style="color: #3b82f6; text-decoration: none;"><i
                            class="fas fa-file-invoice-dollar"></i> Detailed Collections</a></li>
            </ul>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>