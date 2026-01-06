<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="dashboard-container">
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <div
            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; border-bottom: 2px solid #e2e8f0; padding-bottom:15px;">
            <h2 style="margin:0; color:#1e293b;">Edit Complain #<?= $complain['id'] ?></h2>
            <a href="<?= url('complain-list') ?>" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <!-- Customer Info (Read-only) -->
        <div class="form-section"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding:20px; border-radius:10px; margin-bottom:25px; color:white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h4 style="margin-top:0; color:white; border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom:10px;">
                <i class="fas fa-user-circle"></i> Customer Details
            </h4>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; font-size:14px;">
                <div><strong>ID:</strong> <?= $customer['id'] ?></div>
                <div><strong>Name:</strong> <?= htmlspecialchars($customer['full_name']) ?></div>
                <div><strong>Mobile:</strong> <?= htmlspecialchars($customer['mobile_no']) ?></div>
                <div><strong>Area:</strong> <?= htmlspecialchars($customer['area']) ?></div>
            </div>
        </div>

        <!-- Edit Form -->
        <form action="<?= url('complain-list/update/' . $complain['id']) ?>" method="POST">
            <div class="form-section">
                <h4 style="color:#475569; margin-bottom:15px;">
                    <i class="fas fa-clipboard-list" style="color:#3b82f6;"></i> Complain Details
                </h4>

                <div class="form-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <div class="form-group">
                        <label><i class="fas fa-exclamation-circle"></i> Complain Type *</label>
                        <select name="complain_type_id" required>
                            <option value="">Select Issue</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= $complain['complain_type_id'] == $t['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-tasks"></i> Status</label>
                        <select name="status">
                            <option value="Pending" <?= $complain['status'] == 'Pending' ? 'selected' : '' ?>>Pending
                            </option>
                            <option value="In Progress" <?= $complain['status'] == 'In Progress' ? 'selected' : '' ?>>In
                                Progress</option>
                            <option value="Resolved" <?= $complain['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-users"></i> Assign To (Hold Ctrl to select multiple)</label>
                    <?php $assignedIds = json_decode($complain['assigned_to'] ?? '[]', true); ?>
                    <select name="assigned_to[]" multiple style="height:100px; padding:10px;">
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>" <?= in_array($emp['id'], $assignedIds) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($emp['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color:#64748b;">Hold Ctrl (Windows) or Cmd (Mac) to select multiple employees</small>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-comment-alt"></i> Comments / Description</label>
                    <textarea name="description" rows="4" placeholder="Describe the issue in detail..."
                        style="resize:vertical;"><?= htmlspecialchars($complain['description']) ?></textarea>
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" class="btn-primary" style="flex:1; padding:12px; font-size:16px;">
                        <i class="fas fa-save"></i> Update Complain
                    </button>
                    <a href="<?= url('complain-list') ?>" class="btn-secondary"
                        style="flex:1; padding:12px; font-size:16px; text-align:center; display:inline-block;">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .form-section {
        margin-bottom: 30px;
        padding: 20px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .btn-secondary {
        background: #64748b;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background: #475569;
    }
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>