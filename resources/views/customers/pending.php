<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
    .sort-header {
        cursor: pointer;
        user-select: none;
        position: relative;
        padding-right: 20px !important;
    }

    .sort-header:hover {
        background: #f1f5f9;
    }

    .sort-arrows {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 10px;
        display: flex;
        flex-direction: column;
        color: #cbd5e1;
    }

    .sort-arrows .active {
        color: #3b82f6;
    }

    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 0;
        margin-top: 10px;
        border-top: 1px solid #e2e8f0;
    }

    .pagination-info {
        font-size: 14px;
        color: #64748b;
    }

    .pagination-links {
        display: flex;
        gap: 5px;
    }

    .page-link {
        padding: 5px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        text-decoration: none;
        color: #1e293b;
        font-size: 14px;
        transition: all 0.2s;
    }

    .page-link:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .page-link.active {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    .page-link.disabled {
        color: #cbd5e1;
        pointer-events: none;
        background: #f8fafc;
    }
</style>

<main class="dashboard-container">
    <div class="card">
        <div class="search-header"
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Pending Customers <span style="font-size:16px; color:#666; font-weight:normal;">(Total:
                    <?= $totalRecords ?? 0 ?>)</span></h2>

            <form method="GET" action="<?= url('customer/pending') ?>" class="search-box"
                style="display:flex; gap:10px; align-items:center;">
                <input type="text" name="q" placeholder="Search pending..." value="<?= htmlspecialchars($q ?? '') ?>"
                    style="padding: 8px; width: 250px; border: 1px solid #ccc; border-radius: 4px;">
                <button type="submit"
                    style="padding: 8px 15px; background:#3b82f6; color:white; border:none; border-radius:4px; cursor:pointer;">Search</button>
                <?php if (!empty($q)): ?>
                    <a href="<?= url('customer/pending') ?>"
                        style="color:#ef4444; text-decoration:none; font-size:14px;">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <?php
        // Helper for sort URLs
        function sortUrlPending($column, $currentSort, $currentOrder)
        {
            $newOrder = ($column === $currentSort && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
            $params = $_GET;
            $params['sort'] = $column;
            $params['order'] = $newOrder;
            $params['page'] = 1;
            return url('customer/pending?' . http_build_query($params));
        }

        // Helper for arrow classes
        function arrowClassPending($column, $direction, $currentSort, $currentOrder)
        {
            return ($column === $currentSort && strtoupper($direction) === $currentOrder) ? 'active' : '';
        }
        ?>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="sort-header" onclick="location.href='<?= sortUrlPending('id', $sort, $order) ?>'">
                            ID
                            <div class="sort-arrows">
                                <i class="fas fa-caret-up <?= arrowClassPending('id', 'ASC', $sort, $order) ?>"></i>
                                <i class="fas fa-caret-down <?= arrowClassPending('id', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th class="sort-header"
                            onclick="location.href='<?= sortUrlPending('full_name', $sort, $order) ?>'">
                            Name
                            <div class="sort-arrows">
                                <i
                                    class="fas fa-caret-up <?= arrowClassPending('full_name', 'ASC', $sort, $order) ?>"></i>
                                <i
                                    class="fas fa-caret-down <?= arrowClassPending('full_name', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th class="sort-header"
                            onclick="location.href='<?= sortUrlPending('mobile_no', $sort, $order) ?>'">
                            Mobile
                            <div class="sort-arrows">
                                <i
                                    class="fas fa-caret-up <?= arrowClassPending('mobile_no', 'ASC', $sort, $order) ?>"></i>
                                <i
                                    class="fas fa-caret-down <?= arrowClassPending('mobile_no', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th class="sort-header" onclick="location.href='<?= sortUrlPending('area', $sort, $order) ?>'">
                            Area
                            <div class="sort-arrows">
                                <i class="fas fa-caret-up <?= arrowClassPending('area', 'ASC', $sort, $order) ?>"></i>
                                <i
                                    class="fas fa-caret-down <?= arrowClassPending('area', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th class="sort-header"
                            onclick="location.href='<?= sortUrlPending('package_name', $sort, $order) ?>'">
                            Package
                            <div class="sort-arrows">
                                <i
                                    class="fas fa-caret-up <?= arrowClassPending('package_name', 'ASC', $sort, $order) ?>"></i>
                                <i
                                    class="fas fa-caret-down <?= arrowClassPending('package_name', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th class="sort-header"
                            onclick="location.href='<?= sortUrlPending('created_at', $sort, $order) ?>'">
                            Date
                            <div class="sort-arrows">
                                <i
                                    class="fas fa-caret-up <?= arrowClassPending('created_at', 'ASC', $sort, $order) ?>"></i>
                                <i
                                    class="fas fa-caret-down <?= arrowClassPending('created_at', 'DESC', $sort, $order) ?>"></i>
                            </div>
                        </th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?= htmlspecialchars($customer['prefix_code'] ?? '') ?><?= $customer['id'] ?></td>
                                <td>
                                    <strong><?= $customer['full_name'] ?></strong>
                                </td>
                                <td><?= $customer['mobile_no'] ?></td>
                                <td><?= $customer['area'] ?></td>
                                <td><?= $customer['package_name'] ?></td>
                                <td><?= $customer['created_at'] ? date('d/m/Y', strtotime($customer['created_at'])) : '-' ?>
                                </td>
                                <td><span style="color:orange; font-weight:bold;">Pending</span></td>
                                <td>
                                    <div style="display:flex; gap:5px;">
                                        <a href="<?= url('customer/show/' . $customer['id']) ?>" class="btn-table"
                                            style="background:#3b82f6; text-decoration:none;">View</a>
                                        <form action="<?= url('customer/activate/' . $customer['id']) ?>" method="POST"
                                            onsubmit="return confirm('Activate this customer?');">
                                            <button type="submit" class="btn-table"
                                                style="background:#10b981;">Activate</button>
                                        </form>
                                        <form action="<?= url('customer/delete/' . $customer['id']) ?>" method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this pending customer?');">
                                            <button type="submit" class="btn-table" style="background:#ef4444;">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align:center;">No pending customers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing Page <?= $currentPage ?> of <?= $totalPages ?>
                </div>
                <div class="pagination-links">
                    <?php
                    // Helper for page URLs
                    function pageUrlPending($pageNum)
                    {
                        $params = $_GET;
                        $params['page'] = $pageNum;
                        return url('customer/pending?' . http_build_query($params));
                    }
                    ?>

                    <a href="<?= pageUrlPending(1) ?>" class="page-link <?= $currentPage == 1 ? 'disabled' : '' ?>"
                        title="First Page">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    <a href="<?= pageUrlPending($currentPage - 1) ?>"
                        class="page-link <?= $currentPage == 1 ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-left"></i>
                    </a>

                    <?php
                    $start = max(1, $currentPage - 2);
                    $end = min($totalPages, $currentPage + 2);

                    for ($i = $start; $i <= $end; $i++):
                        ?>
                        <a href="<?= pageUrlPending($i) ?>" class="page-link <?= $currentPage == $i ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <a href="<?= pageUrlPending($currentPage + 1) ?>"
                        class="page-link <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="<?= pageUrlPending($totalPages) ?>"
                        class="page-link <?= $currentPage == $totalPages ? 'disabled' : '' ?>" title="Last Page">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>