<?php include __DIR__ . '/../partials/header.php'; ?>

<main class="dashboard-container">
    <div class="form-container">
        <div class="form-header">
            <h2>Create Customer</h2>
        </div>

        <form id="createCustomerForm">

            <!-- 1. Personal Information -->
            <div class="section-title">Personal Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Customer Name *</label>
                    <input type="text" name="full_name" placeholder="Mr. John Doe" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="john@example.com">
                </div>
                <div class="form-group">
                    <label>Identification No</label>
                    <input type="text" name="identification_no" placeholder="NID/Birth Certificate">
                </div>
                <div class="form-group">
                    <label>Mobile No *</label>
                    <input type="text" name="mobile_no" placeholder="8801xxxxxxxxx" required>
                </div>
                <div class="form-group">
                    <label>Alt Mobile No</label>
                    <input type="text" name="alt_mobile_no" placeholder="018xxxxxxxx">
                </div>
                <div class="form-group">
                    <label>Professional Detail</label>
                    <input type="text" name="professional_detail" placeholder="Software Engineer">
                </div>
            </div>

            <!-- 2. Address -->
            <div class="section-title">Address</div>
            <div class="form-grid">
                <div class="form-group"><label>District</label><input type="text" name="district"></div>
                <div class="form-group"><label>Thana</label><input type="text" name="thana"></div>
                <div class="form-group"><label>Area</label><input type="text" name="area"></div>
                <div class="form-group"><label>Building Name</label><input type="text" name="building_name"></div>
                <div class="form-group"><label>Floor</label><input type="text" name="floor"></div>
                <div class="form-group"><label>TJ Box</label><input type="text" name="tj_box"></div>
                <div class="form-group"><label>House Info / No</label><input type="text" name="house_no"></div>
                <div class="form-group"><label>Latitude</label><input type="text" name="latitude"></div>
                <div class="form-group"><label>Longitude</label><input type="text" name="longitude"></div>
            </div>

            <!-- 3. Technical Info -->
            <div class="section-title">Technical Information</div>
            <div class="form-grid">
                <div class="form-group"><label>Fiber Code</label><input type="text" name="fiber_code"></div>
                <div class="form-group"><label>ONU Info</label><input type="text" name="onu_mac"></div>
                <div class="form-group"><label>Group</label><input type="text" name="group_name"></div>
                <div class="form-group"><label>Lazar Info</label><input type="text" name="lazar_info"></div>
                <div class="form-group"><label>Server Info</label><input type="text" name="server_info"></div>
                <div class="form-group">
                    <label>Connection Type</label>
                    <select name="connection_type">
                        <option value="PPPoE">PPPoE</option>
                        <option value="Static">Static</option>
                    </select>
                </div>
                <div class="form-group"><label>Connection Date</label><input type="text" name="connection_date"
                        class="date-picker" placeholder="DD/MM/YYYY" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group"><label>Expire Date</label><input type="text" name="expire_date"
                        class="date-picker" placeholder="DD/MM/YYYY"
                        value="<?= date('Y-m-05', strtotime('first day of next month')) ?>">
                </div>
                <div class="form-group">
                    <label>Auto Temporary Disable</label>
                    <select name="auto_disable">
                        <option value="0">Off</option>
                        <option value="1">On</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Auto Temporary Month</label>
                    <select name="auto_disable_month">
                        <option value="0">Current Month</option>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?> Month</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Set Extra Day</label>
                    <select name="extra_days" id="extra_days_create" onchange="toggleExtraDayApply()">
                        <option value="0">None</option>
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?> Days</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group" id="extra_day_apply_group" style="display: none;">
                    <label>Extra Day Apply</label>
                    <div style="display: flex; gap: 15px; align-items: center; height: 38px;">
                        <label style="font-weight: normal; margin: 0; display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="extra_days_type" value="One month" checked> One month
                        </label>
                        <label style="font-weight: normal; margin: 0; display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="extra_days_type" value="Every month"> Every month
                        </label>
                    </div>
                </div>
            </div>

            <!-- 4. Mikrotik Configuration -->
            <div class="section-title">Mikrotik Configuration</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Mikrotik Router</label>
                    <select name="mikrotik_id">
                        <option value="1">Main Router</option>
                    </select>
                </div>
                <div class="form-group"><label>PPPoE Name</label><input type="text" name="pppoe_name"></div>
                <div class="form-group"><label>Password</label><input type="text" name="pppoe_password"></div>
                <div class="form-group"><label>Profile</label><input type="text" name="pppoe_profile"></div>
                <div class="form-group"><label>IP Address</label><input type="text" name="ip_address"
                        placeholder="1.1.1.1"></div>
                <div class="form-group"><label>MAC Address</label><input type="text" name="mac_address"
                        placeholder="00:1e:ec:..."></div>
                <div class="form-group"><label>Bandwidth</label><input type="text" name="bandwidth" placeholder="2M/4M">
                </div>
                <div class="form-group full-width"><label>Comment</label><input type="text" name="comment"></div>
            </div>

            <!-- 5. Package & Billing -->
            <div class="section-title">Package & Billing</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Package</label>
                    <select name="package_id" id="packageSelect" onchange="updatePrice()">
                        <option value="">Select Package</option>
                        <?php if (isset($packages)):
                            foreach ($packages as $pkg): ?>
                                <option value="<?= $pkg['id'] ?>" data-price="<?= $pkg['price'] ?>">
                                    <?= htmlspecialchars($pkg['name']) ?> (<?= $pkg['price'] ?> TK)
                                </option>
                            <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="form-group"><label>Monthly Rent</label><input type="number" id="monthly_rent"
                        name="monthly_rent" value="0" oninput="calculateTotal()"></div>
                <div class="form-group"><label>Payment ID (Customer ID)</label>
                    <input type="text" name="payment_id"
                        value="<?= htmlspecialchars(($defaultPrefix ?? '') . ($nextId ?? '')) ?>"
                        placeholder="Payment Gateway ID">
                </div>
                <div class="form-group"><label>Due</label><input type="number" id="due_amount" name="due_amount"
                        value="0" oninput="calculateTotal()"></div>
                <div class="form-group"><label>Additional Charge</label><input type="number" id="additional_charge"
                        name="additional_charge" value="0" oninput="calculateTotal()"></div>
                <div class="form-group"><label>Discount</label><input type="number" id="discount" name="discount"
                        value="0" oninput="calculateTotal()"></div>
                <div class="form-group"><label>Advance</label><input type="number" id="advance_amount"
                        name="advance_amount" value="0" oninput="calculateTotal()">
                </div>
                <div class="form-group"><label>Vat ( % )</label><input type="number" id="vat_percent" name="vat_percent"
                        value="0" oninput="calculateTotal()"></div>
                <div class="form-group"><label>Total</label><input type="number" name="total_amount" id="total_amount"
                        readonly value="0"></div>
            </div>

            <!-- 6. Official Information -->
            <div class="section-title">Official Information</div>
            <div class="form-grid">
                <div class="form-group"><label>Billing Type</label>
                    <select name="billing_type">
                        <option>Pre Paid</option>
                        <option>Post Paid</option>
                    </select>
                </div>
                <div class="form-group"><label>Type of Connectivity</label>
                    <select name="connectivity_type">
                        <option>Shared</option>
                        <option>Dedicated</option>
                    </select>
                </div>
                <div class="form-group"><label>Type of Connection</label>
                    <select name="connection_type">
                        <option>Fiber</option>
                        <option>Cat5</option>
                    </select>
                </div>
                <div class="form-group"><label>Type of Client</label>
                    <select name="client_type">
                        <option>Home</option>
                        <option>Corporate</option>
                    </select>
                </div>
                <div class="form-group"><label>Dist. Location Point</label><input type="text" name="distribution_point"
                        placeholder="DC"></div>
                <div class="form-group"><label>Connected By</label>
                    <select name="connected_by">
                        <option value="">Select Employee</option>
                        <?php if (isset($employees)):
                            foreach ($employees as $emp): ?>
                                <option value="<?= htmlspecialchars($emp['name']) ?>"><?= htmlspecialchars($emp['name']) ?>
                                </option>
                            <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="form-group"><label>Reference Name</label><input type="text" name="reference_name"
                        placeholder="Reference person name"></div>
                <div class="form-group"><label>Security Deposit</label><input type="number" name="security_deposit"
                        value="2000"></div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="pending" selected>Pending</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="temp_disable">Temporary Disable</option>
                        <option value="free">Free Customer</option>
                    </select>
                </div>
                <div class="form-group full-width"><label>Description</label><textarea name="description"
                        rows="2"></textarea></div>
                <div class="form-group full-width"><label>Note</label><textarea name="note" rows="2"></textarea></div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="window.history.back()">Cancel</button>
                <button type="submit" class="btn-save">Save Customer</button>
            </div>
        </form>
    </div>
</main>

<script>
    // JS for form submission (pointing to PHP Controller)
    document.getElementById('createCustomerForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            // Date fields are now handled by Flatpickr altInput (sends Y-m-d)

            // Note: URL structure /customer/store
            const response = await fetch('<?= url("customer/store") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.status === 'success') {
                alert('Saved Successfully!');
                window.location.href = '<?= url("customer") ?>'; // redirect
            } else {
                alert('Error: ' + result.message);
            }
        } catch (err) {
            console.error(err);
            alert('Save failed');
        }
    });
</script>

<script>
    let previousExtraDays = 0;

    function toggleExtraDayApply() {
        const extraDaysSelect = document.getElementById('extra_days_create');
        const extraDays = parseInt(extraDaysSelect.value) || 0;
        const group = document.getElementById('extra_day_apply_group');
        group.style.display = (extraDays > 0) ? 'block' : 'none';

        // Update Expire Date
        const expireInput = document.querySelector('input[name="expire_date"]');
        let currentDateStr = expireInput.value;
        let dateObj = null;

        // Parse current date
        if (currentDateStr) {
            // Check format
            if (currentDateStr.includes('/')) {
                const parts = currentDateStr.split('/');
                // dd/mm/yyyy
                dateObj = new Date(`${parts[2]}-${parts[1]}-${parts[0]}`);
            } else if (currentDateStr.includes('-')) {
                // yyyy-mm-dd
                dateObj = new Date(currentDateStr);
            }
        }

        if (dateObj && !isNaN(dateObj.getTime())) {
            // We need to add the difference between new extra days and previous extra days
            // to avoid continuously adding days if user toggles around.
            // OR: simpler approach -> assume the user set a base date, and we are just adding the *difference*
            // But tracking base date is hard if user edits the date field manually.

            // Better approach: 
            // 1. We determine the "change" in extra days.
            const diff = extraDays - previousExtraDays;

            if (diff !== 0) {
                dateObj.setDate(dateObj.getDate() + diff);

                // Format back
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

    function updatePrice() {
        const select = document.getElementById('packageSelect');
        const price = select.options[select.selectedIndex].getAttribute('data-price');
        if (price) {
            document.getElementById('monthly_rent').value = price;
            calculateTotal(); // Trigger calculation
        }
    }

    function calculateTotal() {
        const rent = parseFloat(document.getElementById('monthly_rent').value) || 0;
        const add = parseFloat(document.getElementById('additional_charge').value) || 0;
        const due = parseFloat(document.getElementById('due_amount').value) || 0;
        const vatP = parseFloat(document.getElementById('vat_percent').value) || 0;
        const disc = parseFloat(document.getElementById('discount').value) || 0;
        const adv = parseFloat(document.getElementById('advance_amount').value) || 0;

        // Logic: (Rent + Add) * (1 + VAT/100) + Due - Discount - Advance
        // Or as user said: sum of rent, add, due, vat percentage, then subtract discount and advance.
        
        const vatAmount = (rent + add) * (vatP / 100);
        const total = (rent + add + due + vatAmount) - (disc + adv);

        document.getElementById('total_amount').value = Math.round(total);
    }
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>