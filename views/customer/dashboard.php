<?php
/**
 * Customer Traveler Dashboard
 * GlobeTrek Adventures
 */
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_guard.php';

require_login('views/login.php');

$pageTitle = 'My Bookings & Journeys';
require_once __DIR__ . '/../../includes/header.php';

$db = Database::getConnection();
$userId = current_user_id();

// Fetch customer bookings
$bookingsStmt = $db->prepare("
    SELECT b.*, p.title as package_title, p.destination, p.duration_days, p.duration_nights, p.slug
    FROM bookings b
    JOIN packages p ON b.package_id = p.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$bookingsStmt->execute([$userId]);
$myBookings = $bookingsStmt->fetchAll();

// Fetch custom trips requested
$customStmt = $db->prepare("
    SELECT * FROM custom_trips 
    WHERE user_id = ? OR email = ?
    ORDER BY created_at DESC
");
$customStmt->execute([$userId, current_user()['email']]);
$myCustomTrips = $customStmt->fetchAll();

// Fetch queries
$queriesStmt = $db->prepare("
    SELECT * FROM inquiries 
    WHERE user_id = ? OR email = ?
    ORDER BY created_at DESC
");
$queriesStmt->execute([$userId, current_user()['email']]);
$myQueries = $queriesStmt->fetchAll();
?>

<!-- Dashboard Header -->
<div class="bg-dark text-white py-4" style="background: linear-gradient(135deg, #0f172a, #1e293b) !important;">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary text-white p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="bi bi-person-fill fs-3"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0"><?= e(current_user()['full_name']) ?></h3>
                    <span class="text-white-50 small"><i class="bi bi-envelope me-1"></i> <?= e(current_user()['email']) ?> &bull; Traveler Account</span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= url('views/packages.php') ?>" class="btn btn-warning btn-sm rounded-pill fw-semibold text-dark">
                    <i class="bi bi-compass me-1"></i> Explore More Tours
                </a>
                <a href="<?= url('views/custom_trip.php') ?>" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="bi bi-sliders me-1"></i> Plan Custom Trip
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Stat Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-primary">
                <span class="text-muted small fw-bold text-uppercase">Total Bookings</span>
                <h3 class="fw-bold text-dark mt-1 mb-0"><?= count($myBookings) ?></h3>
                <i class="bi bi-calendar2-check-fill stat-icon text-primary"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-success">
                <span class="text-muted small fw-bold text-uppercase">Custom Trip Requests</span>
                <h3 class="fw-bold text-dark mt-1 mb-0"><?= count($myCustomTrips) ?></h3>
                <i class="bi bi-sliders stat-icon text-success"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-warning">
                <span class="text-muted small fw-bold text-uppercase">Questions / Queries</span>
                <h3 class="fw-bold text-dark mt-1 mb-0"><?= count($myQueries) ?></h3>
                <i class="bi bi-chat-left-dots-fill stat-icon text-warning"></i>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 gap-2" id="travelerTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4" id="bookings-tab" data-bs-toggle="pill" data-bs-target="#tab-bookings" type="button" role="tab">
                <i class="bi bi-briefcase me-1"></i> Tour Bookings (<?= count($myBookings) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4" id="custom-tab" data-bs-toggle="pill" data-bs-target="#tab-custom" type="button" role="tab">
                <i class="bi bi-pencil-square me-1"></i> Custom Trips (<?= count($myCustomTrips) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4" id="queries-tab" data-bs-toggle="pill" data-bs-target="#tab-queries" type="button" role="tab">
                <i class="bi bi-chat-text me-1"></i> Inquiries & Replies (<?= count($myQueries) ?>)
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="travelerTabContent">
        <!-- Tab 1: Bookings -->
        <div class="tab-pane fade show active" id="tab-bookings" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">My Booked Tour Packages</h5>
                    <span class="small text-muted">Click invoice to view or print official voucher</span>
                </div>
                <div class="table-responsive">
                    <table class="table custom-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Booking Ref</th>
                                <th>Tour Package</th>
                                <th>Travel Date</th>
                                <th>Party</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($myBookings)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                        You haven't booked any tour packages yet.<br>
                                        <a href="<?= url('views/packages.php') ?>" class="btn btn-sm btn-primary rounded-pill mt-3">Browse Available Tours</a>
                                    </td>
                                </tr>
                            <?php else: foreach ($myBookings as $b): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark font-monospace"><?= e($b['booking_ref']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= e($b['package_title']) ?></div>
                                        <span class="small text-muted"><i class="bi bi-geo-alt me-1"></i> <?= e($b['destination']) ?> &bull; <?= $b['duration_days'] ?> Days</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark"><?= date('M d, Y', strtotime($b['travel_date'])) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= $b['travelers_count'] ?> Persons</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary"><?= money($b['total_amount']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($b['payment_status'] === 'paid'): ?>
                                            <span class="badge bg-success-subtle text-success fw-bold px-2 py-1"><i class="bi bi-check-circle me-1"></i> Paid</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning fw-bold px-2 py-1"><i class="bi bi-clock me-1"></i> Unpaid</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($b['booking_status'] === 'confirmed'): ?>
                                            <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1">Confirmed</span>
                                        <?php elseif ($b['booking_status'] === 'pending'): ?>
                                            <span class="badge bg-warning-subtle text-warning fw-bold px-2 py-1">Pending</span>
                                        <?php elseif ($b['booking_status'] === 'cancelled'): ?>
                                            <span class="badge bg-danger-subtle text-danger fw-bold px-2 py-1">Cancelled</span>
                                        <?php else: ?>
                                            <span class="badge bg-info-subtle text-info fw-bold px-2 py-1">Completed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= url('views/customer/invoice.php?id=' . $b['id']) ?>" class="btn btn-outline-primary" title="View / Print Voucher & Invoice">
                                                <i class="bi bi-receipt me-1"></i> Voucher
                                            </a>
                                            <?php if (in_array($b['booking_status'], ['pending', 'confirmed'])): ?>
                                                <a href="<?= url('controllers/booking_process.php?action=cancel&id=' . $b['id']) ?>" 
                                                   class="btn btn-outline-danger confirm-action" 
                                                   data-confirm-message="Are you sure you want to cancel booking <?= e($b['booking_ref']) ?>?">
                                                    <i class="bi bi-x-circle"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 2: Custom Trips -->
        <div class="tab-pane fade" id="tab-custom" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-dark">My Tailor-Made Trip Requests</h5>
                </div>
                <div class="table-responsive">
                    <table class="table custom-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Requested Date</th>
                                <th>Destinations</th>
                                <th>Duration</th>
                                <th>Party</th>
                                <th>Preferences</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($myCustomTrips)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        You haven't submitted any custom trip requests yet.<br>
                                        <a href="<?= url('views/custom_trip.php') ?>" class="btn btn-sm btn-primary rounded-pill mt-3">Design a Tailored Trip</a>
                                    </td>
                                </tr>
                            <?php else: foreach ($myCustomTrips as $ct): ?>
                                <tr>
                                    <td class="small text-muted"><?= date('M d, Y', strtotime($ct['created_at'])) ?></td>
                                    <td class="fw-semibold text-dark"><?= e($ct['destinations']) ?></td>
                                    <td><?= $ct['duration_days'] ?> Days</td>
                                    <td><?= $ct['adults_count'] ?> Adults, <?= $ct['children_count'] ?> Kids</td>
                                    <td class="small text-muted">
                                        <div><strong>Stay:</strong> <?= e($ct['accommodation_type']) ?></div>
                                        <div><strong>Transit:</strong> <?= e($ct['transport_type']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info fw-bold text-uppercase px-2 py-1"><?= e($ct['status']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 3: Queries & Replies -->
        <div class="tab-pane fade" id="tab-queries" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold mb-3 text-dark">My Questions & Official Replies</h5>
                <?php if (empty($myQueries)): ?>
                    <p class="text-muted small py-4 text-center">No queries submitted yet. You can submit queries through our <a href="<?= url('views/contact.php') ?>">Contact Desk</a>.</p>
                <?php else: foreach ($myQueries as $q): ?>
                    <div class="p-3 mb-3 border rounded-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold text-dark mb-0"><?= e($q['subject']) ?></h6>
                            <span class="badge <?= $q['status'] === 'replied' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <?= strtoupper($q['status']) ?>
                            </span>
                        </div>
                        <p class="small text-muted mb-2"><?= nl2br(e($q['message'])) ?></p>
                        <span class="small text-secondary d-block mb-2" style="font-size: 0.72rem;">Sent on <?= date('M d, Y H:i', strtotime($q['created_at'])) ?></span>

                        <?php if ($q['reply']): ?>
                            <div class="p-3 bg-white border-start border-4 border-success rounded-end mt-2">
                                <strong class="small text-success d-block mb-1"><i class="bi bi-reply-fill"></i> GlobeTrek Staff Response:</strong>
                                <p class="small text-dark mb-0"><?= nl2br(e($q['reply'])) ?></p>
                                <span class="small text-muted" style="font-size: 0.7rem;">Replied on <?= date('M d, Y H:i', strtotime($q['replied_at'])) ?></span>
                            </div>
                        <?php else: ?>
                            <span class="small text-muted fst-italic"><i class="bi bi-clock me-1"></i> Awaiting reply from Negombo staff...</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
