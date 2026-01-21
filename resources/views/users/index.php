<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
    .management-container {
        max-width: 1100px;
        margin: 0 auto;
    }

    .management-header {
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .user-table-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .u-table {
        width: 100%;
        border-collapse: collapse;
    }

    .u-table th {
        background: #f8fafc;
        padding: 12px 20px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }

    .u-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background: #f1f5f9;
        color: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .user-details .name {
        display: block;
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .user-details .email {
        display: block;
        font-size: 0.8rem;
        color: #64748b;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .badge-active { background: #dcfce7; color: #16a34a; }
    .badge-pending { background: #fef3c7; color: #d97706; }
    .badge-inactive { background: #fee2e2; color: #dc2626; }

    .role-select {
        padding: 6px 10px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.875rem;
        outline: none;
        background: white;
        cursor: pointer;
    }

    .role-select:focus {
        border-color: var(--primary);
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-approve {
        background: #10b981;
        color: white;
    }
    .btn-approve:hover { background: #059669; }

    .btn-deactivate {
        background: white;
        color: #dc2626;
        border-color: #fecaca;
    }
    .btn-deactivate:hover { background: #fee2e2; }

    .username-tag {
        font-family: monospace;
        background: #f1f5f9;
        padding: 2px 6px;
        border-radius: 4px;
        color: #475569;
        font-size: 0.85rem;
    }
</style>

<div class="main-content">
    <div class="management-container">
        <div class="management-header">
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 5px;">User Management</h1>
                <p style="color: #64748b; font-size: 0.9rem;">Review registrations and manage administrative access.</p>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" style="margin-bottom: 24px;">
                <i class="fas fa-check-circle"></i>
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="user-table-card">
            <table class="u-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?= strtoupper(substr($user['display_name'], 0, 1)) ?>
                                    </div>
                                    <div class="user-details">
                                        <span class="name"><?= htmlspecialchars($user['display_name']) ?></span>
                                        <span class="email"><?= htmlspecialchars($user['email']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="username-tag"><?= htmlspecialchars($user['username']) ?></span>
                            </td>
                            <td>
                                <form action="<?= url('user/updateRole') ?>" method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <select name="role" onchange="this.form.submit()" class="role-select"
                                        <?= $user['username'] === 'superadmin' ? 'disabled' : '' ?>>
                                        <option value="Super Admin" <?= $user['role'] === 'Super Admin' ? 'selected' : '' ?>>Super Admin</option>
                                        <option value="Admin" <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="Employee" <?= $user['role'] === 'Employee' ? 'selected' : '' ?>>Employee</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <span class="badge badge-<?= $user['status'] ?>">
                                    <?= $user['status'] ?>
                                </span>
                            </td>
                            <td>
                                <span style="color: #64748b; font-size: 0.875rem;">
                                    <?= date('d M Y', strtotime($user['created_at'])) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <?php if ($user['username'] !== 'superadmin'): ?>
                                    <form action="<?= url('user/updateStatus') ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <?php if ($user['status'] !== 'active'): ?>
                                            <button type="submit" name="status" value="active" class="btn-action btn-approve">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="status" value="inactive" class="btn-action btn-deactivate" onclick="return confirm('Are you sure you want to deactivate this user?')">
                                                <i class="fas fa-user-slash"></i> Deactivate
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>