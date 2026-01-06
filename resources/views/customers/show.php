<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
    .profile-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        /* 2 Column Layout (Left wider) */
        gap: 20px;
        margin-top: 20px;
    }

    @media(max-width: 900px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }

    .card-box {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        border-top: 3px solid #3b82f6;
        overflow: hidden;
    }

    .card-header {
        background: #f8fafc;
        padding: 10px 20px;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 700;
        color: #334155;
        text-transform: uppercase;
        font-size: 13px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .form-table {
        width: 100%;
        border-collapse: collapse;
    }

    .form-table td {
        padding: 8px 15px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }

    .form-table tr:last-child td {
        border-bottom: none;
    }

    .label-cell {
        width: 35%;
        color: #64748b;
        font-weight: 500;
    }

    .input-cell {
        width: 65%;
    }

    .editable-input,
    .editable-select,
    .editable-textarea {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 14px;
        color: #1e293b;
        transition: border-color 0.2s;
    }

    .editable-input:focus {
        border-color: #3b82f6;
        outline: none;
    }

    .status-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    .status-card {
        background: white;
        padding: 15px;
        border-radius: 6px;
        text-align: center;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .status-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 5px;
    }

    .status-value {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }

    .btn-update {
        background: #2563eb;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        font-size: 16px;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
        width: 100%;
    }

    .btn-update:hover {
        background: #1d4ed8;
    }
</style>

<main class="dashboard-container">

    <!-- Top Status Bar (Read Only) -->
    <div class="status-bar">
        <div class="status-card">
            <div class="status-label">GRAPH</div>
            <div class="status-value" style="color:#22c55e;">Live</div>
        </div>
        <div class="status-card">
            <div class="status-label">MIKROTIK</div>
            <div class="status-value">Main</div>
        </div>
        <div class="status-card">
            <div class="status-label">PING</div>
            <div class="status-value"><?= rand(2, 15) ?>ms</div>
        </div>
        <div class="status-card">
            <div class="status-label">STATUS</div>
            <div class="status-value" style="color: green;">Online</div>
        </div>
    </div>

    <form id="profileForm">
        <!-- Hidden ID -->
        <input type="hidden" name="id" value="<?= $c['id'] ?>">

        <div class="profile-grid">

            <!-- LEFT COLUMN -->
            <div class="col-left">

                <!-- Personal Info -->
                <div class="card-box">
                    <div class="card-header">Personal Information</div>
                    <table class="form-table">
                        <tr>
                            <td class="label-cell">Name / Company</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="full_name"
                                    value="<?= $c['full_name'] ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Parents Name</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="parents_name"
                                    value="<?= $c['parents_name'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Spouse Name</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="spouse_name"
                                    value="<?= $c['spouse_name'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">ID</td>
                            <td class="input-cell"><input type="text" class="editable-input" value="<?= htmlspecialchars($c['prefix_code'] ?? '') ?><?= $c['id'] ?>"
                                    disabled style="background:#f1f5f9;"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Contact Person</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="contact_person"
                                    value="<?= $c['contact_person'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Email</td>
                            <td class="input-cell"><input type="email" class="editable-input" name="email"
                                    value="<?= $c['email'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">NID No</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="identification_no"
                                    value="<?= $c['identification_no'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Mobile</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="mobile_no"
                                    value="<?= $c['mobile_no'] ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Alt Mobile</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="alt_mobile_no"
                                    value="<?= $c['alt_mobile_no'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Entry Date</td>
                            <td class="input-cell"><input type="text" class="editable-input"
                                    value="<?= $c['created_at'] ?>" disabled style="background:#f1f5f9;"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Image</td>
                            <td class="input-cell"><input type="file" class="editable-input"></td>
                        </tr>
                    </table>
                </div>

                <!-- Address -->
                <div class="card-box">
                    <div class="card-header">Address</div>
                    <table class="form-table">
                        <tr>
                            <td class="label-cell">District</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="district"
                                    value="<?= $c['district'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Thana</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="thana"
                                    value="<?= $c['thana'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Area</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="area"
                                    value="<?= $c['area'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Building</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="building_name"
                                    value="<?= $c['building_name'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Floor</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="floor"
                                    value="<?= $c['floor'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">TJ Box</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="tj_box"
                                    value="<?= $c['tj_box'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">House No</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="house_no"
                                    value="<?= $c['house_no'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Fiber</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="fiber_code"
                                    value="<?= $c['fiber_code'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">ONU</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="onu_mac"
                                    value="<?= $c['onu_mac'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Latitude</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="latitude"
                                    value="<?= $c['latitude'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Longitude</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="longitude"
                                    value="<?= $c['longitude'] ?? '' ?>"></td>
                        </tr>
                    </table>
                </div>

            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-right">

                <!-- Billing -->
                <div class="card-box" style="border-top-color: #f59e0b;">
                    <div class="card-header">Billing</div>
                    <table class="form-table">
                        <tr>
                            <td class="label-cell">Rent</td>
                            <td class="input-cell"><input type="number" class="editable-input" name="monthly_rent"
                                    value="<?= $c['monthly_rent'] ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Payment ID</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="payment_id"
                                    value="<?= $c['payment_id'] ?? '' ?>" placeholder="Gateway ID"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Due</td>
                            <td class="input-cell"><input type="number" class="editable-input" name="due_amount"
                                    value="<?= $c['due_amount'] ?>" style="color:red; font-weight:bold;"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Add. Charge</td>
                            <td class="input-cell"><input type="number" class="editable-input" name="additional_charge"
                                    value="<?= $c['additional_charge'] ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Discount</td>
                            <td class="input-cell"><input type="number" class="editable-input" name="discount"
                                    value="<?= $c['discount'] ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Advance</td>
                            <td class="input-cell"><input type="number" class="editable-input" name="advance_amount"
                                    value="<?= $c['advance_amount'] ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">VAT</td>
                            <td class="input-cell"><input type="number" class="editable-input" name="vat_percent"
                                    value="<?= $c['vat_percent'] ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Type</td>
                            <td class="input-cell">
                                <select class="editable-select" name="billing_type">
                                    <option <?= $c['billing_type'] == 'Prepaid' ? 'selected' : '' ?>>Prepaid</option>
                                    <option <?= $c['billing_type'] == 'Postpaid' ? 'selected' : '' ?>>Postpaid</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Server Info -->
                <div class="card-box" style="border-top-color: #10b981;">
                    <div class="card-header">Server Information</div>
                    <table class="form-table">
                        <tr>
                            <td class="label-cell">Conn Date</td>
                            <td class="input-cell"><input type="date" class="editable-input" name="connection_date"
                                    value="<?= $c['connection_date'] ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Package</td>
                            <td class="input-cell">
                                <select class="editable-select" name="package_id">
                                    <option value="<?= $c['package_id'] ?>" selected><?= $c['package_name'] ?></option>
                                    <?php if (isset($packages)):
                                        foreach ($packages as $pkg): ?>
                                            <option value="<?= $pkg['id'] ?>"
                                                <?= ($c['package_id'] == $pkg['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($pkg['name']) ?>
                                            </option>
                                        <?php endforeach; endif; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-cell">Conn. Type</td>
                            <td class="input-cell">
                                <select class="editable-select" name="connection_type">
                                    <option value="PPPoE" <?= $c['connection_type'] == 'PPPoE' ? 'selected' : '' ?>>PPPoE
                                    </option>
                                    <option value="Static" <?= $c['connection_type'] == 'Static' ? 'selected' : '' ?>>
                                        Static</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-cell">PPPoE Name</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="pppoe_name"
                                    value="<?= $c['pppoe_name'] ?? $c['full_name'] ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Password</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="pppoe_password"
                                    value="<?= $c['pppoe_password'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Profile</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="pppoe_profile"
                                    value="<?= $c['pppoe_profile'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">IP</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="ip_address"
                                    value="<?= $c['ip_address'] ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Expire Date</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="expire_date"
                                    value="<?= $c['expire_date'] ? date('d/m/Y', strtotime($c['expire_date'])) : '' ?>" 
                                    placeholder="dd/mm/yyyy" onfocus="handleDateFocus(this)" onblur="handleDateBlur(this)"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Auto Temp Disable</td>
                            <td class="input-cell">
                                <select class="editable-select" name="auto_disable">
                                    <option value="0" <?= ($c['auto_disable'] ?? 0) == 0 ? 'selected' : '' ?>>Off</option>
                                    <option value="1" <?= ($c['auto_disable'] ?? 0) == 1 ? 'selected' : '' ?>>On</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-cell">Auto Temp Month</td>
                            <td class="input-cell">
                                <select class="editable-select" name="auto_disable_month">
                                    <option value="0" <?= ($c['auto_disable_month'] ?? 0) == 0 ? 'selected' : '' ?>>Current Month</option>
                                    <?php for($i=1; $i<=12; $i++): ?>
                                        <option value="<?= $i ?>" <?= ($c['auto_disable_month'] ?? 0) == $i ? 'selected' : '' ?>><?= $i ?> Month</option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-cell">Set Extra Day</td>
                            <td class="input-cell">
                                <div style="display: flex; gap: 5px;">
                                    <select class="editable-select" name="extra_days" id="extra_days_select" onchange="toggleExtraDayShow()">
                                        <option value="0">None</option>
                                        <?php for($i=1; $i<=10; $i++): ?>
                                            <option value="<?= $i ?>" <?= ($c['extra_days'] ?? 0) == $i ? 'selected' : '' ?>><?= $i ?> Days</option>
                                        <?php endfor; ?>
                                    </select>
                                    <button type="button" onclick="document.getElementById('extra_days_select').value=0; toggleExtraDayShow();" style="padding: 2px 5px; cursor: pointer; border: 1px solid #ddd; background: #f8fafc; border-radius: 4px;">Remove</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-cell">Extra Day Apply</td>
                            <td class="input-cell">
                                <div id="extra_day_apply_cell" style="display: <?= ($c['extra_days'] ?? 0) > 0 ? 'flex' : 'none' ?>; gap: 15px; align-items: center;">
                                    <label style="font-weight: normal; margin: 0; display: flex; align-items: center; gap: 5px;">
                                        <input type="radio" name="extra_days_type" value="One month" <?= ($c['extra_days_type'] ?? '') == 'One month' ? 'checked' : '' ?>> One month
                                    </label>
                                    <label style="font-weight: normal; margin: 0; display: flex; align-items: center; gap: 5px;">
                                        <input type="radio" name="extra_days_type" value="Every month" <?= ($c['extra_days_type'] ?? '') == 'Every month' ? 'checked' : '' ?>> Every month
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Official Info -->
                <div class="card-box" style="border-top-color: #8b5cf6;">
                    <div class="card-header">Official Information</div>
                    <table class="form-table">
                        <tr>
                            <td class="label-cell">Security Deposit</td>
                            <td class="input-cell"><input type="number" class="editable-input" name="security_deposit"
                                    value="<?= $c['security_deposit'] ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Client Type</td>
                            <td class="input-cell"><select class="editable-select" name="client_type">
                                    <option selected><?= $c['client_type'] ?></option>
                                    <option>Home</option>
                                    <option>Corporate</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Connectivity</td>
                            <td class="input-cell"><select class="editable-select" name="connectivity_type">
                                    <option selected><?= $c['connectivity_type'] ?></option>
                                    <option>Shared</option>
                                    <option>Dedicated</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Dist. Point</td>
                            <td class="input-cell"><input type="text" class="editable-input" name="distribution_point"
                                    value="<?= $c['distribution_point'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Connected By</td>
                            <td class="input-cell">
                                <select class="editable-select" name="connected_by">
                                    <option value="">Select Employee</option>
                                    <?php if (isset($employees)):
                                        foreach ($employees as $emp): ?>
                                            <option value="<?= htmlspecialchars($emp['name']) ?>" <?= ($c['connected_by'] ?? '') == $emp['name'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($emp['name']) ?>
                                            </option>
                                        <?php endforeach; endif; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-cell">Status</td>
                            <td class="input-cell">
                                <select class="editable-select" name="status">
                                    <option value="active" <?= ($c['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>
                                        Active</option>
                                    <option value="inactive" <?= ($c['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>
                                        Inactive</option>
                                    <option value="temp_disable" <?= ($c['status'] ?? '') == 'temp_disable' ? 'selected' : '' ?>>Temporary Disable</option>
                                    <option value="free" <?= ($c['status'] ?? '') == 'free' ? 'selected' : '' ?>>Free
                                        Customer</option>
                                    <option value="pending" <?= ($c['status'] ?? '') == 'pending' ? 'selected' : '' ?>>
                                        Pending</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-cell">Note</td>
                            <td class="input-cell"><textarea class="editable-textarea"
                                    name="note"><?= $c['note'] ?? '' ?></textarea></td>
                        </tr>
                    </table>
                </div>

                <button type="button" id="updateBtn" class="btn-update">UPDATE INFORMATION</button>

            </div>
        </div>
    </form>


</main>

<script>
    let previousExtraDays = 0;
    // Initialize previousExtraDays with initial value
    document.addEventListener("DOMContentLoaded", function() {
        const el = document.getElementById('extra_days_select');
        if(el) previousExtraDays = parseInt(el.value) || 0;
    });

    function handleDateFocus(input) {
        input.type = 'date';
        // Convert dd/mm/yyyy to yyyy-mm-dd
        if (input.value && input.value.includes('/')) {
            const parts = input.value.split('/');
            input.value = `${parts[2]}-${parts[1]}-${parts[0]}`;
        }
    }

    function handleDateBlur(input) {
        input.type = 'text';
        // Convert yyyy-mm-dd to dd/mm/yyyy
        if (input.value && input.value.includes('-')) {
            const parts = input.value.split('-');
            input.value = `${parts[2]}/${parts[1]}/${parts[0]}`;
        }
    }

    function toggleExtraDayShow() {
        const extraDaysSelect = document.getElementById('extra_days_select');
        const extraDays = parseInt(extraDaysSelect.value) || 0;
        const cell = document.getElementById('extra_day_apply_cell');
        cell.style.display = (extraDays > 0) ? 'flex' : 'none';

        // Update Expire Date
        const expireInput = document.querySelector('input[name="expire_date"]');
        let currentDateStr = expireInput.value;
        let dateObj = null;

        if (currentDateStr) {
            if (currentDateStr.includes('/')) {
                const parts = currentDateStr.split('/');
                dateObj = new Date(`${parts[2]}-${parts[1]}-${parts[0]}`);
            } else if (currentDateStr.includes('-')) {
                dateObj = new Date(currentDateStr);
            }
        }

        if (dateObj && !isNaN(dateObj.getTime())) {
            const diff = extraDays - previousExtraDays;
            
            if (diff !== 0) {
                dateObj.setDate(dateObj.getDate() + diff);
                
                const yyyy = dateObj.getFullYear();
                const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
                const dd = String(dateObj.getDate()).padStart(2, '0');
                
                if (expireInput.type === 'date') {
                    expireInput.value = `${yyyy}-${mm}-${dd}`;
                } else {
                    expireInput.value = `${dd}/${mm}/${yyyy}`;
                }
            }
        }
        
        previousExtraDays = extraDays;
    }

    document.getElementById('updateBtn').addEventListener('click', function () {
        const form = document.getElementById('profileForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const btn = this;

        // Convert date formats if needed (dd/mm/yyyy -> yyyy-mm-dd)
        ['expire_date', 'connection_date'].forEach(field => {
            if (data[field] && data[field].includes('/')) {
                const parts = data[field].split('/');
                if (parts.length === 3) {
                    data[field] = `${parts[2]}-${parts[1]}-${parts[0]}`;
                }
            }
        });

        btn.innerText = 'Updating...';
        btn.disabled = true;

        fetch('<?= url("customer/update/" . $c['id']) ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    alert('Information Updated Successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + res.message);
                    btn.innerText = 'UPDATE INFORMATION';
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Failed to update.');
                btn.innerText = 'UPDATE INFORMATION';
                btn.disabled = false;
            });
    });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>