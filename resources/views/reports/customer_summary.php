<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="dashboard-container">
    <div class="card-box">
        <div class="card-header">
            <span><i class="fas fa-chart-pie"></i> Customer Summary</span>
            <button onclick="window.print()" class="btn-collect" style="padding: 5px 15px; font-size: 14px;">Print
                Summary</button>
        </div>


        <div class="search-section"
            style="margin-top: 30px; padding: 20px; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="max-width: 600px; margin: 0 auto; text-align: center;">
                <h3 style="margin-top: 0; color: #1e293b; margin-bottom: 15px;">Search Customer Bill History</h3>
                <div style="position: relative;">
                    <input type="text" id="customerSearch" placeholder="Search by Name, ID or Mobile..."
                        style="width: 100%; padding: 12px 20px; padding-left: 45px; border: 2px solid #e2e8f0; border-radius: 50px; font-size: 16px; outline: none; transition: all 0.2s;">
                    <i class="fas fa-search"
                        style="position: absolute; left: 18px; top: 16px; color: #94a3b8; font-size: 18px;"></i>
                    <div id="searchResults"
                        style="display: none; position: absolute; top: 110%; left: 0; right: 0; background: white; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 50; max-height: 300px; overflow-y: auto; text-align: left;">
                    </div>
                </div>
            </div>
        </div>

        <div id="historySection" style="display: none; margin-top: 30px;">
            <div id="printableArea"
                style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0;">

                <div class="customer-header"
                    style="display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px;">
                    <div>
                        <h2 id="custName" style="margin: 0; color: #0f172a; font-size: 24px;"></h2>
                        <div style="margin-top: 8px; color: #64748b; font-size: 14px;">
                            ID: <span id="custId" style="color: #334155; font-weight: 600;"></span> |
                            Mobile: <span id="custMobile" style="color: #334155; font-weight: 600;"></span>
                        </div>
                        <div id="custAddress" style="margin-top: 4px; color: #64748b; font-size: 13px;"></div>
                    </div>
                    <div style="text-align: right;">
                        <button onclick="printHistory()" class="no-print"
                            style="background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">
                            <i class="fas fa-print"></i> Print History
                        </button>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 12px; text-align: left; color: #475569;">Date</th>
                                <th style="padding: 12px; text-align: left; color: #475569;">Description</th>
                                <th style="padding: 12px; text-align: right; color: #475569;">Bill Amount</th>
                                <th style="padding: 12px; text-align: right; color: #64748b;">Additional</th>
                                <th style="padding: 12px; text-align: right; color: #64748b;">Discount</th>
                                <th style="padding: 12px; text-align: right; color: #ef4444;">Due</th>
                                <th style="padding: 12px; text-align: right; color: #059669;">Advance</th>
                                <th style="padding: 12px; text-align: right; color: #059669; background: #f0fdf4;">Paid
                                    Amount</th>
                                <th style="padding: 12px; text-align: left; color: #475569;">Collected By</th>
                                <th style="padding: 12px; text-align: left; color: #475569;">Note</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div
            style="margin-top: 40px; padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
            <h3>Quick Links</h3>
            <ul style="list-style: none; padding: 0; display: flex; gap: 20px; margin-top: 15px;">
                <li><a href="<?= url('report/dueList') ?>" style="color: #3b82f6; text-decoration: none;"><i
                            class="fas fa-exclamation-triangle"></i> View Due List</a></li>
                <li><a href="<?= url('report/inactiveList') ?>" style="color: #3b82f6; text-decoration: none;"><i
                            class="fas fa-user-slash"></i> View Inactive List</a></li>
                <li><a href="<?= url('report/collectionReport') ?>" style="color: #3b82f6; text-decoration: none;"><i
                            class="fas fa-file-invoice-dollar"></i> Detailed Collections</a></li>
            </ul>
        </div>
    </div>
</div>

<script>
    let searchTimeout;
    const searchInput = document.getElementById('customerSearch');
    const resultsBox = document.getElementById('searchResults');

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 1) {
            resultsBox.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`<?= url('collection/search') ?>?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsBox.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(cust => {
                            const div = document.createElement('div');
                            div.style.padding = '12px 15px';
                            div.style.cursor = 'pointer';
                            div.style.borderBottom = '1px solid #f1f5f9';
                            div.style.transition = 'background 0.2s';
                            div.onmouseover = () => div.style.background = '#f8fafc';
                            div.onmouseout = () => div.style.background = 'white';
                            div.innerHTML = `
                            <div style="font-weight: 600; color: #0f172a;">${cust.full_name}</div>
                            <div style="font-size: 12px; color: #64748b;">
                                ID: ${cust.prefix_code || ''}${cust.id} | ${cust.mobile_no}
                            </div>
                        `;
                            div.onclick = () => loadHistory(cust.id);
                            resultsBox.appendChild(div);
                        });
                        resultsBox.style.display = 'block';
                    } else {
                        resultsBox.style.display = 'none';
                    }
                });
        }, 300);
    });

    // Hide results when clicking outside
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.style.display = 'none';
        }
    });

    function loadHistory(customerId) {
        resultsBox.style.display = 'none';
        searchInput.value = ''; // Clear search

        fetch(`<?= url('report/customerHistory') ?>?customer_id=${customerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const c = data.customer;

                    // Populate Header
                    document.getElementById('custName').textContent = c.full_name;
                    document.getElementById('custId').textContent = (c.prefix_code || '') + c.id;
                    document.getElementById('custMobile').textContent = c.mobile_no;

                    let address = [c.building_name, c.house_no, c.road_no, c.area, c.thana, c.district].filter(Boolean).join(', ');
                    document.getElementById('custAddress').textContent = address;

                    // Populate Table
                    const tbody = document.getElementById('historyTableBody');
                    tbody.innerHTML = '';

                    // --- LEDGER LOGIC ---
                    const events = [];

                    // 1. Generate Bill Events (1st of every month since connection)
                    // Use connection_date or fallback to created_at, or today if missing
                    let startDateStr = c.connection_date || c.created_at;
                    let startDate = startDateStr ? new Date(startDateStr) : new Date();

                    // Normalize to 1st of the month? User said "monthly rent is add on the first date"
                    // If connection is Jan 15, does Bill start Jan 1 or Feb 1?
                    // Usually billing implies paying for usage. Let's assume start from the connection Month's 1st date.
                    startDate.setDate(1);
                    startDate.setHours(0, 0, 0, 0);

                    const today = new Date();
                    today.setHours(23, 59, 59, 999); // Include today

                    // Helper to format date YYYY-MM-DD
                    const toYMD = (date) => {
                        const y = date.getFullYear();
                        const m = String(date.getMonth() + 1).padStart(2, '0');
                        const d = String(date.getDate()).padStart(2, '0');
                        return `${y}-${m}-${d}`;
                    };

                    // Loop months
                    let iterDate = new Date(startDate);
                    while (iterDate <= today) {
                        // Calculate Bill Amount (Rent + Additional - Discount)
                        // Note: Using CURRENT settings for historical bills as we don't track history
                        const rent = parseFloat(c.monthly_rent || 0);
                        const additional = parseFloat(c.additional_charge || 0);
                        const discount = parseFloat(c.discount || 0);
                        const billAmount = rent + additional - discount;

                        events.push({
                            type: 'bill',
                            date: new Date(iterDate), // Clone
                            amount: billAmount,
                            additional: additional,
                            discount: discount,
                            description: `Monthly Bill - ${iterDate.toLocaleString('default', { month: 'short', year: 'numeric' })}`,
                            note: '-'
                        });

                        // Next month
                        iterDate.setMonth(iterDate.getMonth() + 1);
                    }

                    // 2. Add Collection Events
                    data.collections.forEach(col => {
                        events.push({
                            type: 'collection',
                            date: new Date(col.collection_date),
                            amount: parseFloat(col.amount || 0),
                            description: 'Payment Received',
                            note: col.note || '',
                            collected_by: col.collected_by_name || '-'
                        });
                    });

                    // 3. Sort Events by Date ASC
                    events.sort((a, b) => a.date - b.date);

                    // 4. Calculate Running Balance & Render
                    let totalDue = 0; // Represents required payment
                    // Actually, let's track "Balance". Positive = Due (Owe), Negative = Advance (Overpaid)
                    let balance = 0;
                    let totalPaid = 0;

                    if (events.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding: 20px; color: #64748b;">No history found.</td></tr>';
                    } else {
                        events.forEach(e => {
                            let debit = 0;  // Bill
                            let credit = 0; // Payment

                            let rowClass = ''; // Style

                            if (e.type === 'bill') {
                                debit = e.amount;
                                balance += debit;
                                rowClass = 'background: #fff;';
                            } else {
                                credit = e.amount;
                                displayCredit = credit;
                                balance -= credit;
                                totalPaid += credit;
                                rowClass = 'background: #f0fdf4;';
                            }

                            // Determine Due / Advance for this row
                            let showDue = 0;
                            let showAdvance = 0;

                            if (balance > 0) {
                                showDue = balance;
                            } else if (balance < 0) {
                                showAdvance = Math.abs(balance);
                            }

                            // Date Format
                            const dd = String(e.date.getDate()).padStart(2, '0');
                            const mm = String(e.date.getMonth() + 1).padStart(2, '0');
                            const yyyy = e.date.getFullYear();
                            const dateStr = `${dd}/${mm}/${yyyy}`;

                            const row = `
                                <tr style="border-bottom: 1px solid #f1f5f9; ${rowClass}">
                                    <td style="padding: 12px; color: #334155;">${dateStr}</td>
                                    <td style="padding: 12px; font-weight: 500;">${e.description}</td>
                                    <td style="padding: 12px; text-align: right;">${e.type === 'bill' ? debit.toFixed(2) : '-'}</td>
                                    <td style="padding: 12px; text-align: right; color: #64748b;">${e.type === 'bill' ? (e.additional || 0).toFixed(2) : '-'}</td>
                                    <td style="padding: 12px; text-align: right; color: #64748b;">${e.type === 'bill' ? (e.discount || 0).toFixed(2) : '-'}</td>
                                    <td style="padding: 12px; text-align: right; color: #ef4444;">
                                        ${showDue > 0 ? showDue.toFixed(2) : '-'}
                                    </td>
                                    <td style="padding: 12px; text-align: right; color: #059669;">
                                        ${showAdvance > 0 ? showAdvance.toFixed(2) : '-'}
                                    </td>
                                    <td style="padding: 12px; text-align: right; color: #059669; font-weight: 600;">
                                        ${e.type === 'collection' ? credit.toFixed(2) : '-'}
                                    </td>
                                    <td style="padding: 12px; text-align: center; font-size: 12px; color: #64748b;">
                                        ${e.collected_by || '-'}
                                    </td>
                                    <td style="padding: 12px; text-align: left; font-size: 12px; color: #64748b;">
                                        ${e.note && e.note !== '-' ? e.note : ''}
                                    </td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });

                        // Footer Row
                        const footerRow = `
                            <tr style="background: #f8fafc; font-weight: 700; border-top: 2px solid #e2e8f0; border-bottom: 2px solid #e2e8f0;">
                                <td colspan="7" style="padding: 12px; text-align: right; color: #475569;">Total Paid:</td>
                                <td style="padding: 12px; text-align: right; color: #059669;">${totalPaid.toFixed(2)}</td>
                                <td colspan="2"></td>
                            </tr>
                        `;
                        tbody.innerHTML += footerRow;
                    }

                    document.getElementById('historySection').style.display = 'block';
                    document.getElementById('historySection').scrollIntoView({ behavior: 'smooth' });
                }
            });
    }

    function printHistory() {
        const printContent = document.getElementById('printableArea').innerHTML;
        const originalContent = document.body.innerHTML;

        document.body.innerHTML = `
        <div style="padding: 40px;">
            ${printContent}
        </div>
    `;

        // Hide print button in print view
        const btns = document.querySelectorAll('.no-print');
        btns.forEach(btn => btn.style.display = 'none');

        window.print();
        document.body.innerHTML = originalContent;

        // Re-attach event listeners (since we replaced body HTML)
        location.reload();
    }
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>