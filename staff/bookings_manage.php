<?php
/**
 * Bookings Management & Coordination (Staff / Admin)
 * GlobeTrek Adventures
 */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_guard.php';

require_role(['staff', 'admin']);

$pageTitle = 'Manage Bookings & Service Coordination';
require_once __DIR__ . '/../includes/header.php';

$db = Database::getConnection();

// Filter by status
$statusFilter = trim($_GET['status'] ?? 'all');
$sql = "
    SELECT b.*, u.full_name, u.email, u.phone as customer_phone,
           p.title as package_title, p.destination, p.duration_days
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN packages p ON b.package_id = p.id
";

$params = [];
if ($statusFilter !== 'all' && in_array($statusFilter, ['pending', 'confirmed', 'cancelled', 'completed'])) {
    $sql .= " WHERE b.booking_status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY b.id DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('staff/dashboard.php') ?>" class="text-decoration-none">Staff Desk</a></li>
                    <li class="breadcrumb-item active">Bookings & Coordination</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0">Booking Reservations & Logistics</h3>
        </div>
        <!-- Status Filter Tabs -->
        <div class="btn-group btn-group-sm rounded-pill p-1 bg-white shadow-sm border">
            <a href="<?= url('staff/bookings_manage.php?status=all') ?>" class="btn rounded-pill <?= $statusFilter === 'all' ? 'btn-primary' : 'btn-light' ?>">All</a>
            <a href="<?= url('staff/bookings_manage.php?status=pending') ?>" class="btn rounded-pill <?= $statusFilter === 'pending' ? 'btn-warning' : 'btn-light' ?>">Pending</a>
            <a href="<?= url('staff/bookings_manage.php?status=confirmed') ?>" class="btn rounded-pill <?= $statusFilter === 'confirmed' ? 'btn-success' : 'btn-light' ?>">Confirmed</a>
            <a href="<?= url('staff/bookings_manage.php?status=completed') ?>" class="btn rounded-pill <?= $statusFilter === 'completed' ? 'btn-info' : 'btn-light' ?>">Completed</a>
            <a href="<?= url('staff/bookings_manage.php?status=cancelled') ?>" class="btn rounded-pill <?= $statusFilter === 'cancelled' ? 'btn-danger' : 'btn-light' ?>">Cancelled</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table custom-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Booking Ref</th>
                        <th>Traveler Details</th>
                        <th>Tour Package</th>
                        <th>Date & Party</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Coordination Notes</th>
                        <th class="text-end">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No bookings found for the selected status.</td>
                        </tr>
                    <?php else: foreach ($bookings as $b): ?>
                        <tr>
                            <td>
                                <span class="fw-bold text-dark font-monospace"><?= e($b['booking_ref']) ?></span>
                                <div class="small text-muted"><?= date('M d, Y', strtotime($b['created_at'])) ?></div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= e($b['full_name']) ?></div>
                                <div class="small text-muted"><i class="bi bi-envelope me-1"></i><?= e($b['email']) ?></div>
                                <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?= e($b['customer_phone'] ?: 'N/A') ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= e($b['package_title']) ?></div>
                                <span class="small text-muted"><?= e($b['destination']) ?> &bull; <?= $b['duration_days'] ?> Days</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= date('M d, Y', strtotime($b['travel_date'])) ?></div>
                                <span class="badge bg-light text-dark border"><?= $b['travelers_count'] ?> Persons</span>
                            </td>
                            <td>
                                <span class="fw-bold text-primary"><?= money($b['total_amount']) ?></span>
                            </td>
                            <td>
                                <?php if ($b['payment_status'] === 'paid'): ?>
                                    <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">Paid</span>
                                <?php elseif ($b['payment_status'] === 'refunded'): ?>
                                    <span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1">Refunded</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning fw-bold px-2 py-1">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($b['booking_status'] === 'confirmed'): ?>
                                    <span class="badge bg-success text-white fw-bold px-2 py-1">Confirmed</span>
                                <?php elseif ($b['booking_status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark fw-bold px-2 py-1">Pending</span>
                                <?php elseif ($b['booking_status'] === 'completed'): ?>
                                    <span class="badge bg-info text-white fw-bold px-2 py-1">Completed</span>
                                <?php else: ?>
                                    <span class="badge bg-danger text-white fw-bold px-2 py-1">Cancelled</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="small text-muted d-inline-block text-truncate" style="max-width: 140px;">
                                    <?= e($b['staff_notes'] ?: 'No notes yet') ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editModal<?= $b['id'] ?>">
                                    <i class="bi bi-gear-fill me-1"></i> Update
                                </button>
                                <a href="<?= url('views/customer/invoice.php?id=' . $b['id']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill" title="View Voucher">
                                    <i class="bi bi-file-earmark-text"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Status Update Modal -->
                        <div class="modal fade" id="editModal<?= $b['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $b['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header bg-light border-0">
                                        <h5 class="modal-title fw-bold" id="modalLabel<?= $b['id'] ?>">
                                            Update Booking <span class="font-monospace text-primary">#<?= e($b['booking_ref']) ?></span>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="<?= url('controllers/booking_process.php') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">

                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">Booking Reservation Status</label>
                                                <select name="booking_status" class="form-select">
                                                    <option value="pending" <?= $b['booking_status'] === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                                                    <option value="confirmed" <?= $b['booking_status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed (Service Assigned)</option>
                                                    <option value="completed" <?= $b['booking_status'] === 'completed' ? 'selected' : '' ?>>Tour Completed</option>
                                                    <option value="cancelled" <?= $b['booking_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">Payment Transaction Status</label>
                                                <select name="payment_status" class="form-select">
                                                    <option value="paid" <?= $b['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid / Verified</option>
                                                    <option value="unpaid" <?= $b['payment_status'] === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                                                    <option value="refunded" <?= $b['payment_status'] === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">Special Requests from Traveler:</label>
                                                <div class="p-2 bg-light rounded text-muted small">
                                                    <?= e($b['special_requests'] ?: 'None specified') ?>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">Staff Logistics & Provider Coordination Notes</label>
                                                <textarea name="staff_notes" class="form-control" rows="3" placeholder="e.g. Jetwing Hotel Room 104 reserved. Chauffeur Perera assigned (+94 77 111 2222)..."><?= e($b['staff_notes'] ?? '') ?></textarea>
                                                <span class="small text-muted" style="font-size: 0.72rem;">Track hotel booking confirmations and chauffeur details</span>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 bg-light">
                                            <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill fw-semibold px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
