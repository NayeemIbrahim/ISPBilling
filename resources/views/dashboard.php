<?php include __DIR__ . '/partials/header.php'; ?>

<main class="dashboard-container">

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="card stat-card">
            <h3>Total Customers</h3>
            <div class="value"><?= number_format($stats['active_count'] ?? 0) ?></div>
            <div class="trend up">Active</div>
        </div>
        <div class="card stat-card">
            <h3>Monthly Revenue</h3>
            <div class="value"><?= number_format($stats['total_revenue'] ?? 0) ?> TK</div>
            <div class="trend up">Collected</div>
        </div>
        <div class="card stat-card">
            <h3>Due Amount</h3>
            <div class="value"><?= number_format($stats['total_due'] ?? 0) ?> TK</div>
            <div class="trend neutral">To Collect</div>
        </div>
        <div class="card stat-card">
            <h3>Actions</h3>
            <div style="margin-top:10px;">
                <form action="<?= url('customer/seed') ?>" method="POST" style="display:inline;">
                    <button type="submit" class="btn-table" style="background:#10b981;">Generate Dummy Data</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <div class="card chart-container">
            <h3>Revenue Growth</h3>
            <canvas id="revenueChart"></canvas>
        </div>
        <div class="card chart-container">
            <h3>Customers by Area</h3>
            <canvas id="areaChart"></canvas>
        </div>
        <div class="card chart-container">
            <h3>Customer Status Overview</h3>
            <canvas id="statusChart"></canvas>
        </div>
        <div class="card chart-container">
            <h3>Ticket Status</h3>
            <canvas id="ticketChart"></canvas>
        </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="card table-container">
        <h3>Recent Pending Customers</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Area</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pendingCustomers)): ?>
                    <?php foreach ($pendingCustomers as $customer): ?>
                        <tr>
                            <td>#<?= $customer['id'] ?></td>
                            <td><?= $customer['full_name'] ?></td>
                            <td><?= $customer['mobile_no'] ?></td>
                            <td><?= $customer['area'] ?></td>
                            <td><?= $customer['created_at'] ? date('d/m/Y', strtotime($customer['created_at'])) : '-' ?></td>
                            <td style="display:flex; gap:5px;">
                                <a href="<?= url('customer/show/' . $customer['id']) ?>" class="btn-table">View</a>
                                <form action="<?= url('customer/activate/' . $customer['id']) ?>" method="POST"
                                    onsubmit="return confirm('Activate this customer?');">
                                    <button type="submit" class="btn-table" style="background:#3b82f6;">Activate</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center">No recent customers found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<!-- Pass data to JS -->
<script>
    window.customerStatusData = <?= json_encode($statusData ?? []) ?>;
    window.customerAreaData = <?= json_encode($areaData ?? []) ?>;
</script>
<?php include __DIR__ . '/partials/footer.php'; ?>