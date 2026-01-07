<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
    .management-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        margin-top: 20px;
        overflow: hidden;
    }

    .management-header {
        padding: 25px 30px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
    }

    .management-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .management-title i {
        color: #3b82f6;
        background: #eff6ff;
        padding: 8px;
        border-radius: 8px;
        font-size: 1rem;
    }

    .search-container-custom {
        padding: 25px 30px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }

    .search-wrapper {
        position: relative;
        max-width: 600px;
    }

    .search-input-group {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-input-group i {
        position: absolute;
        left: 15px;
        color: #94a3b8;
    }

    .form-control-search {
        width: 100%;
        padding: 12px 15px 12px 45px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        font-size: 0.95rem;
        color: #334155;
        background: #fff;
        transition: all 0.2s;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .form-control-search:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .customer-info-banner {
        padding: 25px 30px;
        background: #fff;
        display: none;
        border-bottom: 1px dashed #e2e8f0;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .banner-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        font-weight: 600;
    }

    .info-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
    }

    .due-badge {
        color: #ef4444;
        background: #fef2f2;
        padding: 2px 8px;
        border-radius: 6px;
    }

    .table-wrapper {
        padding: 25px 30px;
        display: none;
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .custom-table th {
        background: #f8fafc;
        padding: 12px 15px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
        text-align: left;
    }

    .custom-table td {
        padding: 16px 15px;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        background: #fff;
    }

    .custom-table tr:hover td {
        background: #f8fafc;
    }

    .col-id {
        font-family: 'Inter', monospace;
        background: #f1f5f9;
        padding: 4px 8px !important;
        border-radius: 4px;
        color: #475569 !important;
        font-size: 0.8rem !important;
        font-weight: 500;
    }

    .amount-cell {
        font-weight: 700;
        color: #059669;
        font-family: monospace;
    }

    .btn-delete {
        background: #fee2e2;
        color: #ef4444;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-delete:hover {
        background: #ef4444;
        color: #fff;
        transform: scale(1.1);
    }

    .search-results-custom {
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        right: 0;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: none;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .result-item-custom {
        padding: 12px 20px;
        cursor: pointer;
        transition: background 0.2s;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .result-item-custom:last-child { border-bottom: none; }
    .result-item-custom:hover { background: #f1f6ff; }

    .result-main { display: flex; flex-direction: column; }
    .result-name { font-weight: 600; color: #1e293b; }
    .result-sub { font-size: 0.75rem; color: #64748b; }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.3;
    }
</style>

<div class="dashboard-container">
    <div class="management-card">
        <div class="management-header">
            <div class="management-title">
                <i class="fas fa-history"></i>
                Collection Management
            </div>
            <div style="font-size: 0.85rem; color: #64748b;">
                <i class="fas fa-info-circle"></i> Edit or delete previous collection records
            </div>
        </div>

        <div class="search-container-custom">
            <div class="search-wrapper">
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" id="custSearch" class="form-control-search" placeholder="Quick find: Name, Mobile, PPPoE or Customer ID..." autocomplete="off">
                </div>
                <div id="searchResults" class="search-results-custom"></div>
            </div>
        </div>

        <div id="customerInfoBanner" class="customer-info-banner">
            <div class="banner-grid">
                <div class="info-item">
                    <span class="info-label">Customer Name</span>
                    <span id="bannerName" class="info-value">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Customer ID</span>
                    <span id="bannerId" class="info-value">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Mobile Number</span>
                    <span id="bannerMobile" class="info-value">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Current Balance</span>
                    <span id="bannerBalance" class="info-value due-badge">-</span>
                </div>
            </div>
        </div>

        <div id="tableWrapper" class="table-wrapper">
            <div style="overflow-x: auto; border-radius: 12px; border: 1px solid #e2e8f0;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Collection Date</th>
                            <th>Invoice #</th>
                            <th style="text-align: right;">Amount</th>
                            <th>Method</th>
                            <th>Next Expiry</th>
                            <th>Collector</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody">
                        <!-- Content populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <div id="emptyState" class="empty-state">
            <i class="fas fa-user-search"></i>
            <p>Search and select a customer to manage their collection history.</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('custSearch');
    const searchResults = document.getElementById('searchResults');
    const infoBanner = document.getElementById('customerInfoBanner');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const historyBody = document.getElementById('historyBody');

    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`<?= url('collection/search') ?>?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(c => {
                            const div = document.createElement('div');
                            div.className = 'result-item-custom';
                            div.innerHTML = `
                                <div class="result-main">
                                    <span class="result-name">${c.full_name}</span>
                                    <span class="result-sub">${c.mobile_no}</span>
                                </div>
                                <span class="col-id">${c.prefix_code || ''}${c.id}</span>
                            `;
                            div.onclick = () => selectCustomer(c.id);
                            searchResults.appendChild(div);
                        });
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.style.display = 'none';
                    }
                });
        }, 300);
    });

    // Close search results when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    function selectCustomer(id) {
        searchResults.style.display = 'none';
        searchInput.value = '';

        fetch(`<?= url('collection/getCustomerInfo') ?>/${id}`)
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success'){
                    const c = res.data;
                    document.getElementById('bannerName').textContent = c.full_name;
                    document.getElementById('bannerId').textContent = (c.prefix_code || '') + c.id;
                    document.getElementById('bannerMobile').textContent = c.mobile_no;
                    document.getElementById('bannerBalance').textContent = c.due_amount + ' TK';
                    
                    infoBanner.style.display = 'block';
                    emptyState.style.display = 'none';
                    fetchHistory(id);
                }
            });
    }

    function fetchHistory(customerId) {
        fetch(`<?= url('collection/getHistory') ?>/${customerId}`)
            .then(res => res.json())
            .then(res => {
                historyBody.innerHTML = '';
                if(res.data.length === 0){
                    historyBody.innerHTML = '<tr><td colspan="7" style="text-align:center; color:#94a3b8; padding:40px;">No historical payment records found for this customer.</td></tr>';
                } else {
                    res.data.forEach(col => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td style="font-weight: 500;">${col.collection_date}</td>
                            <td><span style="color:#64748b;">${col.invoice_no || 'N/A'}</span></td>
                            <td style="text-align: right;" class="amount-cell">${parseFloat(col.amount).toFixed(2)}</td>
                            <td><span style="font-size:0.75rem; background:#f1f5f9; padding:2px 6px; border-radius:4px;">${col.payment_method}</span></td>
                            <td>${col.next_expire_date || 'N/A'}</td>
                            <td><span style="color:#64748b;">${col.collected_by_name || 'System'}</span></td>
                            <td style="text-align: center;">
                                <div style="display:flex; justify-content:center;">
                                    <button class="btn-delete" onclick="deleteCol(${col.id}, ${customerId})" title="Delete Record">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        historyBody.appendChild(tr);
                    });
                }
                tableWrapper.style.display = 'block';
            });
    }

    window.deleteCol = function(id, customerId) {
        if(!confirm('CRITICAL: Are you sure you want to delete this collection? \n\nThe customer\'s due amount will increase and their expiration date will revert to the previous entry. This cannot be undone.')) return;

        const formData = new FormData();
        formData.append('id', id);

        fetch(`<?= url('collection/deleteRecord') ?>`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success'){
                alert('Success: ' + res.message);
                selectCustomer(customerId); // Refresh view
            } else {
                alert('Error: ' + res.message);
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            alert('Failed to connect to server. Check your connection.');
        });
    };
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>