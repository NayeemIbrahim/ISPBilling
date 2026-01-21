<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="main-content">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="margin-bottom: 25px;">
            <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 5px;">Change Password</h1>
            <p style="color: #64748b; font-size: 0.9rem;">Update your account security by choosing a strong password.
            </p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>" style="margin-bottom: 24px;">
                <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="card" style="padding: 30px;">
            <form action="<?= url('user/changePassword') ?>" method="POST">
                <div style="display: grid; gap: 20px;">
                    <div class="form-group">
                        <label
                            style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 8px; color: #1e293b;">Current
                            Password</label>
                        <input type="password" name="current_password" class="form-control"
                            style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; outline:none;"
                            required>
                    </div>

                    <div style="border-top: 1px solid #f1f5f9; padding-top: 20px;">
                    </div>

                    <div class="form-group">
                        <label
                            style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 8px; color: #1e293b;">New
                            Password</label>
                        <input type="password" name="new_password" class="form-control"
                            style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; outline:none;"
                            required>
                    </div>

                    <div class="form-group">
                        <label
                            style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 8px; color: #1e293b;">Confirm
                            New Password</label>
                        <input type="password" name="confirm_password" class="form-control"
                            style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; outline:none;"
                            required>
                    </div>

                    <div style="margin-top: 10px;">
                        <button type="submit" class="btn"
                            style="background: var(--primary); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                            Update Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>