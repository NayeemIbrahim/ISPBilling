<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
    .profile-page {
        padding: 40px 20px;
        background: #f8fafc;
        min-height: calc(100vh - 60px);
    }

    .profile-layout {
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 32px;
    }

    /* Left Sidebar: Profile Card */
    .profile-card {
        background: white;
        border-radius: 20px;
        padding: 40px 24px;
        text-align: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        height: fit-content;
        position: sticky;
        top: 100px;
    }

    .avatar-wrapper {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto 24px;
    }

    .profile-avatar-large {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary) 0%, #1d4ed8 100%);
        color: white;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        font-weight: 700;
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
    }

    .profile-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .profile-role-badge {
        display: inline-block;
        padding: 4px 12px;
        background: #eff6ff;
        color: var(--primary);
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 32px;
    }

    .profile-info-list {
        text-align: left;
        border-top: 1px solid #f1f5f9;
        padding-top: 24px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        color: #64748b;
        font-size: 0.875rem;
    }

    .info-item i {
        width: 16px;
        color: #94a3b8;
    }

    /* Right Side: Content Area */
    .settings-content {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .settings-card {
        background: white;
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    .settings-heading {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .full-width {
        grid-column: span 2;
    }

    .form-group label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
    }

    .form-input {
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.2s;
        background: #f8fafc;
        color: #1e293b;
    }

    .form-input:focus {
        background: white;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    .form-input:read-only {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }

    textarea.form-input {
        min-height: 120px;
        resize: vertical;
    }

    .divider {
        height: 1px;
        background: #f1f5f9;
        margin: 32px 0;
    }

    .btn-submit {
        background: var(--primary);
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        width: fit-content;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }

    .btn-submit:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .profile-layout {
            grid-template-columns: 1fr;
        }

        .profile-card {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .full-width {
            grid-column: span 1;
        }
    }
</style>

<div class="profile-page">
    <div class="profile-layout">
        <!-- Sidebar -->
        <div class="profile-card">
            <div class="avatar-wrapper">
                <div class="profile-avatar-large">
                    <?= strtoupper(substr($user['display_name'], 0, 1)) ?>
                </div>
            </div>
            <h1 class="profile-name"><?= htmlspecialchars($user['display_name']) ?></h1>
            <div class="profile-role-badge"><?= htmlspecialchars($user['role'] ?? 'User') ?></div>

            <div class="profile-info-list">
                <div class="info-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Joined <?= date('M Y', strtotime($user['created_at'])) ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-shield-check"></i>
                    <span>Status: <?= ucfirst($user['status']) ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-user-tag"></i>
                    <span>ID: #<?= str_pad($user['id'], 4, '0', STR_PAD_LEFT) ?></span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="settings-content">
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>" style="margin-bottom: 0; border-radius: 16px;">
                    <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <form action="<?= url('user/profile') ?>" method="POST" class="settings-card">
                <h2 class="settings-heading">Account Information</h2>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Display Name</label>
                        <input type="text" name="display_name" class="form-input"
                            value="<?= htmlspecialchars($user['display_name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-input" value="<?= htmlspecialchars($user['username']) ?>"
                            readonly>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-input"
                            value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-input"
                            value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+880">
                    </div>

                    <div class="form-group full-width">
                        <label>Physical Address</label>
                        <input type="text" name="address" class="form-input"
                            value="<?= htmlspecialchars($user['address'] ?? '') ?>"
                            placeholder="Enter your full address">
                    </div>

                    <div class="form-group full-width">
                        <label>Brief Bio</label>
                        <textarea name="bio" class="form-input"
                            placeholder="Write a little about yourself..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="divider"></div>

                <h2 class="settings-heading">Update Password</h2>
                <p style="color: #64748b; font-size: 0.875rem; margin-top: -16px; margin-bottom: 24px;">
                    Leave blank if you don't want to change your current password.
                </p>

                <div class="form-grid">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="password" class="form-input" placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-input" placeholder="••••••••">
                    </div>
                </div>

                <div style="margin-top: 40px;">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i>
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>