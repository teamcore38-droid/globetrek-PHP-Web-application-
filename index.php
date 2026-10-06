<?php
/**
 * Home Page - GlobeTrek Adventures
 */
$pageTitle = 'Explore Sri Lanka with GlobeTrek Adventures';
require_once __DIR__ . '/includes/header.php';

$db = Database::getConnection();

// Fetch Categories
$categories = $db->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();

// Fetch Featured & Active Packages
$featuredPackages = $db->query("
    SELECT p.*, c.name as category_name 
    FROM packages p
    JOIN categories c ON p.category_id = c.id
    WHERE p.status = 'active'
    ORDER BY p.featured DESC, p.id ASC
    LIMIT 6
")->fetchAll();

// Fetch Stats for trust building
$totalTours = $db->query("SELECT COUNT(*) FROM packages WHERE status = 'active'")->fetchColumn();
$totalHappyTravelers = 1450 + (int)$db->query("SELECT COUNT(*) FROM bookings WHERE booking_status = 'confirmed'")->fetchColumn();
?>

<!-- Hero Section -->
<section class="hero-section text-center position-relative">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3 text-uppercase tracking-wider">
                    <i class="bi bi-geo-alt-fill me-1"></i> Negombo, Sri Lanka &bull; Since 2024
                </span>
                <h1 class="display-3 fw-bold mb-3 text-white">Discover the Wonder of Sri Lanka</h1>
                <p class="lead mb-4 text-white-50 mx-auto" style="max-width: 750px;">
                    Immerse yourself in sun-kissed Negombo beaches, ancient Sigiriya kingdoms, misty Ella tea valleys, and wild Yala leopard safaris. Handcrafted private and group journeys.
                </p>

                <!-- Search & Filter Card -->
                <div class="hero-search-card text-dark text-start">
                    <form action="<?= url('views/packages.php') ?>" method="GET" class="row g-3 align-items-end">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                <i class="bi bi-search text-primary me-1"></i> Destination or Activity
                            </label>
                            <input type="text" name="keyword" class="form-control" placeholder="e.g. Negombo, Sigiriya, Safari, Train...">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                <i class="bi bi-grid text-primary me-1"></i> Tour Category
                            </label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                <i class="bi bi-clock-history text-primary me-1"></i> Duration
                            </label>
                            <select name="duration" class="form-select">
                                <option value="">Any Duration</option>
                                <option value="1-2">1 - 2 Days (Weekend / Coastal)</option>
                                <option value="3-4">3 - 4 Days (Triangle & Mountains)</option>
                                <option value="5+">5+ Days (Grand Island Tour)</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="bi bi-compass me-1"></i> Find Tours
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Counters / Why Choose Us -->
<section class="py-5 bg-white border-bottom">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="fs-1 fw-bold text-primary mb-1"><?= $totalHappyTravelers ?>+</div>
                    <div class="text-muted fw-semibold small text-uppercase">Happy Travelers</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="fs-1 fw-bold text-primary mb-1">100%</div>
                    <div class="text-muted fw-semibold small text-uppercase">Authentic Local Guides</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="fs-1 fw-bold text-primary mb-1">24/7</div>
                    <div class="text-muted fw-semibold small text-uppercase">Negombo Support Desk</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="fs-1 fw-bold text-primary mb-1">4.9 / 5</div>
                    <div class="text-muted fw-semibold small text-uppercase">Customer Satisfaction</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Negombo Spotlight Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase px-3 py-2 rounded-pill mb-3">
                    Our Local Heritage
                </span>
                <h2 class="display-5 fw-bold mb-4">Negombo: The Gateway to Your Island Journey</h2>
                <p class="text-muted lead">
                    Known as the "Little Rome" of Sri Lanka and located just 15 minutes from Bandaranaike International Airport (CMB), Negombo is the ideal starting point for your Sri Lankan adventure.
                </p>
                <div class="row g-3 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-water text-primary fs-4"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Dutch Canal & Lagoon</h6>
                                <p class="small text-muted mb-0">Peaceful catamaran rides among mangrove ecosystems.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-sunset text-warning fs-4"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Golden Sunset Beaches</h6>
                                <p class="small text-muted mb-0">Lively beachfront with seaside seafood restaurants.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-basket text-success fs-4"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Negombo Fish Lellama</h6>
                                <p class="small text-muted mb-0">Vibrant open-air market with daily ocean catches.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-shield-check text-info fs-4"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Airport Chauffeurs</h6>
                                <p class="small text-muted mb-0">Private pickups and transfers directly to your hotel.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= url('views/custom_trip.php') ?>" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold me-2">
                        <i class="bi bi-sliders me-1"></i> Plan a Custom Negombo Trip
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1546708973-b339540b5162?auto=format&fit=crop&w=800&q=80" alt="Negombo Beach & Boats" class="img-fluid rounded-4 shadow-lg w-100" style="object-fit: cover; max-height: 480px;">
                    <div class="position-absolute bottom-0 start-0 bg-dark text-white p-3 m-3 rounded-3 bg-opacity-75 backdrop-blur">
                        <span class="small fw-semibold"><i class="bi bi-geo-alt-fill text-warning"></i> Negombo Beachfront & Fishing Lagoon</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Tour Packages -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <span class="badge bg-warning-subtle text-warning-emphasis fw-bold text-uppercase px-3 py-2 rounded-pill mb-2">
                    Top Rated Experiences
                </span>
                <h2 class="display-6 fw-bold mb-0">Popular Sri Lanka Tour Packages</h2>
            </div>
            <a href="<?= url('views/packages.php') ?>" class="btn btn-outline-primary rounded-pill fw-semibold mt-3 mt-md-0">
                View All Packages <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($featuredPackages as $pkg): 
                $imgUrl = 'https://images.unsplash.com/photo-1586861635167-e5223aadc9fe?auto=format&fit=crop&w=600&q=80';
                if (strpos($pkg['slug'], 'negombo') !== false) {
                    $imgUrl = 'https://images.unsplash.com/photo-1546708973-b339540b5162?auto=format&fit=crop&w=600&q=80';
                } elseif (strpos($pkg['slug'], 'sigiriya') !== false) {
                    $imgUrl = 'https://images.unsplash.com/photo-1588598198321-9735fd52455f?auto=format&fit=crop&w=600&q=80';
                } elseif (strpos($pkg['slug'], 'ella') !== false) {
                    $imgUrl = 'https://images.unsplash.com/photo-1578330138716-765c4e2b4d1a?auto=format&fit=crop&w=600&q=80';
                } elseif (strpos($pkg['slug'], 'yala') !== false) {
                    $imgUrl = 'https://images.unsplash.com/photo-1534177616072-ef7dc120449d?auto=format&fit=crop&w=600&q=80';
                } elseif (strpos($pkg['slug'], 'knuckles') !== false) {
                    $imgUrl = 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80';
                }
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 package-card">
                    <div class="package-card-img-wrapper">
                        <img src="<?= $imgUrl ?>" alt="<?= e($pkg['title']) ?>" class="package-card-img">
                        <span class="badge-destination">
                            <i class="bi bi-geo-alt-fill text-warning me-1"></i> <?= e($pkg['destination']) ?>
                        </span>
                        <?php if ($pkg['featured']): ?>
                            <span class="badge-featured">
                                <i class="bi bi-star-fill me-1"></i> Featured
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-light text-primary border small"><?= e($pkg['category_name']) ?></span>
                            <span class="small text-muted"><i class="bi bi-clock me-1"></i> <?= $pkg['duration_days'] ?> Days / <?= $pkg['duration_nights'] ?> Nights</span>
                        </div>
                        <h5 class="card-title fw-bold text-dark mb-2">
                            <a href="<?= url('views/package_detail.php?id=' . $pkg['id']) ?>" class="text-decoration-none text-dark hover-primary">
                                <?= e($pkg['title']) ?>
                            </a>
                        </h5>
                        <p class="card-text text-muted small flex-grow-1 mb-3">
                            <?= e(substr($pkg['description'], 0, 115)) ?>...
                        </p>
                        
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                            <div>
                                <span class="d-block small text-muted">From</span>
                                <span class="package-price-tag"><?= money($pkg['discount_price'] ?: $pkg['price']) ?></span>
                                <?php if ($pkg['discount_price']): ?>
                                    <span class="text-decoration-line-through text-muted small ms-1"><?= money($pkg['price']) ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= url('views/package_detail.php?id=' . $pkg['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-semibold">
                                View Details <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-5 bg-dark text-white text-center position-relative" style="background: linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.9)), url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1600&q=80') center/cover;">
    <div class="container py-4">
        <h2 class="display-5 fw-bold mb-3">Looking for a Unique, Custom Itinerary?</h2>
        <p class="lead text-white-50 mx-auto mb-4" style="max-width: 650px;">
            Let our local Negombo destination experts tailor the perfect Sri Lankan holiday suited to your preferred dates, budget, hotel standards, and travel pace.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= url('views/custom_trip.php') ?>" class="btn btn-warning btn-lg px-4 py-3 rounded-pill fw-bold text-dark">
                <i class="bi bi-pencil-square me-2"></i> Design Your Custom Trip
            </a>
            <a href="<?= url('views/contact.php') ?>" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill fw-semibold">
                <i class="bi bi-chat-dots me-2"></i> Contact Us
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
