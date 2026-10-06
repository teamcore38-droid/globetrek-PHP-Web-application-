<?php
/**
 * Booking & Payment Controller
 * GlobeTrek Adventures
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_guard.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$db = Database::getConnection();

// --- 1. CREATE NEW BOOKING & SIMULATE PAYMENT ---
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login('views/login.php');

    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Security verification failed. Please try again.');
        redirect('views/packages.php');
    }

    $userId = current_user_id();
    $packageId = (int)($_POST['package_id'] ?? 0);
    $travelDate = trim($_POST['travel_date'] ?? '');
    $travelersCount = max(1, (int)($_POST['travelers_count'] ?? 1));
    $specialRequests = trim($_POST['special_requests'] ?? '');
    $emergencyContact = trim($_POST['emergency_contact'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'Card Payment');
    $cardNumber = trim($_POST['card_number'] ?? '4242');

    // Fetch package details
    $pkgStmt = $db->prepare("SELECT * FROM packages WHERE id = ? AND status = 'active' LIMIT 1");
    $pkgStmt->execute([$packageId]);
    $package = $pkgStmt->fetch();

    if (!$package) {
        set_flash('danger', 'Selected tour package is not available.');
        redirect('views/packages.php');
    }

    // Validate travel date
    $currentDate = date('Y-m-d');
    if (empty($travelDate) || $travelDate < $currentDate) {
        set_flash('danger', 'Please select a valid future travel date.');
        redirect('views/package_detail.php?id=' . $packageId);
    }

    // Calculate Financials
    $unitPrice = ($package['discount_price'] && $package['discount_price'] > 0) ? (float)$package['discount_price'] : (float)$package['price'];
    $subtotal = $unitPrice * $travelersCount;
    $totalAmount = $subtotal;

    // Generate unique reference numbers
    $bookingRef = 'GTA-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));
    $transactionRef = 'TXN-' . strtoupper(substr(md5(uniqid()), 0, 10));
    $cardLast4 = substr(preg_replace('/\D/', '', $cardNumber), -4) ?: '4242';

    // Begin Database Transaction for ACID integrity
    try {
        $db->beginTransaction();

        // 1. Insert Booking Record
        $bookingStmt = $db->prepare("
            INSERT INTO bookings (
                booking_ref, user_id, package_id, travel_date, travelers_count,
                subtotal, discount, total_amount, special_requests, emergency_contact,
                booking_status, payment_status, payment_method, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, 0.00, ?, ?, ?, 'confirmed', 'paid', ?, NOW())
        ");
        $bookingStmt->execute([
            $bookingRef, $userId, $packageId, $travelDate, $travelersCount,
            $subtotal, $totalAmount, $specialRequests, $emergencyContact, $paymentMethod
        ]);
        $bookingId = $db->lastInsertId();

        // 2. Insert Payment Record
        $paymentStmt = $db->prepare("
            INSERT INTO payments (
                booking_id, transaction_ref, amount, payment_method, status, card_last4, paid_at
            ) VALUES (?, ?, ?, ?, 'successful', ?, NOW())
        ");
        $paymentStmt->execute([
            $bookingId, $transactionRef, $totalAmount, $paymentMethod, $cardLast4
        ]);

        $db->commit();

        set_flash('success', "Booking confirmed successfully! Your booking reference is <strong>$bookingRef</strong>.");
        redirect('views/customer/invoice.php?id=' . $bookingId);

    } catch (Exception $e) {
        $db->rollBack();
        set_flash('danger', 'Booking transaction failed: ' . htmlspecialchars($e->getMessage()));
        redirect('views/package_detail.php?id=' . $packageId);
    }
}

// --- 2. CANCEL BOOKING (CUSTOMER) ---
if ($action === 'cancel') {
    require_login();
    $bookingId = (int)($_GET['id'] ?? 0);
    $userId = current_user_id();

    // Verify ownership
    $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$bookingId, $userId]);
    $booking = $stmt->fetch();

    if ($booking && in_array($booking['booking_status'], ['pending', 'confirmed'])) {
        $update = $db->prepare("UPDATE bookings SET booking_status = 'cancelled' WHERE id = ?");
        $update->execute([$bookingId]);
        set_flash('info', 'Your booking #' . $booking['booking_ref'] . ' has been cancelled.');
    } else {
        set_flash('danger', 'Booking could not be cancelled or you do not have permission.');
    }
    redirect('views/customer/dashboard.php');
}

// --- 3. UPDATE BOOKING STATUS (STAFF / ADMIN) ---
if ($action === 'update_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_role(['admin', 'staff']);

    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Security verification failed.');
        redirect('staff/bookings_manage.php');
    }

    $bookingId = (int)($_POST['booking_id'] ?? 0);
    $bookingStatus = $_POST['booking_status'] ?? 'pending';
    $paymentStatus = $_POST['payment_status'] ?? 'unpaid';
    $staffNotes = trim($_POST['staff_notes'] ?? '');

    $stmt = $db->prepare("
        UPDATE bookings 
        SET booking_status = ?, payment_status = ?, staff_notes = ? 
        WHERE id = ?
    ");
    $stmt->execute([$bookingStatus, $paymentStatus, $staffNotes, $bookingId]);

    set_flash('success', 'Booking #' . $bookingId . ' has been updated successfully.');
    redirect('staff/bookings_manage.php');
}

redirect('index.php');
