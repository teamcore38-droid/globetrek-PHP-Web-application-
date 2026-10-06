<?php
/**
 * Staff Operations Dashboard
 * GlobeTrek Adventures
 */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_guard.php';

require_role(['staff', 'admin']);

$pageTitle = 'Travel Agency Staff Portal';
require_once __DIR__ . '/../includes/header.php';

$db = Database::getConnection();

// Metrics
$pendingBookingsCount = $db->query("SELECT COUNT(*) FROM bookings WHERE booking_status = 'pending'")->fetchColumn();
$confirmedBookingsCount = $db->query("SELECT COUNT(*) FROM bookings WHERE booking_status = 'confirmed'")->fetchColumn();
$activePackagesCount = $db->query("SELECT COUNT(*) FROM packages WHERE status = 'active'")->fetchColumn();
$pendingInquiriesCount = $db->query("SELECT COUNT(*) FROM inquiries WHERE status = 'pending'")->fetchColumn();
$customTripsCount = $db->query("SELECT COUNT(*) FROM custom_trips WHERE status = 'pending'")->fetchColumn();

// Recent Bookings
$recentBookings = $db->query("
    SELECT b.*, u.full_name, u.email, p.title as package_title, p.destination
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN packages p ON b.package_id = p.id
    ORDER BY b.created_at DESC
    LIMIT 6
")->fetchAll();

// Recent Inquiries
$recentInquiries = $db->query("
    SELECT * FROM inquiries
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll();
?>

<!-- Staff Banner -->
<div class="bg-dark text-white py-4" style="background: linear-gradient(135deg, #0f766e, #0f172a) !important;">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="badge bg-warning text-dark fw-bold text-uppercase px-3 py-1 rounded-pill mb-2">Staff Operations Desk</span>
                <h3 class="fw-bold mb-0">Travel Agency Coordination Panel</h3>
                <span class="text-white-50 small">Manage packages, booking reservations, and traveler inquiries &bull; Negombo Office</span>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= url('staff/package_edit.php') ?>" class="btn btn-warning btn-sm rounded-pill fw-bold text-dark">
                    <i class="bi bi-plus-circle me-1"></i> Add New Package
                </a>
                <a href="<?= url('staff/bookings_manage.php') ?>" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="bi bi-calendar-check me-1"></i> Review All Bookings
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Operational KPI Cards -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card border-start border-4 border-warning">
                <span class="text-muted small fw-bold text-uppercase">Pending Bookings</span>
                <h3 class="fw-bold text-dark mt-1 mb-0"><?= $pendingBookingsCount ?></h3>
                <span class="small text-muted">Awaiting confirmation</span>
                <i class="bi bi-clock-history stat-icon text-warning"></i>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card border-start border-4 border-success">
                <span class="text-muted small fw-bold text-uppercase">Confirmed Tours</span>
                <h3 class="fw-bold text-dark mt-1 mb-0"><?= $confirmedBookingsCount ?></h3>
                <span class="small text-muted">Ready for departure</span>
                <i class="bi bi-check-circle-fill stat-icon text-success"></i>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card border-start border-4 border-primary">
                <span class="text-muted small fw-bold text-uppercase">Active Tour Packages</span>
                <h3 class="fw-bold text-dark mt-1 mb-0"><?= $activePackagesCount ?></h3>
                <span class="small text-muted">Visible to travelers</span>
                <i class="bi bi-geo-alt-fill stat-icon text-primary"></i>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card border-start border-4 border-danger">
                <span class="text-muted small fw-bold text-uppercase">Open Queries</span>
                <h3 class="fw-bold text-dark mt-1 mb-0"><?= $pendingInquiriesCount ?></h3>
                <span class="small text-muted">Awaiting staff reply</span>
                <i class="bi bi-chat-dots-fill stat-icon text-danger"></i>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Shortcuts -->
    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <a href="<?= url('staff/packages_manage.php') ?>" class="btn btn-outline-primary w-100 p-3 rounded-4 text-start d-flex align-items-center gap-3">
                <i class="bi bi-journal-album fs-2 text-primary"></i>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Tour Packages</h6>
                    <span class="small text-muted">Create & update tours</span>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?= url('staff/bookings_manage.php') ?>" class="btn btn-outline-success w-100 p-3 rounded-4 text-start d-flex align-items-center gap-3">
                <i class="bi bi-bookmark-check fs-2 text-success"></i>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Bookings Desk</h6>
                    <span class="small text-muted">Confirm & assign notes</span>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?= url('staff/inquiries_manage.php') ?>" class="btn btn-outline-warning w-100 p-3 rounded-4 text-start d-flex align-items-center gap-3">
                <i class="bi bi-chat-quote fs-2 text-warning"></i>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Customer Queries</h6>
                    <span class="small text-muted">Reply to questions</span>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?= url('views/custom_trip.php') ?>" class="btn btn-outline-info w-100 p-3 rounded-4 text-start d-flex align-items-center gap-3">
                <i class="bi bi-sliders fs-2 text-info"></i>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Tailored Trips</h6>
                    <span class="small text-muted"><?= $customTripsCount ?> custom requests</span>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Bookings Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar2-range text-primary me-2"></i> Recent Booking Reservations</h5>
                    <a href="<?= url('staff/bookings_manage.php') ?>" class="small text-primary text-decoration-none fw-semibold">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table custom-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Ref & Date</th>
                                <th>Traveler</th>
                                <th>Package</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end">Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentBookings as $rb): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold font-monospace text-dark"><?= e($rb['booking_ref']) ?></div>
                                        <span class="small text-muted"><?= date('M d, Y', strtotime($rb['travel_date'])) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= e($rb['full_name']) ?></div>
                                        <span class="small text-muted"><?= e($rb['email']) ?></span>
                                    </td>
                                    <td>
                                        <span class="small text-dark fw-semibold"><?= e($rb['package_title']) ?></span>
                                    </td>
                                    <td class="fw-bold text-primary"><?= money($rb['total_amount']) ?></td>
                                    <td>
                                        <?php if ($rb['booking_status'] === 'confirmed'): ?>
                                            <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">Confirmed</span>
                                        <?php elseif ($rb['booking_status'] === 'pending'): ?>
                                            <span class="badge bg-warning-subtle text-warning fw-bold px-2 py-1">Pending</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1"><?= e($rb['booking_status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= url('staff/bookings_manage.php') ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                                            Update
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Queries Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-chat-dots text-warning me-2"></i> Inquiries</h5>
                    <a href="<?= url('staff/inquiries_manage.php') ?>" class="small text-primary text-decoration-none fw-semibold">View Desk</a>
                </div>

                <div class="d-flex flex-column gap-3">
                    <?php if (empty($recentInquiries)): ?>
                        <p class="small text-muted mb-0">No inquiries logged yet.</p>
                    <?php else: foreach ($recentInquiries as $inq): ?>
                        <div class="p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <strong class="small text-dark"><?= e($inq['name']) ?></strong>
                                <span class="badge <?= $inq['status'] === 'replied' ? 'bg-success' : 'bg-warning text-dark' ?>" style="font-size: 0.65rem;">
                                    <?= strtoupper($inq['status']) ?>
                                </span>
                            </div>
                            <div class="small fw-semibold text-primary mb-1"><?= e($inq['subject']) ?></div>
                            <p class="small text-muted mb-2 text-truncate"><?= e($inq['message']) ?></p>
                            <a href="<?= url('staff/inquiries_manage.php') ?>" class="small text-decoration-none fw-semibold">Respond &rarr;</a>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
