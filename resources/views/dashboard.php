<?php include __DIR__ . '/partials/header.php'; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root {
        --primary-color: #3b82f6;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --purple-color: #8b5cf6;
        --card-bg: #ffffff;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
    }

    .dashboard-container {
        padding: 0;
        font-family: 'Inter', system-ui, sans-serif;
    }

    /* Shortcut Links */
    .shortcut-bar {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        overflow-x: auto;
        padding-bottom: 5px;
    }

    .shortcut-btn {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 10px 20px;
        border-radius: 50px;
        color: var(--text-secondary);
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        transition: all 0.2s;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .shortcut-btn:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        transform: translateY(-1px);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(226, 232, 240, 0.8);
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 16px;
    }

    .stat-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-secondary);
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.025em;
    }

    .stat-trend {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 8px;
    }

    .trend-up {
        color: var(--success-color);
    }

    .trend-down {
        color: var(--danger-color);
    }

    /* Charts Layout */
    .charts-grid-top {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 30px;
    }

    .charts-grid-mid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 30px;
    }

    @media (max-width: 1024px) {

        .charts-grid-top,
        .charts-grid-mid {
            grid-template-columns: 1fr;
        }
    }

    .chart-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .chart-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .chart-filter select {
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        color: var(--text-secondary);
        font-size: 0.85rem;
        background: #f8fafc;
        outline: none;
        cursor: pointer;
    }

    /* Pending Table */
    .custom-table {
        width: 100%;
        border-collapse: collapse;
    }

    .custom-table th,
    .custom-table td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    .custom-table th {
        color: #64748b;
        font-weight: 600;
        font-size: 0.8rem;
        background: #f8fafc;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
    }

    .status-pending {
        background: #fee2e2;
        color: #ef4444;
    }

    .status-pending:hover {
        background: #fecaca;
    }

    .btn-action {
        color: #3b82f6;
        background: #eff6ff;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        /* Fixed: inline-block */
    }

    .btn-action:hover {
        background: #dbeafe;
    }
</style>

<div class="dashboard-container">

    <!-- Shortcuts -->
    <div class="shortcut-bar">
        <a href="<?= url('customer/create') ?>" class="shortcut-btn"><i class="fas fa-user-plus"></i> New Customer</a>
        <a href="<?= url('customer/pending') ?>" class="shortcut-btn"><i class="fas fa-user-clock"></i> Pending
            Requests</a>
        <a href="<?= url('complain-list/create') ?>" class="shortcut-btn"><i class="fas fa-exclamation-circle"></i> New
            Complain</a>
        <a href="<?= url('report/collectionReport') ?>" class="shortcut-btn"><i class="fas fa-file-invoice-dollar"></i>
            Collections</a>
        <a href="<?= url('report/customerSummary') ?>" class="shortcut-btn"><i class="fas fa-user-clock"></i> Customer
            Summary</a>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon-wrapper" style="background: #e0e7ff; color: #4f46e5;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-label">Active Customers</div>
            <div class="stat-value"><?= number_format($stats['active_count'] ?? 0) ?></div>
            <div class="stat-trend trend-up"><i class="fas fa-check-circle"></i> <span>Online</span></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper" style="background: #dcfce7; color: #16a34a;">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-label">Collection (This Month)</div>
            <div class="stat-value">৳<?= number_format($stats['total_revenue'] ?? 0) ?></div>
            <div class="stat-trend trend-up"><i class="fas fa-chart-bar"></i> <span>Current Cycle</span></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper" style="background: #fee2e2; color: #dc2626;">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="stat-label">Total Due</div>
            <div class="stat-value">৳<?= number_format($stats['total_due'] ?? 0) ?></div>
            <div class="stat-trend trend-down"><i class="fas fa-exclamation-triangle"></i> <span>Outstanding</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper" style="background: #ffedd5; color: #ea580c;">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="stat-label">Pending Tickets</div>
            <div class="stat-value"><?= isset($ticketData['Pending']) ? $ticketData['Pending'] : 0 ?></div>
            <div class="stat-trend" style="color: #ea580c;">
                <a href="<?= url('complain-list') ?>" style="text-decoration:none; color:inherit;">View Tickets ></a>
            </div>
        </div>
    </div>

    <!-- Row 1: Collection & Ticket (SWAPPED) -->
    <div class="charts-grid-top">
        <!-- Collection Data -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">Collection Analytics</div>
                <div class="chart-filter">
                    <select id="collectionFilter">
                        <option value="daily">Last 7 Days (Daily)</option>
                        <option value="weekly">Last 30 Days (Daily)</option>
                        <option value="month3">Last 3 Months (Monthly)</option>
                        <option value="month6">Last 6 Months (Monthly)</option>
                    </select>
                </div>
            </div>
            <div style="height: 250px;">
                <canvas id="collectionChart"></canvas>
            </div>
        </div>

        <!-- Ticket Status -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">Ticket Status</div>
            </div>
            <div style="height: 250px; display: flex; justify-content: center;">
                <canvas id="ticketChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Row 2: Revenue & Pending (SWAPPED) -->
    <div class="charts-grid-mid">
        <!-- Revenue Growth -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">Revenue Growth</div>
                <div class="chart-filter">
                    <select id="revenueFilter">
                        <option value="6">Last 6 Months</option>
                        <option value="12">Last 1 Year</option>
                        <option value="24">Last 2 Years</option>
                    </select>
                </div>
            </div>
            <!-- Reduced Height -->
            <div style="height: 220px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Recent Pending List -->
        <div class="chart-card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
            <div
                style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
                <div class="chart-title">Recent Pending Customers</div>
                <a href="<?= url('customer/pending') ?>"
                    style="font-size:0.85rem; color: #3b82f6; text-decoration:none;">View All</a>
            </div>
            <div style="overflow-y: auto; flex:1;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Area</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingCustomers)): ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:20px; color:#94a3b8;">No pending requests
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pendingCustomers as $cust): ?>
                                <tr>
                                    <td style="font-weight:500;">
                                        <?= htmlspecialchars($cust['full_name']) ?>
                                        <div style="font-size:0.75rem; color:#64748b;">
                                            <?= htmlspecialchars($cust['mobile_no']) ?>
                                        </div>
                                    </td>
                                    <td><?= date('d M', strtotime($cust['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($cust['area']) ?></td>
                                    <td style="text-align:right;">
                                        <!-- Fixed Link to Show (Profile/Edit) -->
                                        <a href="<?= url('customer/show/' . $cust['id']) ?>" class="btn-action">Review</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Developer Credit -->
    <div
        style="text-align: right; margin-top: 20px; padding: 10px 0; font-size: 0.8rem; color: #94a3b8; font-weight: 500;">
        Developed By <span style="color: #64748b;">NefconIT</span>
    </div>
</div>

<script>
    // --- Data Injection ---
    // Revenue
    const allRevenueLabels = <?= json_encode(array_keys($revenueTrend)) ?>;
    const allRevenueData = <?= json_encode(array_values($revenueTrend)) ?>;
    // Ticket
    const ticketLabels = <?= json_encode(array_keys($ticketData)) ?>;
    const ticketValues = <?= json_encode(array_values($ticketData)) ?>;

    // Collection: Last 30 Days
    const dateLabels30 = <?= json_encode(array_keys($collDaily)) ?>;
    const dateValues30 = <?= json_encode(array_values($collDaily)) ?>;
    // Collection: Monthly
    const monthLabels12 = <?= json_encode(array_keys($collMonthly)) ?>;
    const monthValues12 = <?= json_encode(array_values($collMonthly)) ?>;


    // --- 1. Collection Analytics (Bar) - NOW TOP ROW ---
    const ctxColl = document.getElementById('collectionChart').getContext('2d');
    const collChart = new Chart(ctxColl, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Collection',
                data: [],
                backgroundColor: '#60a5fa', // Light Blue
                borderRadius: 4
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 2], color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });

    function updateCollChart(mode) {
        let labels = [];
        let data = [];

        if (mode === 'daily') {
            labels = dateLabels30.slice(-15);
            data = dateValues30.slice(-15);
        } else if (mode === 'weekly') {
            labels = dateLabels30;
            data = dateValues30;
        } else if (mode === 'month3') {
            labels = monthLabels12.slice(-3);
            data = monthValues12.slice(-3);
        } else if (mode === 'month6') {
            labels = monthLabels12.slice(-6);
            data = monthValues12.slice(-6);
        }

        // Format Date Labels (e.g., 2026-01-20 -> 20 Jan)
        if (mode === 'daily' || mode === 'weekly') {
            labels = labels.map(dateStr => {
                const d = new Date(dateStr);
                return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
            });
        }

        collChart.data.labels = labels;
        collChart.data.datasets[0].data = data;
        collChart.update();
    }

    updateCollChart('daily');
    document.getElementById('collectionFilter').addEventListener('change', (e) => updateCollChart(e.target.value));


    // --- 2. Ticket Status Chart (Doughnut) ---
    const ctxTicket = document.getElementById('ticketChart').getContext('2d');
    new Chart(ctxTicket, {
        type: 'doughnut',
        data: {
            labels: ticketLabels.length ? ticketLabels : ['No Data'],
            datasets: [{
                data: ticketValues.length ? ticketValues : [1],
                backgroundColor: ['#f59e0b', '#10b981', '#ef4444', '#cbd5e1'],
                borderWidth: 0
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } } }
        }
    });


    // --- 3. Revenue Chart (Line) - NOW BOTTOM ROW ---
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Revenue',
                data: [],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3b82f6',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 2], color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });

    function updateRevenueChart(months) {
        const start = Math.max(0, allRevenueLabels.length - months);
        revenueChart.data.labels = allRevenueLabels.slice(start);
        revenueChart.data.datasets[0].data = allRevenueData.slice(start);
        revenueChart.update();
    }
    updateRevenueChart(6);
    document.getElementById('revenueFilter').addEventListener('change', (e) => updateRevenueChart(parseInt(e.target.value)));

</script>

<?php include __DIR__ . '/partials/footer.php'; ?>