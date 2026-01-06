// Dashboard Logic
document.addEventListener('DOMContentLoaded', () => {

    // --- Helper to safely get context ---
    const getSafeContext = (id) => {
        const el = document.getElementById(id);
        if (!el) {
            console.warn(`Chart element #${id} not found.`);
            return null;
        }
        return el.getContext('2d');
    };

    // --- Chart.js Initializations ---
    if (typeof Chart === 'undefined') {
        console.error('Chart.js library is not loaded.');
        return;
    }

    // 1. Revenue Chart (Line)
    const revenueCtx = getSafeContext('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue ($)',
                    data: [12000, 19000, 15000, 22000, 24000, 24500],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // 2. Customers by Area (Doughnut)
    const areaCtx = getSafeContext('areaChart');
    if (areaCtx) {
        const areaDataRaw = window.customerAreaData || {};
        const areaLabels = Object.keys(areaDataRaw);
        const areaCounts = Object.values(areaDataRaw);

        const areaColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316', '#14b8a6', '#6366f1'];

        new Chart(areaCtx, {
            type: 'doughnut',
            data: {
                labels: areaLabels.length > 0 ? areaLabels : ['No Data'],
                datasets: [{
                    data: areaCounts.length > 0 ? areaCounts : [1],
                    backgroundColor: areaColors.slice(0, Math.max(areaLabels.length, 1))
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12, font: { size: 10 } }
                    }
                }
            }
        });
    }

    // 3. Customer Status Overview (Bar)
    const statusCtx = getSafeContext('statusChart');
    if (statusCtx) {
        const statusMap = {
            'active': { label: 'Active', color: '#10b981' },
            'pending': { label: 'Pending', color: '#f59e0b' },
            'inactive': { label: 'Inactive', color: '#ef4444' },
            'temp_disable': { label: 'Temp Disabled', color: '#64748b' },
            'free': { label: 'Free', color: '#3b82f6' }
        };

        const statusDataRaw = window.customerStatusData || {};
        const statusLabels = [];
        const statusCounts = [];
        const statusColors = [];

        Object.keys(statusMap).forEach(key => {
            if (statusDataRaw[key] !== undefined) {
                statusLabels.push(statusMap[key].label);
                statusCounts.push(parseInt(statusDataRaw[key]));
                statusColors.push(statusMap[key].color);
            }
        });

        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: statusLabels,
                datasets: [{
                    label: 'Customers',
                    data: statusCounts,
                    backgroundColor: statusColors,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // 4. Ticket Status (Placeholder / Legacy)
    const ticketCtx = getSafeContext('ticketChart');
    if (ticketCtx) {
        new Chart(ticketCtx, {
            type: 'bar',
            data: {
                labels: ['Pending', 'Resolved', 'In Progress'],
                datasets: [{
                    label: 'Tickets',
                    data: [34, 120, 15],
                    backgroundColor: ['#ef4444', '#10b981', '#f59e0b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

});

