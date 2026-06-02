<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
    .panel-heading {
        background: #3b82f6;
        color: white;
        padding: 10px 15px;
        font-weight: 600;
        border-radius: 4px 4px 0 0;
    }
    .panel-body {
        background: white;
        padding: 15px;
        border: 1px solid #e2e8f0;
        border-top: none;
        border-radius: 0 0 4px 4px;
    }
    .sms-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .sms-list-item label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-size: 14px;
        color: #334155;
    }
    .btn-edit-sm {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-edit-sm:hover {
        background: #2563eb;
    }
    .btn-primary-custom {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.2s;
    }
    .btn-primary-custom:hover {
        background: #2563eb;
    }
</style>

<main class="dashboard-container" style="background: #f8fafc; padding: 20px;">
    <h3 style="margin-top: 0; margin-bottom: 20px; font-weight: 600; color: #1e293b;">SMS Setup</h3>

    <?php if (isset($_GET['success'])): ?>
        <div style="background: #dcfce7; color: #166534; padding: 10px 15px; border-radius: 6px; margin-bottom: 20px;">
            Settings updated successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Super Admin'): ?>
    <!-- API Configuration (Visible to Super Admin Only) -->
    <div style="margin-bottom: 30px;">
        <div class="panel-heading" style="background: #3b82f6;">API Configuration (Super Admin Only)</div>
        <div class="panel-body">
            <form action="<?= url('sms/update') ?>" method="POST" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600; font-size:13px;">API URL</label>
                    <input type="url" name="api_url" value="<?= htmlspecialchars($settings['api_url'] ?? '') ?>" placeholder="e.g. http://api.sms-provider.com/send" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600; font-size:13px;">API Key / Password</label>
                    <input type="text" name="api_key" value="<?= htmlspecialchars($settings['api_key'] ?? '') ?>" placeholder="Your API Key" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600; font-size:13px;">Sender ID / Masking</label>
                    <input type="text" name="sender_id" value="<?= htmlspecialchars($settings['sender_id'] ?? '') ?>" placeholder="e.g. ISP_NAME" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600; font-size:13px;">Client ID (Optional)</label>
                    <input type="text" name="client_id" value="<?= htmlspecialchars($settings['client_id'] ?? '') ?>" placeholder="Client ID / Username" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn-primary-custom" style="background: #3b82f6;">Save API</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-start;">
        
        <!-- Column 1: SMS Balance -->
        <div style="flex: 1; min-width: 250px; max-width: 300px;">
            <div class="panel-heading">SMS Balance</div>
            <div class="panel-body" style="padding: 0;">
                <div style="padding: 15px; border-bottom: 1px solid #e2e8f0;">
                    <div style="color: #64748b; font-size: 14px;">Current Credit</div>
                    <div style="font-size: 20px; font-weight: bold; color: #334155;">109.89</div>
                </div>
                <div style="padding: 15px;">
                    <a href="#" style="color: #3b82f6; text-decoration: none; font-size: 14px; font-weight: 600;">Buy SMS</a>
                </div>
            </div>
        </div>

        <!-- Column 2: SMS Settings -->
        <div style="flex: 2; min-width: 300px;">
            <div class="panel-heading">SMS Settings</div>
            <div class="panel-body">
                <form action="<?= url('sms/update') ?>" method="POST">
                    <input type="hidden" name="update_toggles" value="1">
                    <?php foreach($templates as $tpl): ?>
                        <?php if($tpl['event_name'] !== 'Reminder'): ?>
                            <div class="sms-list-item">
                                <label>
                                    <input type="checkbox" name="settings[]" value="<?= htmlspecialchars($tpl['event_name']) ?>" <?= $tpl['is_active'] ? 'checked' : '' ?>>
                                    <?= htmlspecialchars($tpl['event_name']) ?>
                                </label>
                                <button type="button" class="btn-edit-sm" onclick="openTemplateModal(<?= $tpl['id'] ?>, '<?= addslashes($tpl['event_name']) ?>')">Edit</button>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <?php 
                    // Render Reminder specifically at the bottom if it exists
                    $reminderTpl = array_filter($templates, function($t) { return $t['event_name'] === 'Reminder'; });
                    if (!empty($reminderTpl)):
                        $rTpl = reset($reminderTpl);
                    ?>
                    <div class="sms-list-item">
                        <label>
                            <input type="checkbox" name="settings[]" value="Reminder" <?= $rTpl['is_active'] ? 'checked' : '' ?>>
                            Reminder <a href="#" style="margin-left: 5px; text-decoration:none; color: #3b82f6;">2</a> <span style="color:#ef4444; font-weight:bold; cursor:pointer; margin-left:5px;">&times;</span> <span style="color:#10b981; font-weight:bold; cursor:pointer; margin-left:5px;">+</span>
                        </label>
                        <button type="button" class="btn-edit-sm" onclick="openTemplateModal(<?= $rTpl['id'] ?>, 'Reminder')">Edit</button>
                    </div>
                    <?php endif; ?>

                    <div style="margin-top: 15px;">
                        <button type="submit" class="btn-primary-custom">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Column 3: Masking & Default -->
        <div style="flex: 1; min-width: 250px; max-width: 350px;">
            <!-- Masking / Brand Name -->
            <div class="panel-heading" style="display:flex; justify-content:space-between; align-items:center;">
                Masking / Brand Name
                <span style="cursor:pointer;">&#9650;</span>
            </div>
            <div class="panel-body">
                <form action="#" method="POST">
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600; font-size:13px;">Masking /Brand Name <span style="color:red;">*</span></label>
                        <input type="text" placeholder="Masking Name" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                    <div style="margin-bottom: 15px; display:flex; align-items:center; justify-content:space-between;">
                        <label style="font-weight:600; font-size:13px; margin:0;">National ID Card</label>
                        <input type="file" style="width: 100px; font-size:11px;">
                    </div>
                    <div style="margin-bottom: 15px; display:flex; align-items:center; justify-content:space-between;">
                        <label style="font-weight:600; font-size:13px; margin:0;">Trade License</label>
                        <input type="file" style="width: 100px; font-size:11px;">
                    </div>
                    <div style="margin-bottom: 20px; display:flex; align-items:center; justify-content:space-between;">
                        <label style="font-weight:600; font-size:13px; margin:0;">others</label>
                        <input type="file" style="width: 100px; font-size:11px;">
                    </div>
                    <button type="button" class="btn-primary-custom">Upload</button>
                </form>
            </div>

            <!-- Select Default -->
            <div class="panel-heading" style="margin-top: 20px;">Select One as Default</div>
            <div class="panel-body">
                <label style="display:flex; align-items:center; gap: 10px; cursor: pointer; font-size: 14px; color: #334155;">
                    <input type="checkbox" checked>
                    No Masking (Tk .49/sms)
                </label>
            </div>
        </div>

    </div>
</main>

<!-- Edit Template Modal -->
<div class="modal-overlay" id="templateModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:white; padding:25px; border-radius:12px; width:500px; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-bottom:1px solid #e2e8f0; padding-bottom:15px;">
            <h3 id="templateModalTitle" style="margin:0;">Edit Template</h3>
            <button onclick="closeTemplateModal()" style="background:none; border:none; font-size:20px; cursor:pointer;">&times;</button>
        </div>
        
        <div style="background: #f1f5f9; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 12px; color: #475569;">
            <strong>Available Variables:</strong> <code>[name]</code>, <code>[user_id]</code>, <code>[amount]</code>, <code>[due_date]</code>, <code>[company]</code>, <code>[phone]</code>
        </div>

        <form action="<?= url('sms/saveTemplate') ?>" method="POST">
            <input type="hidden" name="id" id="templateId">
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Message Content</label>
                <textarea name="message" id="templateMessage" rows="5" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: monospace;"></textarea>
            </div>
            
            <div style="text-align:right; margin-top: 20px;">
                <button type="button" onclick="closeTemplateModal()" style="padding: 8px 15px; border:none; background:#e2e8f0; border-radius:6px; cursor:pointer; margin-right:10px;">Cancel</button>
                <button type="submit" class="btn-primary-custom">Save Template</button>
            </div>
        </form>
    </div>
</div>

<script>
    const placeholders = {
        'Create Customer': 'Dear [name], welcome to [company]! Your User ID is [user_id].',
        'Bill Generate': 'Dear [name], your bill of ৳[amount] for this month has been generated. Due date: [due_date].',
        'Collection': 'Dear [name], we have successfully received your payment of ৳[amount]. Thank you for staying with [company].',
        'Reminder': 'Dear [name], friendly reminder: Your internet bill of ৳[amount] is due on [due_date]. Please pay promptly to avoid disconnection.',
        'Auto Temporary Disable Alert': 'Dear [name], your account [user_id] will be temporarily disabled soon due to unpaid dues of ৳[amount].',
        'Complain to Customer': 'Dear [name], we have received your complaint. Our team is working on it and will resolve it shortly.',
        'Inactive Customer List': 'Dear [name], your account [user_id] is currently inactive. Please contact [company] at [phone] for reactivation.',
        'default': 'Type your custom SMS message here using variables like [name], [amount], etc.'
    };

    function openTemplateModal(id, eventName) {
        document.getElementById('templateModalTitle').innerText = 'Edit Template: ' + eventName;
        document.getElementById('templateId').value = id;
        document.getElementById('templateMessage').value = 'Loading...';
        
        // Set dynamic placeholder
        let placeholderText = placeholders[eventName] || placeholders['default'];
        document.getElementById('templateMessage').placeholder = placeholderText;

        document.getElementById('templateModal').style.display = 'flex';
        
        fetch('<?= url('sms/editTemplate/') ?>' + id)
            .then(response => response.json())
            .then(data => {
                document.getElementById('templateMessage').value = data.message || '';
            })
            .catch(error => {
                console.error('Error fetching template:', error);
                document.getElementById('templateMessage').value = 'Error loading template.';
            });
    }

    function closeTemplateModal() {
        document.getElementById('templateModal').style.display = 'none';
    }
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
