<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="dashboard-container">
    <div class="card-box">
        <div class="card-header">
            <span><i class="fas fa-user-slash"></i> Inactive / Expired List</span>
            <div style="display: flex; gap: 10px;">
                 <button onclick="processAutoDisable()" class="btn-collect" style="padding: 5px 15px; font-size: 14px; background: #ef4444;">Run Auto-Disable</button>
                 <button onclick="window.print()" class="btn-collect" style="padding: 5px 15px; font-size: 14px;">Print List</button>
            </div>
        </div>

        <div class="table-container" style="margin-top: 20px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th>Expiry Date</th>
                        <th>Auto Disable</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $c): 
                            $isExpired = $c['expire_date'] && strtotime($c['expire_date']) < time();
                            $statusClass = $c['status'];
                            if ($isExpired && $c['status'] == 'active') $statusClass = 'expired';
                        ?>
                            <tr>
                                <td><?= $c['prefix_code'] ?? '' ?><?= $c['id'] ?></td>
                                <td><?= htmlspecialchars($c['full_name']) ?></td>
                                <td><?= htmlspecialchars($c['mobile_no']) ?></td>
                                <td>
                                    <span class="badge <?= $statusClass ?>">
                                        <?= ($statusClass == 'expired') ? 'Expired' : ucfirst($c['status']) ?>
                                    </span>
                                </td>
                                <td style="<?= $isExpired ? 'color: #ef4444; font-weight: bold;' : '' ?>">
                                    <?= $c['expire_date'] ?? 'N/A' ?>
                                </td>
                                <td>
                                    <?= ($c['auto_disable'] == 1) ? '<span style="color: #10b981;"><i class="fas fa-check-circle"></i> Yes</span>' : 'No' ?>
                                </td>
                                <td>
                                    <a href="<?= url('customers/show/' . $c['id']) ?>" class="btn-collect" style="padding: 3px 8px; font-size: 12px; background: #3b82f6;">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; padding: 20px;">No inactive or expired customers found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function processAutoDisable() {
    if(!confirm('Are you sure you want to run the auto-disable process now? This will disable all active customers with expired dates and auto-disable enabled.')) return;
    
    fetch('<?= url('report/processAutoDisable') ?>')
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        location.reload();
    })
    .catch(err => alert('Error processing requests.'));
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
