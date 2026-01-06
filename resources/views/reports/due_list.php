<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="dashboard-container">
    <div class="card-box">
        <div class="card-header">
            <span><i class="fas fa-exclamation-triangle"></i> Due List Report</span>
            <button onclick="window.print()" class="btn-collect" style="padding: 5px 15px; font-size: 14px;">Print
                Report</button>
        </div>

        <div class="table-container" style="margin-top: 20px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Area</th>
                        <th>Package</th>
                        <th>Due Amount</th>
                        <th>Expiry Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $c): ?>
                            <tr>
                                <td><?= $c['prefix_code'] ?? '' ?><?= $c['id'] ?></td>
                                <td><?= htmlspecialchars($c['full_name']) ?></td>
                                <td><?= htmlspecialchars($c['mobile_no']) ?></td>
                                <td><?= htmlspecialchars($c['area'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($c['package_name'] ?? $c['package_name_ref'] ?? 'N/A') ?></td>
                                <td style="color: #ef4444; font-weight: bold;"><?= number_format($c['due_amount'], 2) ?> TK</td>
                                <td><?= $c['expire_date'] ?? 'N/A' ?></td>
                                <td>
                                    <a href="<?= url('customers/show/' . $c['id']) ?>" class="btn-collect"
                                        style="padding: 3px 8px; font-size: 12px; background: #3b82f6;">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 20px;">No customers with due amount found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>