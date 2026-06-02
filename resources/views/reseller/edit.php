<?php include __DIR__ . '/../partials/header.php'; ?>

<main class="dashboard-container">
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <h2 style="margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Edit Reseller: <?= htmlspecialchars($reseller['company_name']) ?></h2>

        <form action="<?= url('reseller/update/' . $reseller['id']) ?>" method="POST">
            
            <h3 style="color: #3b82f6; margin-top: 10px;">Basic Information</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px;">
                <!-- Company Name -->
                <div class="form-group" style="grid-column: span 2;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Company Name *</label>
                    <input type="text" name="company_name" value="<?= htmlspecialchars($reseller['company_name'] ?? '') ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- Company Address -->
                <div class="form-group" style="grid-column: span 2;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Company Address *</label>
                    <textarea name="company_address" required rows="2" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;"><?= htmlspecialchars($reseller['address'] ?? '') ?></textarea>
                </div>

                <!-- Owner Name -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Owner Name *</label>
                    <input type="text" name="owner_name" value="<?= htmlspecialchars($reseller['contact_person'] ?? '') ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- Mobile No -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Mobile No. *</label>
                    <input type="tel" name="mobile_no" value="<?= htmlspecialchars($reseller['phone'] ?? '') ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- Web Address -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Web Address</label>
                    <input type="url" name="web_address" value="<?= htmlspecialchars($reseller['web_address'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- E-mail Address -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">E-mail Address *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($reseller['email'] ?? '') ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- District -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">District *</label>
                    <select name="district" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        <option value="">Select District</option>
                        <?php 
                        $districts = ['Dhaka','Chittagong','Sylhet','Rajshahi','Khulna','Barisal','Rangpur','Mymensingh'];
                        foreach ($districts as $d): ?>
                            <option value="<?= $d ?>" <?= ($reseller['district'] === $d) ? 'selected' : '' ?>><?= $d ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Thana -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Thana *</label>
                    <input type="text" name="thana" value="<?= htmlspecialchars($reseller['thana'] ?? '') ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- Area -->
                <div class="form-group" style="grid-column: span 2;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Area *</label>
                    <input type="text" name="area_1" value="<?= htmlspecialchars($reseller['area_1'] ?? '') ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
            </div>

            <h3 style="color: #3b82f6; border-top: 1px solid #e2e8f0; padding-top: 20px;">Technical & System Settings</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <!-- Mikrotik Profile -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Mikrotik Profile</label>
                    <select name="mikrotik_profile" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        <option value="">Select Profile</option>
                        <option value="profile_1" <?= ($reseller['mikrotik_id'] == 1) ? 'selected' : '' ?>>Profile 1</option>
                        <option value="profile_2" <?= ($reseller['mikrotik_id'] == 2) ? 'selected' : '' ?>>Profile 2</option>
                    </select>
                </div>

                <!-- PPPoE Prefix -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">PPPoE Username Prefix</label>
                    <input type="text" name="pppoe_prefix" value="<?= htmlspecialchars($reseller['pppoe_prefix'] ?? '') ?>" placeholder="e.g. res1_" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- Child Percentage -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Child Percentage (%)</label>
                    <input type="number" step="0.01" name="child_percentage" value="<?= htmlspecialchars($reseller['child_percentage'] ?? '0') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- Current Balance -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Balance</label>
                    <input type="number" step="0.01" name="balance" value="<?= htmlspecialchars($reseller['balance'] ?? '0') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;" readonly>
                    <small style="color: #64748b;">Use transaction modal to adjust balance.</small>
                </div>
                
                <!-- Google Drive Link -->
                <div class="form-group" style="grid-column: span 2;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Google Drive Folder Link</label>
                    <input type="url" name="drive_link" value="<?= htmlspecialchars($reseller['drive_link'] ?? '') ?>" placeholder="https://drive.google.com/..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                
                <!-- Toggles -->
                <div class="form-group" style="grid-column: span 2; display: flex; gap: 20px; align-items: center; margin-top: 10px;">
                    <label style="display:flex; align-items:center; gap: 8px; cursor:pointer;">
                        <input type="checkbox" name="auto_deduct" value="1" <?= !empty($reseller['auto_deduct']) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                        Enable Auto Deduct
                    </label>
                    <label style="display:flex; align-items:center; gap: 8px; cursor:pointer;">
                        <input type="checkbox" name="enable_with_payment" value="1" <?= !empty($reseller['enable_with_payment']) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                        Enable with Payment
                    </label>
                    <label style="display:flex; align-items:center; gap: 8px; cursor:pointer;">
                        <input type="checkbox" name="advance_payment" value="1" <?= !empty($reseller['advance_payment']) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                        Advance Payment Only
                    </label>
                </div>
            </div>

            <div style="margin-top: 30px; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <a href="<?= url('reseller') ?>" style="padding: 10px 20px; text-decoration: none; color: #475569; margin-right: 15px;">Cancel</a>
                <button type="submit" style="padding: 10px 25px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Update Reseller</button>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>
