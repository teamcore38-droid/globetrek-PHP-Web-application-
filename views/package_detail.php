<?php
/**
 * Package Detail & Instant Booking View
 * GlobeTrek Adventures
 */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getConnection();
$pkgId = (int)($_GET['id'] ?? 0);

$stmt = $db->prepare("
    SELECT p.*, c.name as category_name 
    FROM packages p
    JOIN categories c ON p.category_id = c.id
    WHERE p.id = ? AND p.status = 'active'
    LIMIT 1
");
$stmt->execute([$pkgId]);
$package = $stmt->fetch();

if (!$package) {
    set_flash('danger', 'Tour package not found or currently inactive.');
    redirect('views/packages.php');
}

$pageTitle = $package['title'];
require_once __DIR__ . '/../includes/header.php';

// Fetch Reviews
$reviewsStmt = $db->prepare("
    SELECT r.*, u.full_name 
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.package_id = ? AND r.status = 'approved'
    ORDER BY r.created_at DESC
");
$reviewsStmt->execute([$pkgId]);
$reviews = $reviewsStmt->fetchAll();

// Image logic
$imgUrl = 'https://images.unsplash.com/photo-1586861635167-e5223aadc9fe?auto=format&fit=crop&w=1200&q=80';
if (strpos($package['slug'], 'negombo') !== false) {
    $imgUrl = 'https://images.unsplash.com/photo-1546708973-b339540b5162?auto=format&fit=crop&w=1200&q=80';
} elseif (strpos($package['slug'], 'sigiriya') !== false) {
    $imgUrl = 'https://images.unsplash.com/photo-1588598198321-9735fd52455f?auto=format&fit=crop&w=1200&q=80';
} elseif (strpos($package['slug'], 'ella') !== false) {
    $imgUrl = 'https://images.unsplash.com/photo-1578330138716-765c4e2b4d1a?auto=format&fit=crop&w=1200&q=80';
} elseif (strpos($package['slug'], 'yala') !== false) {
    $imgUrl = 'https://images.unsplash.com/photo-1534177616072-ef7dc120449d?auto=format&fit=crop&w=1200&q=80';
} elseif (strpos($package['slug'], 'knuckles') !== false) {
    $imgUrl = 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80';
}

$unitPrice = ($package['discount_price'] && $package['discount_price'] > 0) ? (float)$package['discount_price'] : (float)$package['price'];
?>

<!-- Package Hero Banner -->
<div class="position-relative text-white py-5" style="background: linear-gradient(rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.85)), url('<?= $imgUrl ?>') center/cover no-repeat; min-height: 380px;">
    <div class="container py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('views/packages.php') ?>" class="text-white-50 text-decoration-none">Packages</a></li>
                <li class="breadcrumb-item active text-warning" aria-current="page"><?= e($package['title']) ?></li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="badge bg-primary px-3 py-2 rounded-pill"><?= e($package['category_name']) ?></span>
            <span class="badge bg-dark bg-opacity-75 px-3 py-2 rounded-pill"><i class="bi bi-geo-alt-fill text-warning me-1"></i> <?= e($package['destination']) ?></span>
            <span class="badge bg-dark bg-opacity-75 px-3 py-2 rounded-pill"><i class="bi bi-speedometer2 text-info me-1"></i> Difficulty: <?= e($package['difficulty']) ?></span>
        </div>

        <h1 class="display-4 fw-bold mb-3"><?= e($package['title']) ?></h1>
        <p class="lead text-white-50 mb-0" style="max-width: 800px;"><?= e($package['description']) ?></p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <!-- Main Content Area -->
        <div class="col-lg-8">
            <!-- Highlights Strip -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <div class="row g-3 text-center">
                    <div class="col-sm-3 col-6 border-end">
                        <div class="text-muted small">Duration</div>
                        <div class="fw-bold fs-5 text-dark"><?= $package['duration_days'] ?> Days / <?= $package['duration_nights'] ?> Nights</div>
                    </div>
                    <div class="col-sm-3 col-6 border-end">
                        <div class="text-muted small">Max Group</div>
                        <div class="fw-bold fs-5 text-dark"><?= $package['max_travelers'] ?> Travelers</div>
                    </div>
                    <div class="col-sm-3 col-6 border-end">
                        <div class="text-muted small">Price / Person</div>
                        <div class="fw-bold fs-5 text-primary"><?= money($unitPrice) ?></div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="text-muted small">Rating</div>
                        <div class="fw-bold fs-5 text-warning"><i class="bi bi-star-fill"></i> 4.9 <span class="text-muted small">(<?= count($reviews) ?> reviews)</span></div>
                    </div>
                </div>
            </div>

            <!-- Day-by-Day Detailed Itinerary -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <h4 class="fw-bold mb-4 d-flex align-items-center">
                    <i class="bi bi-map text-primary me-2"></i> Tour Itinerary Day-by-Day
                </h4>
                
                <div class="timeline">
                    <?php 
                    $itineraryLines = explode("\n", $package['itinerary']);
                    $dayNum = 1;
                    foreach ($itineraryLines as $line):
                        $line = trim($line);
                        if (empty($line)) continue;
                    ?>
                        <div class="d-flex align-items-start gap-3 mb-4">
                            <span class="badge bg-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <?= $dayNum++ ?>
                            </span>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-dark"><?= nl2br(e($line)) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Accommodation & Transport Info -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <h4 class="fw-bold mb-3 d-flex align-items-center">
                    <i class="bi bi-buildings text-primary me-2"></i> Accommodation & Transportation
                </h4>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-house-door text-warning me-2"></i> Accommodation Standard</h6>
                            <p class="small text-muted mb-0"><?= e($package['accommodation_info']) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-car-front text-info me-2"></i> Vehicle & Transport Service</h6>
                            <p class="small text-muted mb-0"><?= e($package['transport_info']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inclusions & Exclusions -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <h4 class="fw-bold mb-3 d-flex align-items-center">
                    <i class="bi bi-check2-all text-primary me-2"></i> What's Included & Excluded
                </h4>
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-success mb-2"><i class="bi bi-check-circle-fill me-1"></i> Included in Price</h6>
                        <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-0">
                            <?php foreach (explode("\n", $package['inclusions']) as $inc): if (trim($inc)): ?>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check2 text-success mt-1"></i>
                                    <span><?= e(trim($inc)) ?></span>
                                </li>
                            <?php endif; endforeach; ?>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-danger mb-2"><i class="bi bi-x-circle-fill me-1"></i> Not Included (Excluded)</h6>
                        <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-0">
                            <?php foreach (explode("\n", $package['exclusions']) as $exc): if (trim($exc)): ?>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-dash text-danger mt-1"></i>
                                    <span><?= e(trim($exc)) ?></span>
                                </li>
                            <?php endif; endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Traveler Reviews -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0 d-flex align-items-center">
                        <i class="bi bi-chat-heart text-primary me-2"></i> Traveler Reviews (<?= count($reviews) ?>)
                    </h4>
                </div>

                <?php if (empty($reviews)): ?>
                    <p class="text-muted small mb-0">No reviews yet for this package. Be the first to book and share your experience!</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($reviews as $rev): ?>
                            <div class="p-3 bg-light rounded-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark"><?= e($rev['full_name']) ?></strong>
                                    <span class="rating-stars">
                                        <?= str_repeat('<i class="bi bi-star-fill text-warning"></i> ', $rev['rating']) ?>
                                    </span>
                                </div>
                                <p class="small text-muted mb-1"><?= e($rev['comment']) ?></p>
                                <span class="small text-secondary" style="font-size: 0.75rem;"><?= date('M d, Y', strtotime($rev['created_at'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sticky Booking Widget Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 p-4 sticky-lg-top bg-white" style="top: 90px;">
                <div class="d-flex justify-content-between align-items-baseline mb-3">
                    <div>
                        <span class="small text-muted">Price per traveler</span>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="fs-2 fw-bold text-primary" id="unit_price" data-price="<?= $unitPrice ?>"><?= money($unitPrice) ?></span>
                            <?php if ($package['discount_price']): ?>
                                <span class="text-decoration-line-through text-muted"><?= money($package['price']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-2 rounded-pill">Instant Booking</span>
                </div>

                <hr class="my-3">

                <!-- Booking Form -->
                <form action="<?= url('controllers/booking_process.php') ?>" method="POST" id="bookingForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="package_id" value="<?= $package['id'] ?>">
                    <input type="hidden" name="total_amount" id="total_amount_input" value="<?= $unitPrice ?>">

                    <!-- Travel Date Selection -->
                    <div class="mb-3">
                        <label for="travel_date" class="form-label small fw-bold text-muted">
                            <i class="bi bi-calendar-event me-1"></i> Starting Date *
                        </label>
                        <input type="date" class="form-control" id="travel_date" name="travel_date" min="<?= date('Y-m-d') ?>" required value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
                    </div>

                    <!-- Number of Travelers -->
                    <div class="mb-3">
                        <label for="travelers_count" class="form-label small fw-bold text-muted">
                            <i class="bi bi-people me-1"></i> Number of Travelers *
                        </label>
                        <input type="number" class="form-control" id="travelers_count" name="travelers_count" min="1" max="<?= $package['max_travelers'] ?>" value="1" required>
                        <span class="small text-muted" style="font-size: 0.75rem;">Maximum allowed: <?= $package['max_travelers'] ?> persons</span>
                    </div>

                    <!-- Special Requests -->
                    <div class="mb-3">
                        <label for="special_requests" class="form-label small fw-bold text-muted">
                            <i class="bi bi-chat-square-text me-1"></i> Special Requests (Optional)
                        </label>
                        <textarea class="form-control" id="special_requests" name="special_requests" rows="2" placeholder="e.g. Vegetarian meals, infant seat, airport pickup time..."></textarea>
                    </div>

                    <!-- Payment Simulation Card Details -->
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-bold text-dark mb-0">
                                <i class="bi bi-credit-card me-1"></i> Payment Simulation
                            </label>
                            <span class="badge bg-secondary" style="font-size: 0.65rem;">Sandbox / Demo</span>
                        </div>
                        <input type="text" name="card_number" class="form-control form-control-sm mb-2" placeholder="Card Number (e.g. 4242 4242 4242 4242)" value="4242 8888 9999 1234">
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="text" class="form-control form-control-sm" placeholder="MM/YY" value="12/28">
                            </div>
                            <div class="col-6">
                                <input type="password" class="form-control form-control-sm" placeholder="CVV" value="789">
                            </div>
                        </div>
                    </div>

                    <!-- Price Summary Box -->
                    <div class="d-flex justify-content-between align-items-center p-3 bg-primary-subtle text-primary-emphasis rounded-3 mb-4">
                        <span class="fw-bold">Total Estimated:</span>
                        <span class="fs-4 fw-bold" id="total_price_display"><?= money($unitPrice) ?></span>
                    </div>

                    <?php if (is_logged_in()): ?>
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold fs-6">
                            <i class="bi bi-shield-check me-1"></i> Confirm & Book Tour
                        </button>
                    <?php else: ?>
                        <a href="<?= url('views/login.php?redirect=' . urlencode('views/package_detail.php?id=' . $package['id'])) ?>" class="btn btn-warning w-100 py-3 rounded-pill fw-bold text-dark">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Book
                        </a>
                        <span class="d-block text-center small text-muted mt-2">New traveler? <a href="<?= url('views/register.php') ?>" class="text-primary fw-semibold">Register here</a></span>
                    <?php endif; ?>
                </form>

                <div class="mt-4 pt-3 border-top text-center text-muted small">
                    <p class="mb-1"><i class="bi bi-shield-lock-fill text-success me-1"></i> 100% Secure Checkout Guarantee</p>
                    <p class="mb-0">Questions? Contact Negombo office at <br><strong><?= COMPANY_PHONE ?></strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
