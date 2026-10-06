<?php
/**
 * Tour Packages Catalog & Search Engine
 * GlobeTrek Adventures
 */
$pageTitle = 'Explore Sri Lanka Tour Packages';
require_once __DIR__ . '/../includes/header.php';

$db = Database::getConnection();

// Query Parameters
$keyword = trim($_GET['keyword'] ?? '');
$selectedCategory = (int)($_GET['category'] ?? 0);
$selectedDuration = trim($_GET['duration'] ?? '');
$maxPrice = (float)($_GET['max_price'] ?? 0);

// Base SQL
$sql = "
    SELECT p.*, c.name as category_name 
    FROM packages p
    JOIN categories c ON p.category_id = c.id
    WHERE p.status = 'active'
";
$params = [];

if (!empty($keyword)) {
    $sql .= " AND (p.title LIKE ? OR p.destination LIKE ? OR p.description LIKE ?)";
    $like = '%' . $keyword . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($selectedCategory > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $selectedCategory;
}

if ($selectedDuration === '1-2') {
    $sql .= " AND p.duration_days BETWEEN 1 AND 2";
} elseif ($selectedDuration === '3-4') {
    $sql .= " AND p.duration_days BETWEEN 3 AND 4";
} elseif ($selectedDuration === '5+') {
    $sql .= " AND p.duration_days >= 5";
}

if ($maxPrice > 0) {
    $sql .= " AND (CASE WHEN p.discount_price > 0 THEN p.discount_price ELSE p.price END) <= ?";
    $params[] = $maxPrice;
}

$sql .= " ORDER BY p.featured DESC, p.id ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$packages = $stmt->fetchAll();

// Categories for filter list
$categories = $db->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
?>

<!-- Page Banner -->
<div class="bg-dark text-white py-5 position-relative" style="background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.9)), url('https://images.unsplash.com/photo-1546708973-b339540b5162?auto=format&fit=crop&w=1600&q=80') center/cover;">
    <div class="container py-3">
        <span class="badge bg-warning text-dark fw-bold text-uppercase px-3 py-2 rounded-pill mb-2">Curated Itineraries</span>
        <h1 class="display-4 fw-bold">Explore Sri Lanka Tours</h1>
        <p class="lead text-white-50 mb-0">From Negombo coastal lagoons to ancient Sigiriya and misty tea valleys.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-lg-top" style="top: 90px;">
                <h5 class="fw-bold mb-3 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-funnel text-primary me-2"></i> Filters</span>
                    <a href="<?= url('views/packages.php') ?>" class="small text-muted text-decoration-none">Reset</a>
                </h5>

                <form action="<?= url('views/packages.php') ?>" method="GET">
                    <!-- Keyword Search -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Keyword</label>
                        <input type="text" name="keyword" value="<?= e($keyword) ?>" class="form-control form-control-sm" placeholder="Search destination, title...">
                    </div>

                    <!-- Category Filter -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Category</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $selectedCategory === (int)$cat['id'] ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Duration Filter -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Duration</label>
                        <select name="duration" class="form-select form-select-sm">
                            <option value="">Any Length</option>
                            <option value="1-2" <?= $selectedDuration === '1-2' ? 'selected' : '' ?>>1 - 2 Days</option>
                            <option value="3-4" <?= $selectedDuration === '3-4' ? 'selected' : '' ?>>3 - 4 Days</option>
                            <option value="5+" <?= $selectedDuration === '5+' ? 'selected' : '' ?>>5+ Days</option>
                        </select>
                    </div>

                    <!-- Max Price Slider -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <label class="form-label small fw-bold text-muted">Max Budget (USD)</label>
                            <span class="small fw-bold text-primary" id="priceLabel">$<?= $maxPrice > 0 ? (int)$maxPrice : '700' ?></span>
                        </div>
                        <input type="range" class="form-range" name="max_price" min="100" max="1000" step="50" value="<?= $maxPrice > 0 ? $maxPrice : 700 ?>" id="priceRange" oninput="document.getElementById('priceLabel').innerText = '$' + this.value">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold btn-sm">
                        <i class="bi bi-filter me-1"></i> Apply Filters
                    </button>
                </form>

                <hr class="my-4">

                <!-- Instant Live Filter Input -->
                <div class="mb-2">
                    <label class="form-label small fw-bold text-muted">Instant In-Page Filter</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-lightning text-warning"></i></span>
                        <input type="text" id="packageSearchInput" class="form-control" placeholder="Quick keyword...">
                    </div>
                    <span class="d-block small text-muted mt-1" style="font-size: 0.72rem;">Filters displayed tours dynamically</span>
                </div>
            </div>
        </div>

        <!-- Packages Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="text-muted small">Showing <strong><?= count($packages) ?></strong> tour packages found</span>
                <a href="<?= url('views/custom_trip.php') ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                    <i class="bi bi-sliders me-1"></i> Need a Custom Itinerary?
                </a>
            </div>

            <!-- No Results Message -->
            <div id="noResultsMessage" class="alert alert-warning text-center p-5 rounded-4 shadow-sm" style="display: <?= empty($packages) ? 'block' : 'none' ?>;">
                <i class="bi bi-search fs-1 text-warning mb-3 d-block"></i>
                <h5 class="fw-bold">No tour packages found matching your criteria</h5>
                <p class="text-muted small mb-3">Try adjusting your filters or search keywords, or let us tailor a bespoke trip for you.</p>
                <a href="<?= url('views/packages.php') ?>" class="btn btn-sm btn-primary rounded-pill me-2">Clear All Filters</a>
                <a href="<?= url('views/custom_trip.php') ?>" class="btn btn-sm btn-outline-dark rounded-pill">Request Custom Tour</a>
            </div>

            <div class="row g-4" id="packagesContainer">
                <?php foreach ($packages as $pkg): 
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
                <div class="col-md-6 package-item-col" 
                     data-title="<?= e($pkg['title']) ?>" 
                     data-destination="<?= e($pkg['destination']) ?>" 
                     data-description="<?= e($pkg['description']) ?>">
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
                                <span class="small text-muted"><i class="bi bi-clock me-1"></i> <?= $pkg['duration_days'] ?>D / <?= $pkg['duration_nights'] ?>N</span>
                            </div>
                            <h5 class="card-title fw-bold text-dark mb-2">
                                <a href="<?= url('views/package_detail.php?id=' . $pkg['id']) ?>" class="text-decoration-none text-dark hover-primary">
                                    <?= e($pkg['title']) ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted small flex-grow-1 mb-3">
                                <?= e(substr($pkg['description'], 0, 110)) ?>...
                            </p>
                            
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                <div>
                                    <span class="d-block small text-muted">Per Person</span>
                                    <span class="package-price-tag"><?= money($pkg['discount_price'] ?: $pkg['price']) ?></span>
                                    <?php if ($pkg['discount_price']): ?>
                                        <span class="text-decoration-line-through text-muted small ms-1"><?= money($pkg['price']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= url('views/package_detail.php?id=' . $pkg['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-semibold">
                                    Explore & Book <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
