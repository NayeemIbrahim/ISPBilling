<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="dashboard-container">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2>Customer Complain List</h2>
            <a href="<?= url('complain-list/create') ?>" class="btn-primary">New Complain</a>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Info</th>
                        <th>Issue</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($complains)): ?>
                        <?php foreach ($complains as $complain): ?>
                            <tr>
                                <td>#<?= $complain['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($complain['full_name']) ?></strong><br>
                                    <small><?= htmlspecialchars($complain['area']) ?></small><br>
                                    <small><?= htmlspecialchars($complain['mobile_no']) ?></small>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-gray"><?= htmlspecialchars($complain['complain_title']) ?></span><br>
                                    <small><?= htmlspecialchars($complain['description']) ?></small>
                                </td>
                                <td>
                                    <?php
                                    $assignedIds = json_decode($complain['assigned_to'] ?? '[]', true);
                                    if ($assignedIds) {
                                        foreach ($assignedIds as $eid) {
                                            $name = $employees[$eid] ?? 'Unknown';
                                            echo "<span class='badge' style='background:#e2e8f0; color:#333; margin-right:2px;'>$name</span>";
                                        }
                                    } else {
                                        echo '<span style="color:#999;">Unassigned</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $statusBy = $complain['status'];
                                    $color = '#f59e0b'; // Pending - orange
                                    if ($statusBy == 'In Progress')
                                        $color = '#3b82f6'; // blue
                                    if ($statusBy == 'Resolved')
                                        $color = '#10b981'; // green
                                    ?>
                                    <span style="color:<?= $color ?>; font-weight:bold;"><?= $statusBy ?></span>
                                </td>
                                <td><?= date('d M, Y h:i A', strtotime($complain['created_at'])) ?></td>
                                <td>
                                    <button class="btn-edit"
                                        onclick="window.location.href='<?= url('complain-list/edit/' . $complain['id']) ?>'">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="<?= url('complain-list/delete/' . $complain['id']) ?>" method="POST"
                                        style="display:inline;"
                                        onsubmit="return confirm('Are you sure you want to delete this complaint?');">
                                        <button type="submit" class="btn-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center;">No complaints found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .badge {
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        display: inline-block;
    }

    .badge-gray {
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
    }

    .btn-edit,
    .btn-delete {
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        margin-right: 5px;
        transition: all 0.2s;
    }

    .btn-edit {
        background: #3b82f6;
        color: white;
    }

    .btn-edit:hover {
        background: #2563eb;
    }

    .btn-delete {
        background: #ef4444;
        color: white;
    }

    .btn-delete:hover {
        background: #dc2626;
    }
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>