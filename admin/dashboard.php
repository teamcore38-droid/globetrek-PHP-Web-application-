<?php
/**
 * Administrator Executive Dashboard & Analytics
 * GlobeTrek Adventures
 */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_guard.php';

require_role('admin');

$pageTitle = 'Admin Executive Dashboard';
require_once __DIR__ . '/../includes/header.php';

$db = Database::getConnection();

// Core Analytics
$totalRevenue = (float)$db->query("SELECT SUM(total_amount) FROM bookings WHERE payment_status = 'paid'")->fetchColumn() ?: 0.00;
$totalBookings = (int)$db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$confirmedBookings = (int)$db->query("SELECT COUNT(*) FROM bookings WHERE booking_status = 'confirmed'")->fetchColumn();
$totalCustomers = (int)$db->query("SELECT COUNT(*) FROM users WHERE role_id = 3")->fetchColumn();
$totalStaff = (int)$db->query("SELECT COUNT(*) FROM users WHERE role_id = 2")->fetchColumn();
$totalPackages = (int)$db->query("SELECT COUNT(*) FROM packages WHERE status = 'active'")->fetchColumn();

// Revenue by Category
$catStats = $db->query("
    SELECT c.name as category_name, COUNT(b.id) as booking_count, COALESCE(SUM(b.total_amount), 0) as total_rev
    FROM categories c
    LEFT JOIN packages p ON c.id = p.category_id
    LEFT JOIN bookings b ON p.id = b.package_id AND b.payment_status = 'paid'
    GROUP BY c.id, c.name
")->fetchAll();

// Monthly Booking Trends (last 6 months or recent)
$recentTransactions = $db->query("
    SELECT pm.*, b.booking_ref, u.full_name, p.title as package_title
    FROM payments pm
    JOIN bookings b ON pm.booking_id = b.id
    JOIN users u ON b.user_id = u.id
    JOIN packages p ON b.package_id = p.id
    ORDER BY pm.paid_at DESC
    LIMIT 5
")->fetchAll();
?>

<!-- Admin Banner -->
<div class="bg-dark text-white py-4" style="background: linear-gradient(135deg, #1e1b4b, #0f172a) !important;">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="badge bg-danger text-white fw-bold text-uppercase px-3 py-1 rounded-pill mb-2">Executive Admin</span>
                <h3 class="fw-bold mb-0">GlobeTrek System Administration & Analytics</h3>
                <span class="text-white-50 small">Oversee sales performance, manage system users, and audit operations &bull; Negombo</span>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= url('admin/users_manage.php') ?>" class="btn btn-primary btn-sm rounded-pill">
                    <i class="bi bi-people me-1"></i> Manage Accounts
                </a>
                <a href="<?= url('admin/reports.php') ?>" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="bi bi-bar-chart-line me-1"></i> Sales Reports
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Financial & Operational Stats -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card border-start border-4 border-success">
                <span class="text-muted small fw-bold text-uppercase">Total Paid Revenue</span>
                <h3 class="fw-bold text-dark mt-1 mb-0"><?= money($totalRevenue) ?></h3>
                <span class="small text-success fw-semibold"><i class="bi bi-graph-up-arrow me-1"></i> Verified Payments</span>
                <i class="bi bi-cash-stack stat-icon text-success"></i>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card border-start border-4 border-primary">
                <span class="text-muted small fw-bold text-uppercase">Total Reservations</span>
                <h3 class="fw-bold text-dark mt-1 mb-0"><?= $totalBookings ?></h3>
                <span class="small text-muted"><?= $confirmedBookings ?> Confirmed tours</span>
                <i class="bi bi-calendar-check-fill stat-icon text-primary"></i>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card border-start border-4 border-info">
                <span class="text-muted small fw-bold text-uppercase">Registered Travelers</span>
                <h3 class="fw-bold text-dark mt-1 mb-0"><?= $totalCustomers ?></h3>
                <span class="small text-muted"><?= $totalStaff ?> Agency staff members</span>
                <i class="bi bi-people-fill stat-icon text-info"></i>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card border-start border-4 border-warning">
                <span class="text-muted small fw-bold text-uppercase">Live Tour Packages</span>
                <h3 class="fw-bold text-dark mt-1 mb-0"><?= $totalPackages ?></h3>
                <span class="small text-muted">Across 5 Sri Lanka regions</span>
                <i class="bi bi-compass-fill stat-icon text-warning"></i>
            </div>
        </div>
    </div>

    <!-- Analytics Charts & Performance -->
    <div class="row g-4 mb-5">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart text-primary me-2"></i> Revenue by Tourism Category</h5>
                <canvas id="categoryRevenueChart" height="230"></canvas>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-check text-success me-2"></i> Administrative Quick Actions</h5>
                <div class="d-flex flex-column gap-3">
                    <a href="<?= url('admin/users_manage.php?action=create') ?>" class="btn btn-light border text-start p-3 rounded-3 d-flex align-items-center gap-3">
                        <i class="bi bi-person-plus-fill fs-3 text-primary"></i>
                        <div>
                            <div class="fw-bold text-dark">Add New Staff Member</div>
                            <span class="small text-muted">Create coordinator account for Negombo office</span>
                        </div>
                    </a>
                    <a href="<?= url('admin/reports.php') ?>" class="btn btn-light border text-start p-3 rounded-3 d-flex align-items-center gap-3">
                        <i class="bi bi-file-earmark-spreadsheet-fill fs-3 text-success"></i>
                        <div>
                            <div class="fw-bold text-dark">Generate Sales Audit Report</div>
                            <span class="small text-muted">View printable summary and financial breakdown</span>
                        </div>
                    </a>
                    <a href="<?= url('staff/packages_manage.php') ?>" class="btn btn-light border text-start p-3 rounded-3 d-flex align-items-center gap-3">
                        <i class="bi bi-sliders2 fs-3 text-warning"></i>
                        <div>
                            <div class="fw-bold text-dark">Inventory & Package Catalog</div>
                            <span class="small text-muted">Audit package pricing, itineraries, and status</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments & Invoices -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-credit-card-2-front text-primary me-2"></i> Recent Payment Transactions</h5>
            <span class="small text-muted">Verified payment records</span>
        </div>
        <div class="table-responsive">
            <table class="table custom-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Transaction Ref</th>
                        <th>Booking Ref</th>
                        <th>Traveler</th>
                        <th>Tour Package</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentTransactions)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">No transactions recorded yet.</td></tr>
                    <?php else: foreach ($recentTransactions as $tx): ?>
                        <tr>
                            <td class="font-monospace fw-bold text-dark small"><?= e($tx['transaction_ref']) ?></td>
                            <td class="font-monospace text-primary"><?= e($tx['booking_ref']) ?></td>
                            <td class="fw-semibold text-dark"><?= e($tx['full_name']) ?></td>
                            <td class="small text-muted"><?= e($tx['package_title']) ?></td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= e($tx['payment_method']) ?> (****<?= e($tx['card_last4'] ?: '4242') ?>)</span>
                            </td>
                            <td class="fw-bold text-primary"><?= money($tx['amount']) ?></td>
                            <td class="small text-muted"><?= date('M d, Y H:i', strtotime($tx['paid_at'])) ?></td>
                            <td>
                                <span class="badge bg-success-subtle text-success fw-bold px-2 py-1"><i class="bi bi-check-circle me-1"></i> Success</span>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js CDN for visual reporting -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('categoryRevenueChart').getContext('2d');
    const labels = <?= json_encode(array_column($catStats, 'category_name')) ?>;
    const data = <?= json_encode(array_map('floatval', array_column($catStats, 'total_rev'))) ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (USD)',
                data: data,
                backgroundColor: [
                    'rgba(2, 128, 144, 0.75)',
                    'rgba(244, 162, 97, 0.75)',
                    'rgba(231, 111, 81, 0.75)',
                    'rgba(42, 157, 143, 0.75)',
                    'rgba(38, 70, 83, 0.75)'
                ],
                borderColor: [
                    '#028090',
                    '#f4a261',
                    '#e76f51',
                    '#2a9d8f',
                    '#264653'
                ],
                borderWidth: 1.5,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '$' + value; }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
