<?php include __DIR__ . '/../partials/header.php'; ?>

<main class="dashboard-container">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="margin:0;">Mikrotik Sync</h2>
        <button onclick="openModal()" style="padding: 8px 15px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor:pointer; font-weight: 500;">
            <i class="fas fa-plus"></i> Add New Mikrotik
        </button>
    </div>

    <div class="card">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>IP</th>
                        <th>Username</th>
                        <th>SSH Port</th>
                        <th>Total Customer</th>
                        <th>Static</th>
                        <th>PPPoE</th>
                        <th>Trace</th>
                        <th>Connection Status</th>
                        <th>Manual Connect</th>
                        <th>Auto Backup</th>
                        <th class="no-print">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mikrotiks)): ?>
                    <tr>
                        <td colspan="12" style="text-align:center; padding:20px;">No Mikrotik routers configured.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($mikrotiks as $m): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
                            <td><?= htmlspecialchars($m['ip_address']) ?></td>
                            <td><?= htmlspecialchars($m['username']) ?></td>
                            <td><?= htmlspecialchars($m['ssh_port']) ?></td>
                            <td><?= $m['total_customer'] ?></td>
                            <td><?= $m['static_customer'] ?></td>
                            <td><?= $m['pppoe_customer'] ?></td>
                            <td>-</td>
                            <td>
                                <!-- Connection Status Message -->
                                <?php if ($m['status'] === 'connected'): ?>
                                    <span style="color: #10b981; font-weight: 600; font-size: 0.9rem;"><i class="fas fa-check-circle"></i> Connected Properly</span>
                                <?php else: ?>
                                    <span style="color: #ef4444; font-weight: 600; font-size: 0.9rem;"><i class="fas fa-times-circle"></i> Not Connected</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Manual Connection Action -->
                                <form action="<?= url('mikrotik/toggleStatus/' . $m['id']) ?>" method="POST" style="margin:0; display:inline;">
                                    <?php if ($m['status'] === 'connected'): ?>
                                        <button type="submit" class="btn-table" style="background:#f59e0b; color: white;" title="Disconnect Manually"><i class="fas fa-power-off"></i> Disconnect</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn-table" style="background:#3b82f6; color: white;" title="Connect Manually"><i class="fas fa-plug"></i> Connect</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                            <td>
                                <!-- Auto Backup Toggle -->
                                <form action="<?= url('mikrotik/toggleBackup/' . $m['id']) ?>" method="POST" style="margin:0; display:inline;">
                                    <?php if ($m['auto_backup']): ?>
                                        <button type="submit" class="btn-table" style="background:#3b82f6; border-radius: 14px; padding: 4px 12px;">On</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn-table" style="background:#cbd5e1; color:#475569; border-radius: 14px; padding: 4px 12px;">Off</button>
                                    <?php endif; ?>
                                </form>
                                <a href="#" style="color: #3b82f6; text-decoration: none; font-size: 0.85rem; margin-left: 8px;">List</a>
                            </td>
                            <td class="no-print">
                                <button onclick='openModal(<?= json_encode($m) ?>)' class="btn-table" style="background:#10b981; margin-right:5px;">Edit</button>
                                <form action="<?= url('mikrotik/delete/' . $m['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this Mikrotik router?');">
                                    <button type="submit" class="btn-table" style="background:#ef4444;"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal for Create/Edit -->
<style>
    /* Retaining basic modal styling to work with standard app components */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(2px);
    }
    .modal.active {
        display: flex;
        animation: fadeIn 0.2s ease-out;
    }
    .modal-content {
        background: white;
        width: 100%;
        max-width: 500px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        padding: 24px;
        position: relative;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 10px;
    }
</style>

<div class="modal" id="mikrotikModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle" style="margin:0;">Add New Mikrotik</h3>
            <button onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; color:#64748b; cursor:pointer;">&times;</button>
        </div>
        
        <form id="mikrotikForm" action="<?= url('mikrotik/store') ?>" method="POST">
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Name *</label>
                <input type="text" name="name" id="mikrotikName" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" placeholder="e.g. Main Router">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Mikrotik Real IP *</label>
                <input type="text" name="ip_address" id="mikrotikIp" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" placeholder="e.g. 103.173.174.170">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Mikrotik SSH Port</label>
                <input type="number" name="ssh_port" id="mikrotikSsh" value="22" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Mikrotik Username *</label>
                <input type="text" name="username" id="mikrotikUsername" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Mikrotik Password</label>
                <input type="password" name="password" id="mikrotikPassword" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            
            <button type="submit" id="btnSubmit" style="padding: 10px 15px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor:pointer; width: 100%; font-weight: 500;">Save Mikrotik</button>
        </form>
    </div>
</div>

<script>
    function openModal(data = null) {
        const modal = document.getElementById('mikrotikModal');
        const form = document.getElementById('mikrotikForm');
        const title = document.getElementById('modalTitle');
        const btnSubmit = document.getElementById('btnSubmit');
        
        if (data) {
            title.textContent = 'Edit Mikrotik';
            form.action = '<?= url('mikrotik/update/') ?>' + data.id;
            
            document.getElementById('mikrotikName').value = data.name;
            document.getElementById('mikrotikIp').value = data.ip_address;
            document.getElementById('mikrotikSsh').value = data.ssh_port;
            document.getElementById('mikrotikUsername').value = data.username;
            document.getElementById('mikrotikPassword').value = data.password;
            
            btnSubmit.textContent = 'Update Mikrotik';
        } else {
            title.textContent = 'Add New Mikrotik';
            form.action = '<?= url('mikrotik/store') ?>';
            form.reset();
            document.getElementById('mikrotikSsh').value = '22';
            btnSubmit.textContent = 'Save Mikrotik';
        }
        
        modal.classList.add('active');
    }

    function closeModal() {
        const modal = document.getElementById('mikrotikModal');
        modal.classList.remove('active');
    }
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
