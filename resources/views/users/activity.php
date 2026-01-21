<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="main-content">
    <div style="max-width: 900px; margin: 0 auto;">
        <div style="margin-bottom: 25px;">
            <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 5px;">Activity Logs</h1>
            <p style="color: #64748b; font-size: 0.9rem;">Your recent actions on the system.</p>
        </div>

        <div class="card"
            style="padding: 0; overflow: hidden; border-radius: 12px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <tr>
                        <th
                            style="padding: 15px 20px; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase;">
                            Activity</th>
                        <th
                            style="padding: 15px 20px; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase;">
                            Description</th>
                        <th
                            style="padding: 15px 20px; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase;">
                            IP Address</th>
                        <th
                            style="padding: 15px 20px; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase;">
                            Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($activities)): ?>
                        <tr>
                            <td colspan="4" style="padding: 30px; text-align: center; color: #64748b;">No activity logs
                                found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($activities as $log): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 15px 20px;">
                                    <?php
                                    $icon = 'fa-info-circle';
                                    $color = '#3b82f6';
                                    switch ($log['activity_type']) {
                                        case 'Login':
                                            $icon = 'fa-sign-in-alt';
                                            $color = '#10b981';
                                            break;
                                        case 'Logout':
                                            $icon = 'fa-sign-out-alt';
                                            $color = '#ef4444';
                                            break;
                                        case 'Change Password':
                                            $icon = 'fa-key';
                                            $color = '#f59e0b';
                                            break;
                                        case 'Update Profile':
                                            $icon = 'fa-user-edit';
                                            $color = '#6366f1';
                                            break;
                                    }
                                    ?>
                                    <span
                                        style="display: inline-flex; align-items: center; gap: 8px; font-weight: 600; color: #1e293b;">
                                        <i class="fas <?= $icon ?>" style="color: <?= $color ?>; width: 20px;"></i>
                                        <?= $log['activity_type'] ?>
                                    </span>
                                </td>
                                <td style="padding: 15px 20px; color: #475569; font-size: 0.9rem;">
                                    <?= htmlspecialchars($log['description']) ?>
                                </td>
                                <td style="padding: 15px 20px; font-family: monospace; font-size: 0.85rem; color: #64748b;">
                                    <?= $log['ip_address'] ?>
                                </td>
                                <td style="padding: 15px 20px; color: #64748b; font-size: 0.85rem;">
                                    <?= date('d M Y, h:i A', strtotime($log['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>