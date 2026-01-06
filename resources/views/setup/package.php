<?php require_once __DIR__ . '/../partials/header.php'; ?>

<main class="dashboard-container">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Package Setup</h2>
        </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $messageType ?>"
            style="margin-bottom: 20px; padding: 10px; border-radius: 5px; background-color: <?= $messageType === 'success' ? '#d1fae5' : '#fee2e2' ?>; color: <?= $messageType === 'success' ? '#065f46' : '#991b1b' ?>;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="content-grid" style="grid-template-columns: 1fr; gap: 30px;">
        
        <!-- Create Package Section -->
            <div class="card-header" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
                <h3 id="formTitle" style="color: #1e293b; font-size: 1.1rem; font-weight: 600;">Create Package</h3>
                <button type="button" id="cancelEdit" style="display:none; background: #94a3b8; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Cancel Edit</button>
            </div>
            <div class="card-body" style="padding: 25px;">
                <form action="<?= url('setup/package') ?>" method="POST" id="packageForm">
                    <input type="hidden" name="id" id="packageId">
                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; color: #475569; font-weight: 500;">Package Name</label>
                            <input type="text" name="name" id="packageName" placeholder="Enter Name" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; color: #475569; font-weight: 500;">Price</label>
                            <input type="number" name="price" id="packagePrice" placeholder="Enter Price" required step="0.01" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;">
                        </div>
                    </div>
                    
                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; color: #475569; font-weight: 500;">Description</label>
                            <input type="text" name="description" id="packageDescription" placeholder="Enter Description" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; color: #475569; font-weight: 500;">Merchant Company Setup</label>
                            <select name="merchant_id" id="packageMerchant" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; background-color: white;">
                                <option value="">Select Merchant Company Setup</option>
                                <?php foreach ($merchants as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions" style="text-align: right;">
                        <button type="submit" id="submitBtn" class="btn-primary" style="padding: 10px 25px; font-weight: 600; letter-spacing: 0.5px;">Save Package</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Package List Section -->
        <div class="card" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
            <div class="card-header" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 15px 20px; justify-content: space-between; display: flex; align-items: center;">
                <h3 style="color: #1e293b; font-size: 1.1rem; font-weight: 600;">Package List</h3>
                <div class="search-box">
                    <input type="text" id="packageSearch" placeholder="Search packages..." style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; width: 250px;">
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table" id="packageTable" style="margin: 0; border-collapse: collapse; width: 100%;">
                    <thead style="background-color: #f1f5f9;">
                        <tr>
                            <th style="padding: 15px 20px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0;">Sl.</th>
                            <th style="padding: 15px 20px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0;">Name</th>
                            <th style="padding: 15px 20px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0;">Price</th>
                            <th style="padding: 15px 20px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0;">Description</th>
                            <th style="padding: 15px 20px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0;">Merchant Company Setup</th>
                            <th style="padding: 15px 20px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($packages)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding: 30px; color: #64748b;">No packages found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($packages as $k => $p): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background-color 0.2s;">
                                    <td style="padding: 12px 20px; color: #334155;"><?= $k + 1 ?></td>
                                    <td style="padding: 12px 20px; font-weight: 500; color: #1e293b;"><?= $p['name'] ?></td>
                                    <td style="padding: 12px 20px; font-weight: 600; color: #059669;"><?= number_format($p['price'], 2) ?></td>
                                    <td style="padding: 12px 20px; color: #64748b;"><?= $p['description'] ?></td>
                                    <td style="padding: 12px 20px; color: #334155;"><?= $p['merchant_name'] ?? '-' ?></td>
                                    <td style="padding: 12px 20px;">
                                        <button class="btn-sm edit-btn" 
                                            style="background:#3b82f6; color:white; border:none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85rem;" 
                                            data-id="<?= $p['id'] ?>"
                                            data-name="<?= htmlspecialchars($p['name']) ?>"
                                            data-price="<?= $p['price'] ?>"
                                            data-description="<?= htmlspecialchars($p['description'] ?? '') ?>"
                                            data-merchant="<?= $p['merchant_id'] ?? '' ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    </div>
</main>

<script>
    // Edit Functionality
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Populate Form
            document.getElementById('packageId').value = this.dataset.id;
            document.getElementById('packageName').value = this.dataset.name;
            document.getElementById('packagePrice').value = this.dataset.price;
            document.getElementById('packageDescription').value = this.dataset.description;
            document.getElementById('packageMerchant').value = this.dataset.merchant;
            
            // Change UI State
            document.getElementById('formTitle').textContent = 'Edit Package';
            document.getElementById('submitBtn').textContent = 'Update Package';
            document.getElementById('cancelEdit').style.display = 'block';
            
            // Scroll to form
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // Cancel Edit
    document.getElementById('cancelEdit').addEventListener('click', function() {
        // Reset Form
        document.getElementById('packageForm').reset();
        document.getElementById('packageId').value = '';
        
        // Reset UI State
        document.getElementById('formTitle').textContent = 'Create Package';
        document.getElementById('submitBtn').textContent = 'Save Package';
        this.style.display = 'none';
    });

    // Simple Client-Side Search
    document.getElementById('packageSearch').addEventListener('keyup', function () {
        let filter = this.value.toUpperCase();
        let rows = document.querySelector("#packageTable tbody").rows;

        for (let i = 0; i < rows.length; i++) {
            let cells = rows[i].getElementsByTagName("td");
            let found = false;
            if (cells.length > 1) { // Skip empty/loading rows
                // Check Name (1), Price (2), Description (3), Merchant (4)
                if (cells[1].textContent.toUpperCase().indexOf(filter) > -1 ||
                    cells[2].textContent.toUpperCase().indexOf(filter) > -1 ||
                    cells[3].textContent.toUpperCase().indexOf(filter) > -1 ||
                    (cells[4] && cells[4].textContent.toUpperCase().indexOf(filter) > -1)) {
                    found = true;
                }
                rows[i].style.display = found ? "" : "none";
            }
        }
    });
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>