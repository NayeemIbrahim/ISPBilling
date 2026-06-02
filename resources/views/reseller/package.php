<?php include __DIR__ . '/../partials/header.php'; ?>

<main class="dashboard-container">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:20px;">
        <!-- Left Side: Package List -->
        <div class="card" style="flex: 2;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h2 style="margin:0;">Reseller Package List</h2>
            </div>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sl.</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Reseller</th>
                            <th>Mikrotik Profile</th>
                            <th>Sort</th>
                            <th class="no-print">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($packages)): ?>
                            <?php foreach ($packages as $index => $pkg): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($pkg['name']) ?></strong></td>
                                    <td><?= number_format($pkg['price'], 2) ?></td>
                                    <td><?= htmlspecialchars($pkg['reseller_name'] ?? 'All Resellers') ?></td>
                                    <td><?= htmlspecialchars($pkg['mikrotik_profile'] ?? '-') ?></td>
                                    <td><?= $pkg['sort'] ?></td>
                                    <td class="no-print">
                                        <button onclick='editPackage(<?= json_encode($pkg) ?>)' class="btn-table" style="background:#10b981; margin-right:5px;">Edit</button>
                                        <form action="<?= url('reseller/deletePackage/' . $pkg['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this package?');">
                                            <button type="submit" class="btn-table" style="background:#ef4444;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align:center; padding:20px;">No packages found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side: Create/Edit Form -->
        <div class="card" style="flex: 1; min-width: 300px;">
            <h3 id="formTitle" style="margin-top:0; border-bottom:1px solid #e2e8f0; padding-bottom:10px;">Create Package</h3>
            
            <form id="packageForm" action="<?= url('reseller/storePackage') ?>" method="POST">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Name *</label>
                    <input type="text" name="name" id="pkg_name" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Price *</label>
                    <input type="number" step="0.01" name="price" id="pkg_price" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Reseller</label>
                    <select name="reseller_id" id="pkg_reseller" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        <option value="">-- All Resellers --</option>
                        <?php foreach ($resellers as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['company_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Mikrotik</label>
                    <select name="mikrotik_id" id="pkg_mikrotik" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        <option value="">Select Mikrotik</option>
                        <option value="1">Main Router</option>
                        <option value="2">Backup Router</option>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Profile Name</label>
                    <input type="text" name="mikrotik_profile" id="pkg_profile" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Sort Order</label>
                    <input type="number" name="sort" id="pkg_sort" value="0" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                
                <div style="display:flex; justify-content:space-between;">
                    <button type="button" onclick="resetForm()" class="btn-secondary" style="padding: 8px 15px;">Reset</button>
                    <button type="submit" id="submitBtn" style="padding: 8px 15px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor:pointer;">Save</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    function editPackage(pkg) {
        document.getElementById('formTitle').innerText = 'Edit Package';
        document.getElementById('submitBtn').innerText = 'Update';
        document.getElementById('submitBtn').style.background = '#10b981';
        
        document.getElementById('packageForm').action = '<?= url('reseller/updatePackage/') ?>' + pkg.id;
        
        document.getElementById('pkg_name').value = pkg.name;
        document.getElementById('pkg_price').value = pkg.price;
        document.getElementById('pkg_reseller').value = pkg.reseller_id || '';
        document.getElementById('pkg_mikrotik').value = pkg.mikrotik_id || '';
        document.getElementById('pkg_profile').value = pkg.mikrotik_profile || '';
        document.getElementById('pkg_sort').value = pkg.sort || '0';
    }
    
    function resetForm() {
        document.getElementById('formTitle').innerText = 'Create Package';
        document.getElementById('submitBtn').innerText = 'Save';
        document.getElementById('submitBtn').style.background = '#3b82f6';
        
        document.getElementById('packageForm').action = '<?= url('reseller/storePackage') ?>';
        document.getElementById('packageForm').reset();
    }
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
