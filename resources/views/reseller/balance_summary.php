<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/export_scripts.php'; ?>

<main class="dashboard-container">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0;">Reseller Balance Summary</h2>
            
            <div style="display:flex; gap:10px; align-items:center;">
                <button type="button" onclick="exportTable('balanceSummaryTable', 'excel', 'Reseller_Balance_Summary')" style="padding: 8px 15px; background:#10b981; color:white; border:none; border-radius:4px; cursor:pointer;">Excel</button>
                <button type="button" onclick="window.print()" style="padding: 8px 15px; background:#64748b; color:white; border:none; border-radius:4px; cursor:pointer;">Print</button>
            </div>
        </div>
        
        <div class="table-container">
            <table class="data-table" id="balanceSummaryTable">
                <thead>
                    <tr>
                        <th>Sl.</th>
                        <th>Reseller ID</th>
                        <th>Company Name</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Current Balance</th>
                        <th class="no-print">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalBalance = 0;
                    if (!empty($resellers)): 
                        foreach ($resellers as $index => $r): 
                            $totalBalance += $r['balance'];
                    ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>R-<?= str_pad($r['id'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td><strong><?= htmlspecialchars($r['company_name']) ?></strong></td>
                            <td><?= htmlspecialchars($r['contact_person'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($r['phone'] ?? '-') ?></td>
                            <td>
                                <?php if ($r['balance'] < 0): ?>
                                    <strong style="color: #ef4444;">৳ <?= number_format($r['balance'], 2) ?></strong>
                                <?php else: ?>
                                    <strong style="color: #10b981;">৳ <?= number_format($r['balance'], 2) ?></strong>
                                <?php endif; ?>
                            </td>
                            <td class="no-print">
                                <a href="<?= url('reseller/balance?reseller_id=' . $r['id']) ?>" class="btn-table" style="background:#3b82f6; text-decoration:none;">View Ledger</a>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 20px;">No resellers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($resellers)): ?>
                <tfoot>
                    <tr style="background-color: #f8fafc; font-weight: bold;">
                        <td colspan="5" style="text-align: right;">Total Network Balance:</td>
                        <td>৳ <?= number_format($totalBalance, 2) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>
