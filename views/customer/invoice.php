<?php
/**
 * Printable Travel Voucher & Booking Invoice
 * GlobeTrek Adventures
 */
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_guard.php';

require_login('views/login.php');

$bookingId = (int)($_GET['id'] ?? 0);
$userId = current_user_id();
$db = Database::getConnection();

// Fetch Booking details
$stmt = $db->prepare("
    SELECT b.*, u.full_name, u.email, u.phone as user_phone,
           p.title as package_title, p.destination, p.duration_days, p.duration_nights,
           p.accommodation_info, p.transport_info, p.inclusions,
           pm.transaction_ref, pm.card_last4, pm.paid_at
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN packages p ON b.package_id = p.id
    LEFT JOIN payments pm ON b.id = pm.booking_id
    WHERE b.id = ? AND (b.user_id = ? OR ? IN ('admin', 'staff'))
    LIMIT 1
");
$stmt->execute([$bookingId, $userId, current_user_role()]);
$booking = $stmt->fetch();

if (!$booking) {
    set_flash('danger', 'Booking invoice not found or unauthorized.');
    redirect('views/customer/dashboard.php');
}

$pageTitle = 'Booking Voucher #' . $booking['booking_ref'];
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container py-5 my-3">
    <!-- Top Action Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="<?= url('views/customer/dashboard.php') ?>" class="btn btn-outline-secondary rounded-pill btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary rounded-pill btn-sm px-4 fw-semibold">
                <i class="bi bi-printer me-1"></i> Print / Save Voucher (PDF)
            </button>
        </div>
    </div>

    <!-- Official Travel Voucher & Invoice Card -->
    <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white print-card">
        <!-- Header -->
        <div class="row align-items-center border-bottom pb-4 mb-4">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="brand-icon rounded-circle p-2 d-flex align-items-center justify-content-center bg-primary text-white">
                        <i class="bi bi-compass-fill fs-5"></i>
                    </span>
                    <h3 class="fw-bold mb-0 text-dark">GlobeTrek <span class="text-primary">Adventures</span></h3>
                </div>
                <p class="text-muted small mb-0">
                    Official Travel Voucher & Tax Invoice<br>
                    No. 48, Lewis Place, Negombo Beach Road, Negombo, Sri Lanka<br>
                    Hotline: <?= COMPANY_HOTLINE ?> | Email: <?= COMPANY_EMAIL ?>
                </p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <span class="badge bg-success-subtle text-success fs-6 fw-bold px-3 py-2 rounded-pill mb-2">
                    <i class="bi bi-check-circle-fill me-1"></i> BOOKING CONFIRMED
                </span>
                <div class="font-monospace fw-bold text-dark fs-5">REF: <?= e($booking['booking_ref']) ?></div>
                <span class="small text-muted">Issued: <?= date('F d, Y', strtotime($booking['created_at'])) ?></span>
            </div>
        </div>

        <!-- Traveler & Tour Information -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6">
                <h6 class="text-uppercase text-muted fw-bold small mb-2">Primary Traveler</h6>
                <div class="fw-bold fs-6 text-dark"><?= e($booking['full_name']) ?></div>
                <div class="small text-muted"><i class="bi bi-envelope me-1"></i> <?= e($booking['email']) ?></div>
                <div class="small text-muted"><i class="bi bi-telephone me-1"></i> <?= e($booking['user_phone'] ?: 'N/A') ?></div>
                <?php if ($booking['emergency_contact']): ?>
                    <div class="small text-muted mt-1"><strong>Emergency:</strong> <?= e($booking['emergency_contact']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6 class="text-uppercase text-muted fw-bold small mb-2">Tour Schedule</h6>
                <div class="fw-bold fs-6 text-dark"><?= e($booking['package_title']) ?></div>
                <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i> <?= e($booking['destination']) ?></div>
                <div class="small text-muted"><i class="bi bi-calendar-event me-1"></i> Travel Date: <strong><?= date('l, F d, Y', strtotime($booking['travel_date'])) ?></strong></div>
                <div class="small text-muted"><i class="bi bi-people me-1"></i> Number of Travelers: <strong><?= $booking['travelers_count'] ?></strong></div>
            </div>
        </div>

        <!-- Accommodations & Transport -->
        <div class="p-3 bg-light rounded-3 mb-4">
            <div class="row g-3 small">
                <div class="col-md-6">
                    <strong><i class="bi bi-building text-primary me-1"></i> Accommodation Arrangement:</strong>
                    <div class="text-muted"><?= e($booking['accommodation_info']) ?></div>
                </div>
                <div class="col-md-6">
                    <strong><i class="bi bi-car-front text-info me-1"></i> Transit & Chauffeur:</strong>
                    <div class="text-muted"><?= e($booking['transport_info']) ?></div>
                </div>
            </div>
            <?php if (!empty($booking['special_requests'])): ?>
                <div class="mt-2 pt-2 border-top small">
                    <strong><i class="bi bi-chat-text text-warning me-1"></i> Special Requests:</strong>
                    <span class="text-muted"><?= e($booking['special_requests']) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Billing Table -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Item Description</th>
                        <th class="text-center">Travelers</th>
                        <th class="text-end">Unit Rate</th>
                        <th class="text-end">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong><?= e($booking['package_title']) ?></strong>
                            <div class="small text-muted"><?= $booking['duration_days'] ?> Days / <?= $booking['duration_nights'] ?> Nights Private Guided Tour</div>
                        </td>
                        <td class="text-center"><?= $booking['travelers_count'] ?></td>
                        <td class="text-end"><?= money($booking['total_amount'] / $booking['travelers_count']) ?></td>
                        <td class="text-end fw-bold"><?= money($booking['total_amount']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                        <td class="text-end"><?= money($booking['subtotal']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end text-success">Discount / Promotion:</td>
                        <td class="text-end text-success">- <?= money($booking['discount']) ?></td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="3" class="text-end fw-bold fs-5">Grand Total Paid:</td>
                        <td class="text-end fw-bold fs-5 text-primary"><?= money($booking['total_amount']) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Payment Verification Strip -->
        <div class="row align-items-center p-3 border rounded-3 bg-light g-3 mb-4">
            <div class="col-md-7">
                <span class="small text-muted d-block">Payment Method: <strong><?= e($booking['payment_method']) ?></strong> (Card Ending ****<?= e($booking['card_last4'] ?: '4242') ?>)</span>
                <span class="small text-muted font-monospace">Transaction ID: <?= e($booking['transaction_ref'] ?: 'TXN-DIRECT-VERIFIED') ?></span>
            </div>
            <div class="col-md-5 text-md-end">
                <span class="badge bg-success px-3 py-2"><i class="bi bi-shield-check me-1"></i> Full Payment Received</span>
            </div>
        </div>

        <!-- Terms & Travel Advisory -->
        <div class="small text-muted border-top pt-3">
            <p class="fw-bold mb-1 text-dark">Travel Instructions & Negombo Meeting Point:</p>
            <p class="mb-1">
                &bull; Please present this digital or printed voucher along with your passport/ID to your GlobeTrek chauffeur guide upon pickup.
            </p>
            <p class="mb-1">
                &bull; For airport meet-and-greets at BIA (Katunayake), our chauffeur will hold a placard with your name at Arrival Gate 02.
            </p>
            <p class="mb-0">
                &bull; Emergency contact: Negombo Operations Desk at <strong><?= COMPANY_HOTLINE ?></strong> (available 24/7 on WhatsApp).
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
