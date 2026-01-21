<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
    :root {
        --primary: #3b82f6;
        --primary-dark: #2563eb;
        --bg-color: #f8fafc;
        --card-bg: #ffffff;
        --text-main: #1e293b;
        --text-sub: #64748b;
        --border-color: #e2e8f0;
    }

    .dashboard-container {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: var(--text-main);
    }

    .layout-container {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 24px;
        align-items: start;
        margin-top: 24px;
    }

    @media(max-width: 1024px) {
        .layout-container {
            grid-template-columns: 1fr;
        }
    }

    /* Sidebar Styling */
    .sidebar-menu {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid var(--border-color);
        position: sticky;
        top: 20px;
    }

    .menu-heading {
        margin: 0 0 12px 12px;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.05em;
        font-weight: 600;
    }

    .menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        color: var(--text-sub);
        text-decoration: none;
        border-radius: 8px;
        margin-bottom: 4px;
        font-weight: 500;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 0.95rem;
    }

    .menu-link:hover {
        background: #f1f5f9;
        color: var(--text-main);
    }

    .menu-link.active {
        background: #eff6ff;
        color: var(--primary);
        font-weight: 600;
    }

    /* Main Content Card */
    .card-main {
        background: var(--card-bg);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        padding: 32px;
        border: 1px solid var(--border-color);
    }

    .header {
        margin-bottom: 30px;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 24px;
    }

    .header h2 {
        margin: 0 0 8px 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -0.025em;
    }

    .header p {
        margin: 0;
        color: var(--text-sub);
        font-size: 0.95rem;
    }

    /* Sortable List Grid */
    .column-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 16px;
    }

    .column-item {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 16px;
        cursor: grab;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        touch-action: none;
    }

    .column-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
        z-index: 10;
    }

    .column-item:active {
        cursor: grabbing;
    }

    .column-item.dragging {
        opacity: 0.5;
        border: 2px dashed var(--primary);
        background: #f8fafc;
        box-shadow: none;
    }

    /* Drag Handle */
    .drag-handle {
        color: #cbd5e1;
        cursor: grab;
        padding: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: color 0.2s;
    }

    .column-item:hover .drag-handle {
        color: #94a3b8;
        background: #f1f5f9;
    }

    /* Content Styling */
    .item-content {
        flex: 1;
        min-width: 0;
        /* Text truncation fix */
    }

    .item-label {
        font-weight: 600;
        color: var(--text-main);
        font-size: 1rem;
        margin-bottom: 4px;
    }

    .item-key {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.75rem;
        color: var(--text-sub);
        background: #f1f5f9;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
    }

    /* Toggle Switch */
    .item-actions {
        display: flex;
        align-items: center;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #e2e8f0;
        transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    input:checked+.slider {
        background-color: var(--primary);
    }

    input:focus+.slider {
        box-shadow: 0 0 1px var(--primary);
    }

    input:checked+.slider:before {
        transform: translateX(20px);
    }

    /* Buttons */
    .actions {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-save {
        background: var(--primary);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        font-size: 0.95rem;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }

    .btn-save:hover {
        background: var(--primary-dark);
        box-shadow: 0 6px 8px -1px rgba(59, 130, 246, 0.4);
    }

    .btn-cancel {
        background: white;
        color: var(--text-sub);
        border: 1px solid #cbd5e1;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .btn-cancel:hover {
        background: #f8fafc;
        color: var(--text-main);
        border-color: #94a3b8;
    }
</style>

<main class="dashboard-container">
    <div class="layout-container">
        <!-- Sidebar -->
        <div class="sidebar-menu">
            <h4 class="menu-heading">Tables</h4>
            <a href="<?= url('setup/column-preview?table=all_customers') ?>"
                class="menu-link <?= $currentTable == 'all_customers' ? 'active' : '' ?>">
                <i class="fas fa-users" style="width: 20px;"></i> All Customers
            </a>
            <a href="<?= url('setup/column-preview?table=pending_customers') ?>"
                class="menu-link <?= $currentTable == 'pending_customers' ? 'active' : '' ?>">
                <i class="fas fa-user-clock" style="width: 20px;"></i> Pending Customers
            </a>
            <a href="<?= url('setup/column-preview?table=recent_customers') ?>"
                class="menu-link <?= $currentTable == 'recent_customers' ? 'active' : '' ?>">
                <i class="fas fa-history" style="width: 20px;"></i> Recent Customers
            </a>
            <a href="<?= url('setup/column-preview?table=complain_list') ?>"
                class="menu-link <?= $currentTable == 'complain_list' ? 'active' : '' ?>">
                <i class="fas fa-exclamation-circle" style="width: 20px;"></i> Complain List
            </a>
            <a href="<?= url('setup/column-preview?table=collection_report') ?>"
                class="menu-link <?= $currentTable == 'collection_report' ? 'active' : '' ?>">
                <i class="fas fa-file-invoice-dollar" style="width: 20px;"></i> Collection Report
            </a>
            <a href="<?= url('setup/column-preview?table=customer_summary') ?>"
                class="menu-link <?= $currentTable == 'customer_summary' ? 'active' : '' ?>">
                <i class="fas fa-file-contract" style="width: 20px;"></i> Customer Summary
            </a>
            <a href="<?= url('setup/column-preview?table=due_list') ?>"
                class="menu-link <?= $currentTable == 'due_list' ? 'active' : '' ?>">
                <i class="fas fa-hand-holding-usd" style="width: 20px;"></i> Due List Report
            </a>
            <a href="<?= url('setup/column-preview?table=inactive_list') ?>"
                class="menu-link <?= $currentTable == 'inactive_list' ? 'active' : '' ?>">
                <i class="fas fa-user-slash" style="width: 20px;"></i> Inactive List
            </a>
        </div>

        <!-- Main Content -->
        <div class="card-main">
            <div class="main-content">
                <div class="header">
                    <h2><?= ucwords(str_replace('_', ' ', $currentTable)) ?></h2>
                    <p>Customize the column visibility and order for this table. Drag cards to reorder.</p>
                </div>

                <form method="POST" action="<?= url('setup/column-preview?table=' . $currentTable) ?>">
                    <?php if (empty($allPossibleColumns)): ?>
                        <div
                            style="padding: 20px; color: #64748b; text-align: center; background: #f8fafc; border-radius: 8px;">
                            No configurable columns for this table.
                        </div>
                    <?php else: ?>
                        <ul class="column-list" id="sortableList">
                            <?php foreach ($allPossibleColumns as $index => $col):
                                $key = $col['key'];
                                $isEnabled = $col['enabled'];
                                ?>
                                <li class="column-item sortable-item" draggable="true" data-key="<?= $key ?>">
                                    <div class="drag-handle" title="Drag to reorder">
                                        <i class="fas fa-grip-vertical"></i>
                                    </div>
                                    <div class="item-content">
                                        <div class="item-label"><?= htmlspecialchars($col['label']) ?></div>
                                        <div class="item-key"><?= htmlspecialchars($key) ?></div>
                                    </div>
                                    <div class="item-actions">
                                        <label class="toggle-switch" title="Toggle Visibility">
                                            <input type="checkbox" name="columns[<?= $index ?>][enabled]" value="1"
                                                <?= $isEnabled ? 'checked' : '' ?>>
                                            <span class="slider"></span>
                                        </label>
                                        <input type="hidden" name="columns[<?= $index ?>][key]" value="<?= $key ?>">
                                        <input type="hidden" name="columns[<?= $index ?>][label]" value="<?= $col['label'] ?>">
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <div class="actions">
                        <a href="<?= url('setup/column-preview?table=' . $currentTable . '&action=reset') ?>"
                            class="btn-cancel"
                            onclick="return confirm('Are you sure you want to reset to default settings? This will clear your custom order and visibility preferences.')">
                            Reset to Default
                        </a>
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const list = document.getElementById('sortableList');
                let draggedItem = null;

                list.addEventListener('dragstart', function (e) {
                    draggedItem = e.target.closest('li');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/html', draggedItem.innerHTML); // Firefox requirement?
                    setTimeout(() => {
                        draggedItem.classList.add('dragging');
                    }, 0);
                });

                list.addEventListener('dragend', function () {
                    draggedItem.classList.remove('dragging');
                    draggedItem = null;
                    updateIndices();

                    // Cleanup visual cues
                    document.querySelectorAll('.sortable-item').forEach(item => {
                        item.classList.remove('drag-over');
                        item.style.borderTop = '';
                        item.style.borderBottom = '';
                    });
                });

                list.addEventListener('dragover', function (e) {
                    e.preventDefault();
                    const afterElement = getDragAfterElement(list, e.clientY);
                    const draggingItem = document.querySelector('.dragging');

                    if (afterElement == null) {
                        list.appendChild(draggingItem);
                    } else {
                        list.insertBefore(draggingItem, afterElement);
                    }
                });

                // Helper to find position
                function getDragAfterElement(container, y) {
                    const draggableElements = [...container.querySelectorAll('.sortable-item:not(.dragging)')];

                    return draggableElements.reduce((closest, child) => {
                        const box = child.getBoundingClientRect();
                        const offset = y - box.top - box.height / 2;
                        if (offset < 0 && offset > closest.offset) {
                            return { offset: offset, element: child };
                        } else {
                            return closest;
                        }
                    }, { offset: Number.NEGATIVE_INFINITY }).element;
                }

                function updateIndices() {
                    const items = list.querySelectorAll('.sortable-item');
                    items.forEach((item, index) => {
                        // Update field names to preserve order
                        const keyInput = item.querySelector('input[type="hidden"][name$="[key]"]');
                        const labelInput = item.querySelector('input[type="hidden"][name$="[label]"]');
                        const enabledInput = item.querySelector('input[type="checkbox"]');

                        if (keyInput) keyInput.name = `columns[${index}][key]`;
                        if (labelInput) labelInput.name = `columns[${index}][label]`;
                        if (enabledInput) enabledInput.name = `columns[${index}][enabled]`;
                    });
                }
            });
        </script>
        <?php require_once __DIR__ . '/../partials/footer.php'; ?>