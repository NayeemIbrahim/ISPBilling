<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/export_scripts.php'; ?>

<main class="dashboard-container">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0;">Reseller Balance Report</h2>
            
            <form method="GET" action="<?= url('reseller/balance') ?>" style="display:flex; gap:10px; align-items:center;">
                <select name="reseller_id" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="">All Resellers</option>
                    <?php foreach ($resellers as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ($r['id'] == $selectedReseller) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['company_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                
                <button type="submit" style="padding: 8px 15px; background:#3b82f6; color:white; border:none; border-radius:4px; cursor:pointer;">Filter</button>
                
                <button type="button" onclick="exportTable('balanceTable', 'excel', 'Reseller_Balance_Report')" style="padding: 8px 15px; background:#10b981; color:white; border:none; border-radius:4px; cursor:pointer;">Excel</button>
                <button type="button" onclick="window.print()" style="padding: 8px 15px; background:#64748b; color:white; border:none; border-radius:4px; cursor:pointer;">Print</button>
            </form>
        </div>
        
        <div class="table-container">
            <table class="data-table" id="balanceTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Transaction ID</th>
                        <th>Reseller Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                        <th>Note</th>
                        <th>Processed By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalDeposit = 0;
                    $totalWithdraw = 0;
                    
                    if (!empty($transactions)): 
                        foreach ($transactions as $t): 
                            $typeLabel = ucfirst($t['type']);
                            $typeColor = '#64748b';
                            
                            if ($t['type'] === 'deposit') {
                                $typeColor = '#3b82f6';
                                $totalDeposit += $t['amount'];
                            } elseif ($t['type'] === 'withdraw' || $t['type'] === 'deduct') {
                                $typeColor = '#ef4444';
                                $totalWithdraw += $t['amount'];
                            }
                    ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($t['date'])) ?></td>
                            <td>TRX-<?= str_pad($t['id'], 6, '0', STR_PAD_LEFT) ?></td>
                            <td><strong><?= htmlspecialchars($t['reseller_name']) ?></strong></td>
                            <td><span style="color: <?= $typeColor ?>; font-weight: bold;"><?= $typeLabel ?></span></td>
                            <td><strong>৳ <?= number_format($t['amount'], 2) ?></strong></td>
                            <td>৳ <?= number_format($t['balance_after'], 2) ?></td>
                            <td><?= htmlspecialchars($t['note'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($t['user_name'] ?? 'System') ?></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="8" style="text-align:center; padding: 20px;">No transactions found for the selected period.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($transactions)): ?>
                <tfoot>
                    <tr style="background-color: #f8fafc; font-weight: bold;">
                        <td colspan="4" style="text-align: right;">Total Summary:</td>
                        <td colspan="4">
                            <span style="color:#3b82f6; margin-right: 15px;">Total Deposits: ৳ <?= number_format($totalDeposit, 2) ?></span>
                            <span style="color:#ef4444;">Total Deductions: ৳ <?= number_format($totalWithdraw, 2) ?></span>
                        </td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>
