<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="dashboard-container">
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <div
            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; border-bottom: 2px solid #e2e8f0; padding-bottom:15px;">
            <h2 style="margin:0; color:#1e293b;">Create New Complain</h2>
            <a href="<?= url('complain-list') ?>" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <!-- Step 1: Customer Search -->
        <div class="form-section">
            <h4 style="color:#475569; margin-bottom:15px;">
                <i class="fas fa-search" style="color:#3b82f6;"></i> Step 1: Search Customer
            </h4>
            <div class="form-group" style="position:relative;">
                <label>Search Customer (Name, ID, Mobile)</label>
                <input type="text" id="custSearch" placeholder="Type customer name, ID, or mobile number..."
                    autocomplete="off" style="font-size:15px;">
                <div id="searchResults" class="search-results"></div>
            </div>
        </div>

        <!-- Step 2: Selected Customer Info (Hidden Initially) -->
        <div id="customerInfo" class="form-section"
            style="display:none; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding:20px; border-radius:10px; margin-bottom:25px; color:white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h4 style="margin-top:0; color:white; border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom:10px;">
                <i class="fas fa-user-circle"></i> Customer Details
            </h4>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; font-size:14px;">
                <div><strong>ID:</strong> <span id="cID"></span></div>
                <div><strong>Name:</strong> <span id="cName"></span></div>
                <div><strong>Mobile:</strong> <span id="cMobile"></span></div>
                <div><strong>Address:</strong> <span id="cAddress"></span></div>
                <div><strong>Area:</strong> <span id="cArea"></span></div>
                <div><strong>Payment ID:</strong> <span id="cPayID"></span></div>
            </div>
        </div>

        <!-- Step 3: Complain Form -->
        <form action="<?= url('complain-list/store') ?>" method="POST" id="mainForm" style="display:none;">
            <div class="form-section">
                <h4 style="color:#475569; margin-bottom:15px;">
                    <i class="fas fa-clipboard-list" style="color:#3b82f6;"></i> Step 2: Complain Details
                </h4>
                <input type="hidden" name="customer_id" id="valCustId">

                <div class="form-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <div class="form-group">
                        <label><i class="fas fa-exclamation-circle"></i> Complain Type *</label>
                        <select name="complain_type_id" required>
                            <option value="">Select Issue</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-tasks"></i> Status</label>
                        <select name="status">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Resolved">Resolved</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-users"></i> Assign To (Hold Ctrl to select multiple)</label>
                    <select name="assigned_to[]" multiple style="height:100px; padding:10px;">
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color:#64748b;">Hold Ctrl (Windows) or Cmd (Mac) to select multiple employees</small>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-comment-alt"></i> Comments / Description</label>
                    <textarea name="description" rows="4" placeholder="Describe the issue in detail..."
                        style="resize:vertical;"></textarea>
                </div>

                <button type="submit" class="btn-primary"
                    style="width:100%; padding:12px; font-size:16px; margin-top:10px;">
                    <i class="fas fa-paper-plane"></i> Submit Complain
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-section {
        margin-bottom: 30px;
        padding: 20px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #cbd5e1;
        max-height: 250px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 0 0 8px 8px;
    }

    .search-item {
        padding: 12px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s;
    }

    .search-item:hover {
        background: #eff6ff;
        color: #3b82f6;
        padding-left: 20px;
    }

    .btn-secondary {
        background: #64748b;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        background: #475569;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('custSearch');
        const results = document.getElementById('searchResults');
        const infoBox = document.getElementById('customerInfo');
        const mainForm = document.getElementById('mainForm');

        // UI Elements
        const cID = document.getElementById('cID');
        const cName = document.getElementById('cName');
        const cMobile = document.getElementById('cMobile');
        const cAddress = document.getElementById('cAddress');
        const cArea = document.getElementById('cArea');
        const cPayID = document.getElementById('cPayID');
        const valCustId = document.getElementById('valCustId');

        let debounceTimer;

        input.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                results.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(() => {
                // Use direct path instead of url() helper
                const baseUrl = window.location.origin + window.location.pathname.replace('/complain-list/create', '');
                const url = baseUrl + '/complain-list/search?q=' + encodeURIComponent(query);
                console.log('Fetching:', url);
                console.log('Query:', query);

                fetch(url)
                    .then(res => {
                        console.log('Response status:', res.status);
                        if (!res.ok) {
                            throw new Error('HTTP error ' + res.status);
                        }
                        return res.text(); // Get as text first to see what we're getting
                    })
                    .then(text => {
                        console.log('Raw response:', text);
                        const data = JSON.parse(text);
                        console.log('Parsed data:', data);
                        results.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(cust => {
                                const div = document.createElement('div');
                                div.className = 'search-item';
                                div.innerHTML = `<strong>${cust.full_name}</strong> - ${cust.mobile_no} (${cust.area})`;
                                div.onclick = () => selectCustomer(cust);
                                results.appendChild(div);
                            });
                            results.style.display = 'block';
                        } else {
                            results.innerHTML = '<div class="search-item">No customers found</div>';
                            results.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                        results.innerHTML = '<div class="search-item" style="color:red;">Error: ' + err.message + '</div>';
                        results.style.display = 'block';
                    });
            }, 300);
        });

        // Hide results on click outside
        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !results.contains(e.target)) {
                results.style.display = 'none';
            }
        });

        function selectCustomer(cust) {
            // Populate Info
            cID.textContent = cust.id;
            cName.textContent = cust.full_name;
            cMobile.textContent = cust.mobile_no;
            // Build address from available fields
            const addressParts = [cust.house_no, cust.area, cust.district].filter(p => p);
            cAddress.textContent = addressParts.join(', ') || 'N/A';
            cArea.textContent = cust.area || 'N/A';
            cPayID.textContent = cust.payment_id || 'N/A';

            // set hidden value
            valCustId.value = cust.id;

            // Show Sections
            infoBox.style.display = 'block';
            mainForm.style.display = 'block';

            // Clear Search
            input.value = '';
            results.style.display = 'none';
        }
    });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>