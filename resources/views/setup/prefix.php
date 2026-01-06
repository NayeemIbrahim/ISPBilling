<?php include __DIR__ . '/../partials/header.php'; ?>

<main class="dashboard-container">
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h2>ID Prefix Setup</h2>
        </div>

        <div style="display:grid; grid-template-columns: 350px 1fr; gap: 30px; margin-top: 20px;">
            <!-- Form Side -->
            <div>
                <form action="<?= url('prefix/store') ?>" method="POST" id="prefixForm">
                    <input type="hidden" name="id" id="prefixId">
                    <div class="form-group">
                        <label>Prefix Code (e.g., HK_, ISP-)</label>
                        <input type="text" name="prefix_code" id="prefixCode" placeholder="Enter prefix..." required
                            style="padding:10px; border:1px solid #ddd; border-radius:4px; width:100%;">
                    </div>
                    <div style="margin-top:15px;">
                        <button type="submit" class="btn-primary" id="submitBtn" style="width:100%;">Create
                            Prefix</button>
                        <button type="button" onclick="resetForm()" id="cancelBtn"
                            style="width:100%; margin-top:10px; display:none; padding:10px; border:1px solid #ddd; background:#f8fafc; cursor:pointer;">Cancel
                            Edit</button>
                    </div>
                </form>
            </div>

            <!-- List Side -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Default</th>
                            <th>Prefix Code</th>
                            <th>Customers Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prefixes as $p): ?>
                            <tr>
                                <td style="text-align:center;">
                                    <input type="checkbox" <?= $p['is_default'] ? 'checked' : '' ?>
                                        onclick="window.location.href='<?= url('prefix/setDefault/' . $p['id']) ?>'">
                                </td>
                                <td><strong><?= htmlspecialchars($p['prefix_code']) ?></strong></td>
                                <td style="text-align:center;">
                                    <span
                                        style="background:#e2e8f0; padding:2px 8px; border-radius:10px; font-weight:bold; font-size:12px;">
                                        <?= $p['customer_count'] ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-secondary"
                                        onclick="editPrefix(<?= $p['id'] ?>, '<?= htmlspecialchars($p['prefix_code']) ?>')"
                                        style="padding:5px 10px; font-size:12px;">Edit</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    function editPrefix(id, code) {
        document.getElementById('prefixId').value = id;
        document.getElementById('prefixCode').value = code;
        document.getElementById('submitBtn').innerText = 'Update Prefix';
        document.getElementById('cancelBtn').style.display = 'block';
    }

    function resetForm() {
        document.getElementById('prefixId').value = '';
        document.getElementById('prefixCode').value = '';
        document.getElementById('submitBtn').innerText = 'Create Prefix';
        document.getElementById('cancelBtn').style.display = 'none';
    }
</script>

<style>
    .btn-secondary {
        background: #64748b;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background: #475569;
    }

    input[type="checkbox"] {
        transform: scale(1.5);
        cursor: pointer;
    }
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>