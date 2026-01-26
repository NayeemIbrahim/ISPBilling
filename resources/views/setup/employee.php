<?php require_once __DIR__ . '/../partials/header.php'; ?>

<main class="dashboard-container">
    <div class="card">
        <div class="card-header"
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Employee Setup</h2>
            <div class="tab-buttons">
                <button class="btn-tab active" onclick="showTab('role')">Employee Role</button>
                <button class="btn-tab" onclick="showTab('new')">New Employee</button>
                <button class="btn-tab" onclick="showTab('list')">Employee List</button>
            </div>
        </div>

        <div class="card-body">

            <!-- SECTION 1: EMPLOYEE ROLE -->
            <div id="tab-role" class="tab-content">
                <div class="content-grid" style="grid-template-columns: 1fr; gap: 30px;">
                    <!-- Role Form -->
                    <div class="card" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        <div class="card-header"
                            style="background:#f8fafc; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="font-size:1.1rem; margin:0;" id="roleFormTitle">Create New Role</h3>
                            <button type="button" id="cancelRoleEdit"
                                style="display:none; background: #94a3b8; color: white; border: none; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Cancel
                                Edit</button>
                        </div>
                        <div class="card-body" style="padding:20px;">
                            <form action="<?= url('employee/storeRole') ?>" method="POST" id="roleForm">
                                <input type="hidden" name="id" id="roleId">
                                <div class="form-group" style="margin-bottom:15px;">
                                    <label style="display:block; margin-bottom:5px;">Role Name</label>
                                    <input type="text" name="role_name" id="roleName" required
                                        style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                                </div>
                                <div class="form-group" style="margin-bottom:15px;">
                                    <label style="display:block; margin-bottom:5px;">Description</label>
                                    <textarea name="description" id="roleDescription" rows="2"
                                        style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;"></textarea>
                                </div>
                                <div class="form-group" style="margin-bottom:15px;">
                                    <label style="display:block; margin-bottom:10px; font-weight:bold;">Accessible
                                        Menus</label>
                                    <div
                                        style="display:grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap:10px;">
                                        <?php foreach ($menus as $menu): ?>
                                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                                <input type="checkbox" name="permissions[]" value="<?= $menu ?>"
                                                    class="permission-checkbox"> <?= $menu ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div style="text-align:right;">
                                    <button type="submit" id="saveRoleBtn" class="btn-primary"
                                        style="padding:8px 20px;">Save Role</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Role List -->
                    <div class="card" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        <div class="card-header" style="background:#f8fafc; padding: 15px;">
                            <h3 style="font-size:1.1rem; margin:0;">Role List</h3>
                        </div>
                        <div class="card-body" style="padding:0;">
                            <table class="table" style="margin:0; width:100%;">
                                <thead style="background:#f1f5f9;">
                                    <tr>
                                        <th style="padding:12px 15px; text-align:left;">ID</th>
                                        <th style="padding:12px 15px; text-align:left;">Name</th>
                                        <th style="padding:12px 15px; text-align:left;">Description</th>
                                        <th style="padding:12px 15px; text-align:left;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roles as $role): ?>
                                        <tr style="border-bottom:1px solid #eee;">
                                            <td style="padding:10px 15px;">#<?= $role['id'] ?></td>
                                            <td style="padding:10px 15px; font-weight:500;"><?= $role['name'] ?></td>
                                            <td style="padding:10px 15px;"><?= $role['description'] ?></td>
                                            <td style="padding:10px 15px;">
                                                <button class="btn-sm edit-role-btn"
                                                    style="background:#3b82f6; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;"
                                                    data-id="<?= $role['id'] ?>"
                                                    data-name="<?= htmlspecialchars($role['name']) ?>"
                                                    data-description="<?= htmlspecialchars($role['description'] ?? '') ?>"
                                                    data-permissions='<?= $role['permissions'] ?? '[]' ?>'>
                                                    Edit
                                                </button>
                                                <a href="<?= url('employee/deleteRole/' . $role['id']) ?>"
                                                    onclick="return confirm('Are you sure you want to delete this role?')"
                                                    class="btn-sm"
                                                    style="background:#ef4444; color:white; border:none; padding:4px 10px; border-radius:4px; text-decoration:none; display:inline-block; font-size: 13px;">
                                                    Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: NEW EMPLOYEE -->
            <div id="tab-new" class="tab-content" style="display:none;">
                <div class="card" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto;">
                    <div class="card-header"
                        style="background:#f8fafc; padding: 15px; display:flex; justify-content:space-between; align-items:center;">
                        <h3 style="font-size:1.1rem; margin:0;" id="empFormTitle">Add New Employee</h3>
                        <button type="button" id="cancelEmpEdit"
                            style="display:none; background: #94a3b8; color: white; border: none; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Cancel
                            Edit</button>
                    </div>
                    <div class="card-body" style="padding:25px;">
                        <form action="<?= url('employee/storeEmployee') ?>" method="POST" id="empForm">
                            <input type="hidden" name="id" id="empId">
                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:15px;">
                                <div class="form-group">
                                    <label style="display:block; margin-bottom:5px;">Name</label>
                                    <input type="text" name="name" id="empName" required
                                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                </div>
                                <div class="form-group">
                                    <label style="display:block; margin-bottom:5px;">Email</label>
                                    <input type="email" name="email" id="empEmail" required
                                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                </div>
                            </div>

                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:15px;">
                                <div class="form-group">
                                    <label style="display:block; margin-bottom:5px;">Mobile</label>
                                    <input type="text" name="mobile" id="empMobile"
                                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                </div>
                                <div class="form-group">
                                    <label style="display:block; margin-bottom:5px;">Access Mikrotik</label>
                                    <select name="mikrotik_access" id="empMikrotik"
                                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; background:white;">
                                        <option value="">Select Mikrotik</option>
                                        <?php foreach ($mikrotiks as $m): ?>
                                            <option value="<?= $m ?>"><?= $m ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:15px;">
                                <div class="form-group">
                                    <label style="display:block; margin-bottom:5px;">Password <span id="pwdHint"
                                            style="font-weight:normal; font-size:0.8rem; color:#64748b; display:none;">(Leave
                                            blank to keep current)</span></label>
                                    <input type="password" name="password" id="empPassword" required
                                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                </div>
                                <div class="form-group">
                                    <label style="display:block; margin-bottom:5px;">Retype Password</label>
                                    <input type="password" name="retype_password" id="empRetypePassword" required
                                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom:20px;">
                                <label style="display:block; margin-bottom:5px;">Role</label>
                                <select name="role_id" id="empRole" required
                                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; background:white;">
                                    <option value="">Select Role</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div style="text-align:right;">
                                <button type="submit" id="saveEmpBtn" class="btn-primary"
                                    style="padding:10px 30px;">Create Employee</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: EMPLOYEE LIST -->
            <div id="tab-list" class="tab-content" style="display:none;">
                <div class="card" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div class="card-header" style="background:#f8fafc; padding: 15px;">
                        <h3 style="font-size:1.1rem; margin:0;">Employee List</h3>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <table class="table" style="margin:0; width:100%;">
                            <thead style="background:#f1f5f9;">
                                <tr>
                                    <th style="padding:12px 15px; text-align:left;">Name</th>
                                    <th style="padding:12px 15px; text-align:left;">Email</th>
                                    <th style="padding:12px 15px; text-align:left;">Mobile</th>
                                    <th style="padding:12px 15px; text-align:left;">Role</th>
                                    <th style="padding:12px 15px; text-align:left;">Mikrotik</th>
                                    <th style="padding:12px 15px; text-align:left;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employees as $emp): ?>
                                    <tr style="border-bottom:1px solid #eee;">
                                        <td style="padding:10px 15px; font-weight:500;"><?= $emp['name'] ?></td>
                                        <td style="padding:10px 15px;"><?= $emp['email'] ?></td>
                                        <td style="padding:10px 15px;"><?= $emp['mobile'] ?></td>
                                        <td style="padding:10px 15px;">
                                            <span
                                                style="background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:10px; font-size:0.8rem;">
                                                <?= $emp['role_name'] ?? 'No Role' ?>
                                            </span>
                                        </td>
                                        <td style="padding:10px 15px;"><?= $emp['mikrotik_access'] ?? '-' ?></td>
                                        <td style="padding:10px 15px;">
                                            <button class="btn-sm edit-emp-btn"
                                                style="background:#3b82f6; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;"
                                                data-id="<?= $emp['id'] ?>"
                                                data-name="<?= htmlspecialchars($emp['name']) ?>"
                                                data-email="<?= htmlspecialchars($emp['email']) ?>"
                                                data-mobile="<?= htmlspecialchars($emp['mobile']) ?>"
                                                data-role="<?= $emp['role_id'] ?>"
                                                data-mikrotik="<?= htmlspecialchars($emp['mikrotik_access'] ?? '') ?>">
                                                Edit
                                            </button>
                                            <a href="<?= url('employee/deleteEmployee/' . $emp['id']) ?>"
                                                onclick="return confirm('Are you sure you want to delete this employee?')"
                                                class="btn-sm"
                                                style="background:#ef4444; color:white; border:none; padding:4px 10px; border-radius:4px; text-decoration:none; display:inline-block; font-size: 13px;">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<style>
    .btn-tab {
        background: white;
        border: 1px solid #ddd;
        padding: 8px 16px;
        cursor: pointer;
        font-weight: 500;
        color: #64748b;
        transition: all 0.2s;
    }

    .btn-tab:first-child {
        border-radius: 4px 0 0 4px;
    }

    .btn-tab:last-child {
        border-radius: 0 4px 4px 0;
        border-left: none;
    }

    .btn-tab:not(:first-child):not(:last-child) {
        border-left: none;
    }

    .btn-tab:hover {
        background: #f8fafc;
        color: #1e293b;
    }

    .btn-tab.active {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }
</style>

<script>
    function showTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        // Show selected tab
        document.getElementById('tab-' + tabName).style.display = 'block';

        // Update buttons
        document.querySelectorAll('.btn-tab').forEach(btn => btn.classList.remove('active'));
        // Find the button that called this function is hard without 'this', so we rely on index or text. 
        // Better: select by matching text content or index. 
        // For simplicity:
        const buttons = document.querySelectorAll('.btn-tab');
        if (tabName === 'role') buttons[0].classList.add('active');
        if (tabName === 'new') buttons[1].classList.add('active');
        if (tabName === 'list') buttons[2].classList.add('active');
    }

    // Role Edit Functionality
    document.querySelectorAll('.edit-role-btn').forEach(button => {
        button.addEventListener('click', function () {
            // Populate Form
            document.getElementById('roleId').value = this.dataset.id;
            document.getElementById('roleName').value = this.dataset.name;
            document.getElementById('roleDescription').value = this.dataset.description;

            // Handle Permissions (Checkbox)
            let perms = [];
            try {
                perms = JSON.parse(this.dataset.permissions);
            } catch (e) { }

            document.querySelectorAll('.permission-checkbox').forEach(cb => {
                cb.checked = perms.includes(cb.value);
            });

            // Change UI State
            document.getElementById('roleFormTitle').textContent = 'Edit Role';
            document.getElementById('saveRoleBtn').textContent = 'Update Role';
            document.getElementById('cancelRoleEdit').style.display = 'block';

            // Scroll to form
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // Cancel Role Edit
    document.getElementById('cancelRoleEdit').addEventListener('click', function () {
        // Reset Form
        document.getElementById('roleForm').reset();
        document.getElementById('roleId').value = '';

        // Reset UI State
        document.getElementById('roleFormTitle').textContent = 'Create New Role';
        document.getElementById('saveRoleBtn').textContent = 'Save Role';
        this.style.display = 'none';

        // Uncheck all boxes
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
    });

    // Employee Edit Functionality
    document.querySelectorAll('.edit-emp-btn').forEach(button => {
        button.addEventListener('click', function () {
            // Populate Form
            document.getElementById('empId').value = this.dataset.id;
            document.getElementById('empName').value = this.dataset.name;
            document.getElementById('empEmail').value = this.dataset.email;
            document.getElementById('empMobile').value = this.dataset.mobile;
            document.getElementById('empRole').value = this.dataset.role;
            document.getElementById('empMikrotik').value = this.dataset.mikrotik;

            // Password fields not populated for security, but hint shown
            document.getElementById('empPassword').required = false;
            document.getElementById('empRetypePassword').required = false;
            document.getElementById('pwdHint').style.display = 'inline';

            // Switch to New Employee Tab
            showTab('new');

            // Change UI State
            document.getElementById('empFormTitle').textContent = 'Edit Employee';
            document.getElementById('saveEmpBtn').textContent = 'Update Employee';
            document.getElementById('cancelEmpEdit').style.display = 'block';

            // Scroll to form
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // Cancel Employee Edit
    document.getElementById('cancelEmpEdit').addEventListener('click', function () {
        // Reset Form
        document.getElementById('empForm').reset();
        document.getElementById('empId').value = '';

        // Reset Password Requirement
        document.getElementById('empPassword').required = true;
        document.getElementById('empRetypePassword').required = true;
        document.getElementById('pwdHint').style.display = 'none';

        // Reset UI State
        document.getElementById('empFormTitle').textContent = 'Add New Employee';
        document.getElementById('saveEmpBtn').textContent = 'Create Employee';
        this.style.display = 'none';
    });
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>