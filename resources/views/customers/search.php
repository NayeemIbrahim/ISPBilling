<?php include __DIR__ . '/../partials/header.php'; ?>

<main class="dashboard-container">
    <div class="card">
        <div class="form-header">
            <h2>Search Customer</h2>
        </div>

        <div class="search-box-large" style="margin: 20px 0; display:flex; gap:10px;">
            <input type="text" id="liveSearchInput" placeholder="Enter Name, Mobile or ID..."
                style="flex:1; padding: 12px; font-size: 16px; border: 2px solid #ddd; border-radius: 6px;">
            <button class="btn-save" style="width: auto; padding: 0 30px;">Search</button>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Payment ID</th>
                        <th>Area</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="searchResults">
                    <tr>
                        <td colspan="8" style="text-align:center; color:#888;">Start typing to search...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    const searchInput = document.getElementById('liveSearchInput');
    const resultsBody = document.getElementById('searchResults');

    searchInput.addEventListener('keyup', function () {
        const query = this.value;
        if (query.length < 1) {
            resultsBody.innerHTML = '<tr><td colspan="8" style="text-align:center; color:#888;">Start typing to search...</td></tr>';
            return;
        }

        fetch('<?= url("customer/filter?q=") ?>' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const rows = data.data;
                    let html = '';
                    if (rows.length > 0) {
                        rows.forEach(c => {
                            let statusLabel = 'Active';
                            let statusColor = 'green';
                            if (c.status === 'pending') { statusColor = 'orange'; statusLabel = 'Pending'; }
                            else if (c.status === 'inactive') { statusColor = 'red'; statusLabel = 'Inactive'; }
                            else if (c.status === 'temp_disable') { statusColor = 'gray'; statusLabel = 'T. Disable'; }
                            else if (c.status === 'free') { statusColor = 'blue'; statusLabel = 'Free'; }

                            html += `
                            <tr>
                                <td>${c.prefix_code || ''}${c.id}</td>
                                <td><a href="<?= url('customer/show/') ?>${c.id}" style="font-weight:bold; color: #2563eb; text-decoration:none;">${c.full_name}</a></td>
                                <td>${c.mobile_no}</td>
                                <td>${c.payment_id || '-'}</td>
                                <td>${c.area || '-'}</td>
                                <td><span style="color:${statusColor}; font-weight:bold;">${statusLabel}</span></td>
                                <td>
                                    <a href="<?= url('customer/show/') ?>${c.id}" class="btn-table" style="background:#3b82f6; text-decoration:none;">View Profile</a>
                                </td>
                            </tr>
                            `;
                        });
                        resultsBody.innerHTML = html;
                    } else {
                        resultsBody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No match found</td></tr>';
                    }
                }
            })
            .catch(err => console.error(err));
    });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>