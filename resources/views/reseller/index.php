<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/export_scripts.php'; ?>

<main class="dashboard-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <h2 style="margin: 0;">Reseller Information</h2>
        
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form method="GET" action="<?= url('reseller') ?>" style="display: flex; gap: 5px;">
                <input type="text" name="q" value="<?= htmlspecialchars($searchQuery ?? '') ?>" placeholder="Search Reseller..." style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; min-width: 200px;">
                <button type="submit" style="padding: 8px 15px; background: #64748b; color: white; border: none; border-radius: 4px; cursor: pointer;">Search</button>
                <?php if (!empty($searchQuery)): ?>
                    <a href="<?= url('reseller') ?>" style="padding: 8px 15px; background: #cbd5e1; color: #333; text-decoration: none; border-radius: 4px;">Clear</a>
                <?php endif; ?>
            </form>
            
            <button type="button" onclick="exportTable('resellerTable', 'excel', 'Reseller_List')" style="padding: 8px 15px; background:#10b981; color:white; border:none; border-radius:4px; cursor:pointer;">Excel</button>
            <button type="button" onclick="window.print()" style="padding: 8px 15px; background:#475569; color:white; border:none; border-radius:4px; cursor:pointer;">Print</button>
            <a href="<?= url('reseller/create') ?>" class="btn-primary" style="background-color: #3b82f6;">Create New Reseller</a>
        </div>
    </div>

    <!-- Stats summary (optional but good) -->
    <div style="display: flex; gap: 20px; margin-bottom: 20px;" class="no-print">
        <div style="background: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 4px solid #ef4444; flex: 1;">
            <h4 style="margin: 0; color: #64748b; font-size: 14px;">Total Reseller</h4>
            <div style="font-size: 24px; font-weight: bold; margin-top: 5px; color: #ef4444;"><?= count($resellers) ?></div>
        </div>
        <div style="background: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 4px solid #10b981; flex: 1;">
            <h4 style="margin: 0; color: #64748b; font-size: 14px;">Active Reseller</h4>
            <div style="font-size: 24px; font-weight: bold; margin-top: 5px; color: #10b981;"><?= count($resellers) ?></div>
        </div>
        <div style="background: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 4px solid #f59e0b; flex: 1;">
            <h4 style="margin: 0; color: #64748b; font-size: 14px;">Total Balance</h4>
            <div style="font-size: 24px; font-weight: bold; margin-top: 5px; color: #f59e0b;">
                ৳ <?= number_format(array_sum(array_column($resellers, 'balance')), 2) ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-container" style="overflow-x: auto;">
            <table class="data-table" id="resellerTable" style="font-size: 12px; min-width: 1500px;">
                <thead>
                    <tr style="background-color: #f8fafc;">
                        <th style="padding: 8px;">Sl.</th>
                        <th style="padding: 8px;">Reseller ID</th>
                        <th style="padding: 8px; min-width: 250px;">Company</th>
                        <th style="padding: 8px;">Sort</th>
                        <th style="padding: 8px;">Enable<br><small>(Active)</small></th>
                        <th style="padding: 8px;">Temporary<br><small>Disable</small></th>
                        <th style="padding: 8px; min-width: 150px;">Customer List</th>
                        <th style="padding: 8px;">Total Balance</th>
                        <th style="padding: 8px;">Deposit</th>
                        <th style="padding: 8px;">Withdraw</th>
                        <th style="padding: 8px; min-width: 180px;">Deduct</th>
                        <th style="padding: 8px;">Reseller<br>Child %</th>
                        <th style="padding: 8px;" class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($resellers)): ?>
                        <?php foreach ($resellers as $index => $reseller): ?>
                            <tr>
                                <td style="padding: 8px; vertical-align: top;"><?= $index + 1 ?></td>
                                <td style="padding: 8px; vertical-align: top;">
                                    <strong>R-<?= str_pad($reseller['id'], 4, '0', STR_PAD_LEFT) ?></strong><br>
                                    <button class="btn-table" style="background:#8b5cf6; padding: 2px 5px; font-size: 10px; margin-top: 5px;" title="Enter as Reseller">Enter</button>
                                    <button class="btn-table" style="background:#14b8a6; padding: 2px 5px; font-size: 10px; margin-top: 5px;" title="Add Note">Note</button>
                                </td>
                                <td style="padding: 8px; vertical-align: top; line-height: 1.5;">
                                    <a href="<?= url('reseller/edit/' . $reseller['id']) ?>" style="color: #3b82f6; font-weight: bold; text-decoration: none; font-size: 14px;"><?= htmlspecialchars($reseller['company_name']) ?></a><br>
                                    <strong>Owner:</strong> <?= htmlspecialchars($reseller['contact_person'] ?? '-') ?><br>
                                    <strong>Mobile:</strong> <?= htmlspecialchars($reseller['phone'] ?? '-') ?><br>
                                    <strong>Email:</strong> <?= htmlspecialchars($reseller['email'] ?? '-') ?><br>
                                    <?php if (!empty($reseller['drive_link'])): ?>
                                        <a href="<?= htmlspecialchars($reseller['drive_link']) ?>" target="_blank" style="color: #ef4444; font-weight: bold;">Google Drive</a>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 8px; vertical-align: top;">
                                    <input type="number" value="0" style="width: 50px; padding: 4px; border: 1px solid #ccc; border-radius: 4px;">
                                </td>
                                <td style="padding: 8px; vertical-align: top; text-align: center; color: #10b981; font-weight: bold; font-size: 16px;">
                                    <?= $reseller['active_customers'] ?? 0 ?>
                                </td>
                                <td style="padding: 8px; vertical-align: top; text-align: center; color: #ef4444; font-weight: bold; font-size: 16px;">
                                    <?= $reseller['disabled_customers'] ?? 0 ?>
                                </td>
                                <td style="padding: 8px; vertical-align: top;">
                                    <a href="<?= url('customer?reseller_id=' . $reseller['id']) ?>" class="btn-table" style="background:#6366f1; text-decoration:none; display:inline-block; margin-bottom: 5px; width: 100%; text-align: center;">
                                        Customer List (<?= $reseller['total_customers'] ?? 0 ?>)
                                    </a>
                                    <div style="font-size: 11px; margin-top: 5px; background: #f1f5f9; padding: 5px; border-radius: 4px;">
                                        <strong>Payment Cycle:</strong> Month to Month
                                    </div>
                                    <div style="margin-top: 5px; display: flex; align-items: center; gap: 5px; font-size: 11px;">
                                        <input type="checkbox" <?= !empty($reseller['auto_deduct']) ? 'checked' : '' ?> onchange="alert('Setting update pending API')">
                                        <label>Balance Auto Deduct</label>
                                    </div>
                                </td>
                                <td style="padding: 8px; vertical-align: top;">
                                    <?php if ($reseller['balance'] < 0): ?>
                                        <strong style="color: #ef4444; font-size: 16px;">৳ <?= number_format($reseller['balance'], 2) ?></strong>
                                    <?php else: ?>
                                        <strong style="color: #10b981; font-size: 16px;">৳ <?= number_format($reseller['balance'], 2) ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 8px; vertical-align: top;">
                                    <button onclick="openTransactionModal(<?= $reseller['id'] ?>, 'deposit')" class="btn-table" style="background:#3b82f6; width: 100%;" title="Deposit">Deposit (+)</button>
                                </td>
                                <td style="padding: 8px; vertical-align: top;">
                                    <button onclick="openTransactionModal(<?= $reseller['id'] ?>, 'withdraw')" class="btn-table" style="background:#ef4444; width: 100%;" title="Withdraw">Withdraw (-)</button>
                                </td>
                                <td style="padding: 8px; vertical-align: top;">
                                    <button onclick="openTransactionModal(<?= $reseller['id'] ?>, 'deduct')" class="btn-table" style="background:#f59e0b; width: 100%; margin-bottom: 8px;" title="Deduct">Deduct</button>
                                    
                                    <div style="display: flex; align-items: center; gap: 5px; font-size: 11px; margin-bottom: 5px;">
                                        <input type="checkbox" <?= !empty($reseller['enable_with_payment']) ? 'checked' : '' ?> onchange="alert('Setting update pending API')">
                                        <label>Enable with Payment</label>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 5px; font-size: 11px; margin-bottom: 5px;">
                                        <input type="checkbox" <?= !empty($reseller['advance_payment']) ? 'checked' : '' ?> onchange="alert('Setting update pending API')">
                                        <label>Advance Payment Only</label>
                                    </div>
                                    <div style="margin-top: 8px;">
                                        <input type="text" value="<?= htmlspecialchars($reseller['pppoe_prefix'] ?? '') ?>" placeholder="PPPoE Name Prefix" style="width: 100%; padding: 4px; border: 1px solid #ccc; border-radius: 4px; font-size: 11px;" onchange="alert('Prefix update pending API')">
                                    </div>
                                </td>
                                <td style="padding: 8px; vertical-align: top;">
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <input type="number" step="0.01" value="<?= htmlspecialchars($reseller['child_percentage'] ?? '0') ?>" style="width: 60px; padding: 4px; border: 1px solid #ccc; border-radius: 4px;" onchange="alert('Percentage update pending API')"> %
                                    </div>
                                </td>
                                <td style="padding: 8px; vertical-align: top;" class="no-print">
                                    <div style="display: flex; flex-direction: column; gap: 5px;">
                                        <a href="<?= url('reseller/edit/' . $reseller['id']) ?>" class="btn-table" style="background:#10b981; text-decoration:none; text-align:center;">Edit</a>
                                        <form action="<?= url('reseller/delete/' . $reseller['id']) ?>" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Are you sure you want to delete this reseller?');">
                                            <button type="submit" class="btn-table" style="background:#ef4444; width: 100%;">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="13" style="text-align: center; padding: 20px;">No resellers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Transaction Modal -->
<div class="modal-overlay" id="transactionModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:white; padding:25px; border-radius:12px; width:400px; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid #e2e8f0; padding-bottom:15px;">
            <h3 id="modalTitle" style="margin:0;">Transaction</h3>
            <button onclick="closeModal()" style="background:none; border:none; font-size:20px; cursor:pointer;">&times;</button>
        </div>
        <form id="transactionForm" action="<?= url('reseller/transaction') ?>" method="POST">
            <input type="hidden" name="reseller_id" id="modalResellerId">
            <input type="hidden" name="type" id="modalTransactionType">
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Amount *</label>
                <input type="number" step="0.01" name="amount" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Notes (Optional)</label>
                <textarea name="note" rows="2" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;"></textarea>
            </div>
            
            <div style="text-align:right; margin-top: 20px;">
                <button type="button" onclick="closeModal()" style="padding: 8px 15px; border:none; background:#e2e8f0; border-radius:6px; cursor:pointer; margin-right:10px;">Cancel</button>
                <button type="submit" id="modalSubmitBtn" style="padding: 8px 15px; border:none; background:#3b82f6; color:white; border-radius:6px; cursor:pointer;">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openTransactionModal(resellerId, type) {
        document.getElementById('modalResellerId').value = resellerId;
        document.getElementById('modalTransactionType').value = type;
        
        let title = '';
        let btnColor = '';
        
        if (type === 'deposit') {
            title = 'Deposit Funds (+)';
            btnColor = '#3b82f6';
        } else if (type === 'withdraw') {
            title = 'Withdraw Funds (-)';
            btnColor = '#ef4444';
        } else if (type === 'deduct') {
            title = 'Deduct Charge';
            btnColor = '#f59e0b';
        }
        
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalSubmitBtn').style.background = btnColor;
        document.getElementById('modalSubmitBtn').innerText = title;
        
        document.getElementById('transactionModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('transactionModal').style.display = 'none';
        document.getElementById('transactionForm').reset();
    }
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
