<?php
/**
 * Sales, Revenue & Customer Reports (Administrator Only)
 * GlobeTrek Adventures
 */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_guard.php';

require_role('admin');

$db = Database::getConnection();

// Overall Metrics
$totalSales = (float)$db->query("SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE payment_status = 'paid'")->fetchColumn();
$totalBookings = (int)$db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$avgBooking = $totalBookings > 0 ? ($totalSales / $totalBookings) : 0;
$totalTravelers = (int)$db->query("SELECT COALESCE(SUM(travelers_count), 0) FROM bookings WHERE booking_status = 'confirmed'")->fetchColumn();

// Package Performance Ranking
$packagePerformance = $db->query("
    SELECT p.id, p.title, p.destination, c.name as category_name,
           COUNT(b.id) as total_bookings,
           COALESCE(SUM(b.travelers_count), 0) as total_guests,
           COALESCE(SUM(CASE WHEN b.payment_status = 'paid' THEN b.total_amount ELSE 0 END), 0) as revenue
    FROM packages p
    JOIN categories c ON p.category_id = c.id
    LEFT JOIN bookings b ON p.id = b.package_id
    GROUP BY p.id, p.title, p.destination, c.name
    ORDER BY revenue DESC, total_bookings DESC
")->fetchAll();

// Booking Status Breakdown
$statusCounts = $db->query("
    SELECT booking_status, COUNT(*) as count 
    FROM bookings 
    GROUP BY booking_status
")->fetchAll(PDO::FETCH_KEY_PAIR);

$pageTitle = 'Sales & Operations Reports';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 no-print">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('admin/dashboard.php') ?>" class="text-decoration-none">Admin Panel</a></li>
                    <li class="breadcrumb-item active">Business Reports</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0">Sales & Operations Analytics Report</h3>
        </div>
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-semibold">
            <i class="bi bi-printer me-1"></i> Print / Export Report
        </button>
    </div>

    <!-- Printable Header for Official Records -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white print-card mb-4">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">GlobeTrek Adventures &bull; Operations & Revenue Audit</h4>
                <span class="small text-muted">Negombo, Sri Lanka &bull; Generated on: <?= date('F d, Y H:i') ?></span>
            </div>
            <div class="text-end">
                <span class="badge bg-primary px-3 py-2 fs-6">CSE5009 Business Intelligence</span>
            </div>
        </div>

        <!-- Metric KPI Highlights -->
        <div class="row g-4 text-center mb-4">
            <div class="col-md-3 col-6">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="small text-muted text-uppercase fw-bold">Total Paid Sales</div>
                    <div class="fs-4 fw-bold text-success"><?= money($totalSales) ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="small text-muted text-uppercase fw-bold">Total Bookings</div>
                    <div class="fs-4 fw-bold text-primary"><?= $totalBookings ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="small text-muted text-uppercase fw-bold">Average Order Value</div>
                    <div class="fs-4 fw-bold text-dark"><?= money($avgBooking) ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="small text-muted text-uppercase fw-bold">Confirmed Guests</div>
                    <div class="fs-4 fw-bold text-info"><?= $totalTravelers ?> Travelers</div>
                </div>
            </div>
        </div>

        <!-- Tour Performance Ranking Table -->
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-trophy text-warning me-2"></i> Tour Package Performance & Revenue Breakdown</h5>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tour Package Title</th>
                        <th>Category</th>
                        <th>Destination</th>
                        <th class="text-center">Bookings</th>
                        <th class="text-center">Total Guests</th>
                        <th class="text-end">Gross Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; foreach ($packagePerformance as $pkg): ?>
                        <tr>
                            <td class="text-muted fw-bold"><?= $rank++ ?></td>
                            <td class="fw-bold text-dark"><?= e($pkg['title']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= e($pkg['category_name']) ?></span></td>
                            <td><?= e($pkg['destination']) ?></td>
                            <td class="text-center"><?= $pkg['total_bookings'] ?></td>
                            <td class="text-center"><?= $pkg['total_guests'] ?></td>
                            <td class="text-end fw-bold text-primary"><?= money($pkg['revenue']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">Totals:</td>
                        <td class="text-center"><?= $totalBookings ?></td>
                        <td class="text-center"><?= $totalTravelers ?></td>
                        <td class="text-end text-success fs-5"><?= money($totalSales) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Reservation Status Distribution -->
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart text-primary me-2"></i> Reservation Pipeline Distribution</h5>
        <div class="row g-3 text-center">
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded bg-success-subtle text-success-emphasis">
                    <strong>Confirmed:</strong> <?= $statusCounts['confirmed'] ?? 0 ?>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded bg-warning-subtle text-warning-emphasis">
                    <strong>Pending:</strong> <?= $statusCounts['pending'] ?? 0 ?>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded bg-info-subtle text-info-emphasis">
                    <strong>Completed:</strong> <?= $statusCounts['completed'] ?? 0 ?>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded bg-danger-subtle text-danger-emphasis">
                    <strong>Cancelled:</strong> <?= $statusCounts['cancelled'] ?? 0 ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
