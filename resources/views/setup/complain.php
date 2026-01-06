<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="dashboard-container">
    <div class="setup-grid">
        <!-- Create/Edit Form -->
        <div class="card">
            <h3 id="formTitle">Create A Complain Type</h3>
            <form action="<?= url('complain/store') ?>" method="POST" id="complainForm">
                <input type="hidden" name="id" id="complainId">

                <div class="form-group">
                    <label>Complain Title</label>
                    <input type="text" name="title" id="complainTitle" required placeholder="e.g. Slow Internet">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="complainDescription" rows="3"
                        placeholder="Optional description..."></textarea>
                </div>

                <div class="form-actions" style="margin-top: 20px; display: flex; gap: 10px;">
                    <button type="submit" class="btn-primary" id="saveBtn">Save Complain</button>
                    <button type="button" class="btn-secondary" id="cancelEdit" style="display: none;">Cancel</button>
                </div>
            </form>
        </div>

        <!-- List Table -->
        <div class="card">
            <h3>Complain List</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($complains)): ?>
                            <?php foreach ($complains as $index => $complain): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($complain['title']) ?></strong></td>
                                    <td><?= htmlspecialchars($complain['description']) ?></td>
                                    <td>
                                        <button class="btn-edit" data-id="<?= $complain['id'] ?>"
                                            data-title="<?= htmlspecialchars($complain['title']) ?>"
                                            data-description="<?= htmlspecialchars($complain['description']) ?>">
                                            Edit
                                        </button>

                                        <form action="<?= url('complain/delete/' . $complain['id']) ?>" method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this complain type?');">
                                            <button type="submit" class="btn-delete">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center;">No complain types found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .setup-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 20px;
        align-items: start;
    }

    .btn-edit {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 5px;
    }

    .btn-delete {
        background: #ef4444;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
    }

    @media (max-width: 900px) {
        .setup-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formTitle = document.getElementById('formTitle');
        const complainForm = document.getElementById('complainForm');
        const complainId = document.getElementById('complainId');
        const complainTitle = document.getElementById('complainTitle');
        const complainDescription = document.getElementById('complainDescription');
        const saveBtn = document.getElementById('saveBtn');
        const cancelEdit = document.getElementById('cancelEdit');

        // Edit Button Click
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function () {
                // Populate fields
                complainId.value = this.dataset.id;
                complainTitle.value = this.dataset.title;
                complainDescription.value = this.dataset.description;

                // Change UI
                formTitle.textContent = 'Edit Complain Type';
                saveBtn.textContent = 'Update Complain';
                cancelEdit.style.display = 'block';

                // Scroll to form
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

        // Cancel Edit Click
        cancelEdit.addEventListener('click', function () {
            // Reset Form
            complainForm.reset();
            complainId.value = '';

            // Reset UI
            formTitle.textContent = 'Create A Complain Type';
            saveBtn.textContent = 'Save Complain';
            this.style.display = 'none';
        });
    });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>