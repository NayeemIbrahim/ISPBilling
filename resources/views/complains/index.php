<?php include __DIR__ . '/../partials/header.php'; ?>

<?php
/**
 * Helper to generate sorting links.
 */
function sortLink($field, $currentSort, $currentOrder, $q)
{
    $order = ($field == $currentSort && $currentOrder == 'ASC') ? 'DESC' : 'ASC';
    return url("complain-list?q=" . urlencode($q) . "&sort=$field&order=$order");
}

/**
 * Helper to display sorting icons.
 */
function sortIcon($field, $currentSort, $currentOrder)
{
    if ($field != $currentSort)
        return '<i class="fas fa-sort sort-icon-inactive"></i>';
    return $currentOrder == 'ASC' ? '<i class="fas fa-sort-up sort-icon-active"></i>' : '<i class="fas fa-sort-down sort-icon-active"></i>';
}
?>

<div class="dashboard-container">
    <div class="card no-print-padding">
        <div class="header-actions">
            <h2>Customer Complain List</h2>
            <div class="action-group">
                <form method="GET" action="<?= url('complain-list') ?>" class="search-form">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" name="q" placeholder="Search name, mobile, issue..."
                            value="<?= htmlspecialchars($q ?? '') ?>">
                    </div>
                </form>
                <div class="column-selector-wrapper">
                    <button type="button" class="btn-secondary" id="colPickerBtn">
                        <i class="fas fa-columns"></i> Columns
                    </button>
                    <div class="column-picker-dropdown" id="colPickerDropdown">
                        <label><input type="checkbox" class="col-toggle" data-col="0" checked> ID</label>
                        <label><input type="checkbox" class="col-toggle" data-col="1" checked> Customer Info</label>
                        <label><input type="checkbox" class="col-toggle" data-col="2" checked> Issue</label>
                        <label><input type="checkbox" class="col-toggle" data-col="3" checked> Assigned To</label>
                        <label><input type="checkbox" class="col-toggle" data-col="4" checked> Status</label>
                        <label><input type="checkbox" class="col-toggle" data-col="5" checked> Date</label>
                    </div>
                </div>
                <button onclick="window.print()" class="btn-secondary"><i class="fas fa-print"></i> Print</button>
                <a href="<?= url('complain-list/create') ?>" class="btn-primary"><i class="fas fa-plus"></i> New
                    Complain</a>
            </div>
        </div>

        <div class="table-container">
            <table class="data-table selectable-table">
                <thead>
                    <tr>
                        <th width="8%"><a href="<?= sortLink('id', $sort, $order, $q) ?>" class="table-sort-link">ID
                                <?= sortIcon('id', $sort, $order) ?></a></th>
                        <th width="22%"><a href="<?= sortLink('full_name', $sort, $order, $q) ?>"
                                class="table-sort-link">Customer Info <?= sortIcon('full_name', $sort, $order) ?></a>
                        </th>
                        <th width="20%"><a href="<?= sortLink('complain_title', $sort, $order, $q) ?>"
                                class="table-sort-link">Issue <?= sortIcon('complain_title', $sort, $order) ?></a></th>
                        <th width="15%">Assigned To</th>
                        <th width="12%"><a href="<?= sortLink('status', $sort, $order, $q) ?>"
                                class="table-sort-link">Status <?= sortIcon('status', $sort, $order) ?></a></th>
                        <th width="13%"><a href="<?= sortLink('created_at', $sort, $order, $q) ?>"
                                class="table-sort-link">Date <?= sortIcon('created_at', $sort, $order) ?></a></th>
                        <th width="10%" class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($complains)): ?>
                        <?php foreach ($complains as $complain): ?>
                            <tr>
                                <td>#<?= $complain['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($complain['full_name']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($complain['area']) ?></small><br>
                                    <small class="text-muted"><?= htmlspecialchars($complain['mobile_no']) ?></small>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-gray"><?= htmlspecialchars($complain['complain_title']) ?></span><br>
                                    <small class="description-small"><?= htmlspecialchars($complain['description']) ?></small>
                                </td>
                                <td>
                                    <div class="assigned-chips">
                                        <?php
                                        $assignedIds = json_decode($complain['assigned_to'] ?? '[]', true);
                                        if ($assignedIds) {
                                            foreach ($assignedIds as $eid) {
                                                $name = $employees[$eid] ?? 'Unknown';
                                                echo "<span class='employee-chip'>$name</span>";
                                            }
                                        } else {
                                            echo '<span class="unassigned-text">Unassigned</span>';
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $statusBy = $complain['status'];
                                    $statusClass = 'status-pending';
                                    if ($statusBy == 'In Progress')
                                        $statusClass = 'status-progress';
                                    if ($statusBy == 'Resolved')
                                        $statusClass = 'status-resolved';
                                    ?>
                                    <span class="status-indicator <?= $statusClass ?>">
                                        <i class="fas fa-circle"></i> <?= $statusBy ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y h:i A', strtotime($complain['created_at'])) ?></td>
                                <td class="no-print">
                                    <div class="action-buttons">
                                        <a href="<?= url('complain-list/edit/' . $complain['id']) ?>"
                                            class="action-btn edit-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?= url('complain-list/delete/' . $complain['id']) ?>" method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this complaint?');">
                                            <button type="submit" class="action-btn delete-btn" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 40px;">No complaints found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 15px;
    }

    .action-group {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .search-input-wrapper input {
        padding: 8px 12px 8px 35px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        width: 250px;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .search-input-wrapper input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .btn-secondary {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .table-sort-link {
        color: #475569;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sort-icon-inactive {
        opacity: 0.2;
        font-size: 0.8rem;
    }

    .sort-icon-active {
        color: #3b82f6;
    }

    .employee-chip {
        background: #f1f5f9;
        color: #64748b;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-block;
        margin-right: 4px;
        margin-bottom: 4px;
        border: 1px solid #e2e8f0;
    }

    .unassigned-text {
        font-style: italic;
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .status-pending {
        background: #fff7ed;
        color: #ea580c;
    }

    .status-progress {
        background: #eff6ff;
        color: #2563eb;
    }

    .status-resolved {
        background: #f0fdf4;
        color: #16a34a;
    }

    .status-indicator i {
        font-size: 0.5rem;
    }

    .description-small {
        color: #64748b;
        font-size: 0.8rem;
        display: block;
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
    }

    .edit-btn {
        background: #eff6ff;
        color: #2563eb;
    }

    .edit-btn:hover {
        background: #3b82f6;
        color: white;
    }

    .delete-btn {
        background: #fef2f2;
        color: #dc2626;
        border: none;
        cursor: pointer;
    }

    .delete-btn:hover {
        background: #ef4444;
        color: white;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .dashboard-container {
            padding: 0 !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .no-print-padding {
            padding: 0 !important;
        }

        .data-table {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #dee2e6 !important;
            padding: 8px !important;
        }

        .table-sort-link .fas {
            display: none;
        }

        /* Hide columns marked as hidden */
        .col-hidden {
            display: none !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pickerBtn = document.getElementById('colPickerBtn');
        const pickerDropdown = document.getElementById('colPickerDropdown');
        const toggles = document.querySelectorAll('.col-toggle');
        const table = document.querySelector('.data-table');
        const STORAGE_KEY = 'complain_list_columns';

        // Toggle dropdown
        pickerBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            pickerDropdown.classList.toggle('active');
        });

        document.addEventListener('click', () => {
            pickerDropdown.classList.remove('active');
        });

        pickerDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Load saved preferences
        let preferences = JSON.parse(localStorage.getItem(STORAGE_KEY)) || {};

        toggles.forEach(checkbox => {
            const colIndex = checkbox.dataset.col;
            if (preferences[colIndex] === false) {
                checkbox.checked = false;
                toggleColumn(colIndex, false);
            }

            checkbox.addEventListener('change', function () {
                toggleColumn(colIndex, this.checked);
                preferences[colIndex] = this.checked;
                localStorage.setItem(STORAGE_KEY, JSON.stringify(preferences));
            });
        });

        function toggleColumn(index, show) {
            const rows = table.rows;
            for (let i = 0; i < rows.length; i++) {
                const cell = rows[i].cells[index];
                if (cell) {
                    if (show) {
                        cell.classList.remove('col-hidden');
                    } else {
                        cell.classList.add('col-hidden');
                    }
                }
            }
        }
    });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>