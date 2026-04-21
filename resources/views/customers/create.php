<?php include __DIR__ . '/../partials/header.php'; ?>

<main class="dashboard-container">
    <div class="form-container">
        <div class="form-header">
            <h2>Create Customer</h2>
        </div>

        <form id="createCustomerForm">

            <?php foreach ($formSections as $section): ?>
                <div class="section-title"><?= htmlspecialchars($section['name']) ?></div>
                <div class="form-grid">
                    <?php foreach ($section['fields'] as $field):
                        $key = $field['field_key'];
                        $type = $field['type'];
                        $required = $field['required'] ? 'required' : '';
                        $placeholder = htmlspecialchars(($field['placeholder'] ?? '') ?: $field['label']);
                        $id = $key; // Use key as ID for JS compatibility
                        $options = $field['options'] ? json_decode($field['options'], true) : [];
                        ?>
                        <div class="form-group <?= ($type === 'textarea') ? 'full-width' : '' ?>">
                            <label><?= htmlspecialchars($field['label']) ?><?= $field['required'] ? ' *' : '' ?></label>

                            <?php if ($type === 'select'): ?>
                                <select name="<?= $key ?>" id="<?= $id ?>" 
                                    <?= $key === 'package_id' ? 'onchange="updatePrice()"' : '' ?> 
                                    <?= $key === 'extra_days' ? 'onchange="toggleExtraDayApply()"' : '' ?> 
                                    <?= $required ?>>
                                    <option value="">Select <?= htmlspecialchars($field['label']) ?></option>
                                    <?php if ($key === 'package_id'): ?>
                                        <?php foreach ($packages as $pkg): ?>
                                            <option value="<?= $pkg['id'] ?>" data-price="<?= $pkg['price'] ?>">
                                                <?= htmlspecialchars($pkg['name']) ?> (<?= $pkg['price'] ?> TK)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php elseif ($key === 'connected_by'): ?>
                                        <?php foreach ($employees as $emp): ?>
                                            <option value="<?= htmlspecialchars($emp['name']) ?>"><?= htmlspecialchars($emp['name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php elseif ($key === 'extra_days' || $key === 'auto_disable_month'): ?>
                                        <?php 
                                            $max = ($key === 'extra_days') ? 10 : 12;
                                            for ($i = 0; $i <= $max; $i++): 
                                                $lbl = ($i === 0) ? ($key === 'extra_days' ? 'None' : 'Current Month') : "$i " . ($key === 'extra_days' ? 'Days' : 'Month');
                                        ?>
                                            <option value="<?= $i ?>"><?= $lbl ?></option>
                                        <?php endfor; ?>
                                    <?php elseif (!empty($options)): ?>
                                        <?php foreach ($options as $val => $lbl): ?>
                                            <option value="<?= is_numeric($val) ? $val : $lbl ?>"><?= $lbl ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>

                                <?php if ($key === 'extra_days'): ?>
                                    <div class="form-group" id="extra_day_apply_group" style="display: none; margin-top: 10px;">
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
                                <?php endif; ?>

                            <?php elseif ($type === 'textarea'): ?>
                                <textarea name="<?= $key ?>" id="<?= $id ?>" rows="2" <?= $required ?>></textarea>

                            <?php elseif ($type === 'date'): ?>
                                <input type="text" name="<?= $key ?>" id="<?= $id ?>" class="date-picker" placeholder="DD/MM/YYYY"
                                    value="<?= ($key === 'connection_date') ? date('Y-m-d') : (($key === 'expire_date') ? date('Y-m-05', strtotime('first day of next month')) : '') ?>"
                                    <?= $required ?>>

                            <?php else: ?>
                                <input type="<?= $type ?>" name="<?= $key ?>" id="<?= $id ?>" 
                                    placeholder="<?= $placeholder ?>" 
                                    value="<?= ($key === 'payment_id') ? htmlspecialchars(($defaultPrefix ?? '') . ($nextId ?? '')) : (($key === 'security_deposit') ? '2000' : '') ?>"
                                    <?= ($key === 'monthly_rent' || $key === 'due_amount' || $key === 'additional_charge' || $key === 'discount' || $key === 'advance_amount' || $key === 'vat_percent') ? 'oninput="calculateTotal()"' : '' ?>
                                    <?= ($key === 'total_amount') ? 'readonly' : '' ?> <?= $required ?>>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

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