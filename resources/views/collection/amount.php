<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
    /* Main Layout */
    .amount-collection-wrapper {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 24px;
        margin-top: 20px;
    }

    /* Left Panel: Search */
    .search-panel {
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        border: 1px solid #e2e8f0;
        height: fit-content;
        position: sticky;
        top: 90px;
    }

    .search-panel h3 {
        font-size: 1.1rem;
        color: #1e293b;
        margin-bottom: 8px;
        font-weight: 700;
    }

    .search-panel p {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 16px;
    }

    #customer-search {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s;
        margin-bottom: 20px;
    }

    #customer-search:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    /* Search Results */
    #search-results {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 500px;
        overflow-y: auto;
    }

    .result-item {
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
        margin-bottom: 8px;
    }

    .result-item:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .result-item.active {
        background: #eff6ff;
        border-color: var(--accent);
    }

    .result-item .name {
        font-weight: 600;
        display: block;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .result-item .meta {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 2px;
        display: block;
    }

    /* Right Panel: Display & Form */
    .info-panel {
        background: white;
        padding: 32px;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        border: 1px solid #e2e8f0;
        min-height: 600px;
    }

    .customer-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    .customer-header h2 {
        font-size: 1.5rem;
        color: #1e293b;
        font-weight: 700;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-item label {
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .info-item span {
        font-size: 1rem;
        color: #334155;
        font-weight: 500;
    }

    .info-item.full-width {
        grid-column: span 2;
    }

    .billing-section {
        background: #f8fafc;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e2e8f0;
    }

    .billing-section h3 {
        font-size: 1.1rem;
        margin-bottom: 20px;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.9rem;
    }

    .btn-collect {
        background: #10b981;
        color: white;
        padding: 14px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        width: 100%;
        margin-top: 24px;
        transition: all 0.2s;
    }

    .btn-collect:hover {
        background: #059669;
        transform: translateY(-1px);
    }

    /* Utilities */
    .badge {
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge.active {
        background: #dcfce7;
        color: #166534;
    }

    .badge.pending {
        background: #fef9c3;
        color: #854d0e;
    }

    .badge.inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .placeholder-view {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #94a3b8;
        padding: 40px;
        text-align: center;
    }

    .placeholder-view i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .loader {
        border: 3px solid #f3f3f3;
        border-top: 3px solid var(--accent);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 40px auto;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<div class="dashboard-container">
    <div class="amount-collection-wrapper">
        <!-- Search Sidebar -->
        <div class="search-panel">
            <h3>Amount Collection</h3>
            <p>Search by Mobile, Username, Name or ID</p>
            <input type="text" id="customer-search" placeholder="Type to search..." autocomplete="off">

            <ul id="search-results">
                <!-- Data injected here -->
            </ul>
        </div>

        <!-- Details Content -->
        <div class="info-panel" id="info-panel">
            <div class="placeholder-view">
                <i class="fas fa-search-dollar"></i>
                <h2>Ready to Collect</h2>
                <p>Search and select a customer from the left to view billing details and process payment.</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('customer-search');
        const resultsList = document.getElementById('search-results');
        const infoPanel = document.getElementById('info-panel');
        let searchTimeout;

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const q = this.value.trim();

            if (q.length < 2) {
                resultsList.innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`<?= url('collection/search') ?>?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        resultsList.innerHTML = '';
                        if (data.length === 0) {
                            resultsList.innerHTML = '<li class="result-item">No records found.</li>';
                            return;
                        }
                        data.forEach(c => {
                            const li = document.createElement('li');
                            li.className = 'result-item';
                            li.innerHTML = `
                            <span class="name">${c.full_name}</span>
                            <span class="meta">ID: ${c.prefix_code || ''}${c.id} | ${c.mobile_no}</span>
                            ${c.pppoe_name ? `<span class="meta">${c.pppoe_name}</span>` : ''}
                        `;
                            li.onclick = () => selectCustomer(c.id, li);
                            resultsList.appendChild(li);
                        });
                    });
            }, 300);
        });

        function selectCustomer(id, element) {
            document.querySelectorAll('.result-item').forEach(i => i.classList.remove('active'));
            element.classList.add('active');

            infoPanel.innerHTML = '<div class="loader"></div>';

            // Use a slight delay to ensure UI transition feels smooth
            fetch(`<?= url('collection/getCustomerInfo') ?>/${id}`)
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        renderInfo(res.data, element);
                    } else {
                        infoPanel.innerHTML = `<div class="placeholder-view"><i class="fas fa-exclamation-circle"></i><p>${res.message}</p></div>`;
                    }
                })
                .catch(err => {
                    infoPanel.innerHTML = `<div class="placeholder-view"><i class="fas fa-wifi-slash"></i><p>Connection Error. Please check your network.</p></div>`;
                });
        }

        function renderInfo(c, element) {
            const lastPayment = c.last_payment ?
                `${c.last_payment.collection_date.split(" ")[0]} | ${c.last_payment.payment_method} | ${c.last_payment.amount} TK` :
                "No previous payment history found.";

            infoPanel.innerHTML = `
            <div class="customer-header">
                <h2>${c.full_name}</h2>
                <div style="display:flex; gap:10px; align-items:center;">
                    <span class="badge ${c.status}">${c.status.replace("_", " ")}</span>
                    ${c.auto_disable == 1 ? '<span class="badge warning" title="Auto-Disable Enabled"><i class="fas fa-clock"></i> Auto</span>' : ""}
                </div>
            </div>
            
            <div class="info-grid">
                <div class="info-item"><label>Customer ID</label><span>${c.prefix_code || ""}${c.id}</span></div>
                <div class="info-item"><label>Mobile Number</label><span>${c.mobile_no}</span></div>
                <div class="info-item"><label>PPPoE Username</label><span>${c.pppoe_name || "N/A"}</span></div>
                <div class="info-item"><label>Billing Type</label><span>${c.billing_type || "Prepaid"}</span></div>
                <div class="info-item full-width"><label>Address</label><span>${c.area || "N/A"}, House ${c.house_no || "N/A"}, ${c.district || "N/A"}</span></div>
                <div class="info-item"><label>Current Expiry</label><span id="current-expiry-display">${c.expire_date ? c.expire_date.split('-').reverse().join('/') : "N/A"}</span></div>
                <div class="info-item"><label>Monthly Rent</label><span>${c.monthly_rent} TK</span></div>
                <div class="info-item"><label>Total Payable</label><span>${c.total_amount} TK</span></div>
                <div class="info-item"><label>Extra Days</label><span>${c.extra_days > 0 ? c.extra_days + " Days (" + c.extra_days_type + ")" : "None"}</span></div>
                <div class="info-item"><label>Last Collection</label><span>${lastPayment}</span></div>
            </div>

            <div class="billing-section">
                <h3><i class="fas fa-file-invoice-dollar"></i> New Collection</h3>
                <form id="pay-form">
                    <input type="hidden" name="customer_id" value="${c.id}">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Amount to Collect (TK)</label>
                            <input type="number" name="amount" id="collect-amount" class="form-control" value="${c.total_amount}" required>
                        </div>
                        <div class="form-group">
                            <label>Invoice Number</label>
                            <input type="text" name="invoice_no" class="form-control" placeholder="Optional invoice #">
                        </div>
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="Cash">Cash</option>
                                <option value="bKash">bKash</option>
                                <option value="Rocket">Rocket</option>
                                <option value="Nagad">Nagad</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Next Expiry Date</label>
                            <input type="text" name="next_expire_date" id="next-expire-date" class="form-control date-picker" placeholder="DD/MM/YYYY" required>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Internal Note</label>
                            <input type="text" name="note" class="form-control" placeholder="Add optional details...">
                        </div>
                    </div>
                    <button type="submit" class="btn-collect">Submit Payment Collection</button>
                </form>
            </div>
        `;

            // Re-initialize Flatpickr for the new input
            flatpickr("#next-expire-date", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                allowInput: true,
                placeholder: "DD/MM/YYYY"
            });

            const amountInput = document.getElementById("collect-amount");
            const expiryInput = document.getElementById("next-expire-date");

            function updateNextExpire() {
                const paidAmount = parseFloat(amountInput.value) || 0;
                const monthlyRent = parseFloat(c.monthly_rent) || 0;
                const autoDisableMonth = parseInt(c.auto_disable_month) || 0;
                const extraDays = parseInt(c.extra_days) || 0;

                let baseDate = c.expire_date ? new Date(c.expire_date) : new Date();
                if (isNaN(baseDate.getTime())) baseDate = new Date();

                // Rule: If paid_amount < monthly_rent, date shouldn't change
                if (paidAmount < monthlyRent) {
                    const yyyy = baseDate.getFullYear();
                    const mm = String(baseDate.getMonth() + 1).padStart(2, '0');
                    const dd = String(baseDate.getDate()).padStart(2, '0');
                    if (expiryInput._flatpickr) {
                        expiryInput._flatpickr.setDate(`${yyyy}-${mm}-${dd}`);
                    } else {
                        expiryInput.value = `${dd}/${mm}/${yyyy}`;
                    }
                    return;
                }

                let monthsToAdd = Math.floor(paidAmount / monthlyRent);

                // Add calculation months
                baseDate.setMonth(baseDate.getMonth() + monthsToAdd);

                // Add Auto Temp Month if set (optional interpretation of "incorporate")
                if (autoDisableMonth > 0) {
                    baseDate.setMonth(baseDate.getMonth() + autoDisableMonth);
                }

                // Add Extra Days
                if (extraDays > 0) {
                    baseDate.setDate(baseDate.getDate() + extraDays);
                }

                // Format output as dd/mm/yyyy
                const yyyy = baseDate.getFullYear();
                const mm = String(baseDate.getMonth() + 1).padStart(2, '0');
                const dd = String(baseDate.getDate()).padStart(2, '0');

                // Set value for display (input type=text initially)
                expiryInput.value = `${dd}/${mm}/${yyyy}`;

                // Store ISO date for submission if needed, but input[name=next_expire_date] 
                // typically expects YYYY-MM-DD for backend. 
                // However, user asked for "formation will be: Days/month/year"
                // If the backend expects YYYY-MM-DD, we should keep a hidden input 
                // or ensure the controller parses dd/mm/yyyy.
                // Assuming standard PHP YYYY-MM-DD requirement, let's keep the internal val as YYYY-MM-DD
                // But the user specifically asked for "Expire Date field ,date formation wiil be : Days/month/year."
                // which usually implies the visual format.

                // To support <input type="text" placeholder="dd/mm/yyyy" onfocus="(this.type='date')" ...>
                // We should actually set the 'date' value (YYYY-MM-DD) if type is date 
                // OR set dd/mm/yyyy if type is text.

                // Let's set the input type to text for display initially
                // Trigger Flatpickr update
                if (expiryInput._flatpickr) {
                    expiryInput._flatpickr.setDate(`${yyyy}-${mm}-${dd}`);
                } else {
                    expiryInput.value = `${dd}/${mm}/${yyyy}`;
                }
            }

            updateNextExpire();
            amountInput.addEventListener("input", updateNextExpire);

            document.getElementById("pay-form").onsubmit = function (e) {
                e.preventDefault();
                const btn = this.querySelector("button");
                btn.disabled = true;
                btn.innerText = "Processing...";

                const formData = new FormData(this);

                // Date fields are now handled by Flatpickr altInput (sends Y-m-d)

                fetch("<?= url('collection/store') ?>", {
                    method: "POST",
                    body: formData
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === "success") {
                            alert("Collection recorded successfully.");
                            selectCustomer(c.id, element);
                        } else {
                            alert("Error: " + res.message);
                            btn.disabled = false;
                            btn.innerText = "Submit Payment Collection";
                        }
                    })
                    .catch(err => {
                        alert("An error occurred. Please try again.");
                        btn.disabled = false;
                        btn.innerText = "Submit Payment Collection";
                    });
            };
        }
    });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>