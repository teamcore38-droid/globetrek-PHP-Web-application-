<?php
/**
 * Create / Edit Tour Package Form
 * GlobeTrek Adventures
 */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_guard.php';

require_role(['staff', 'admin']);

$db = Database::getConnection();
$pkgId = (int)($_GET['id'] ?? 0);
$package = null;

if ($pkgId > 0) {
    $stmt = $db->prepare("SELECT * FROM packages WHERE id = ? LIMIT 1");
    $stmt->execute([$pkgId]);
    $package = $stmt->fetch();
    if (!$package) {
        set_flash('danger', 'Tour package not found.');
        redirect('staff/packages_manage.php');
    }
}

$pageTitle = $pkgId > 0 ? 'Edit Tour: ' . $package['title'] : 'Create New Tour Package';
require_once __DIR__ . '/../includes/header.php';

$categories = $db->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="<?= url('staff/packages_manage.php') ?>" class="text-decoration-none">Tour Packages</a></li>
                            <li class="breadcrumb-item active"><?= $pkgId > 0 ? 'Edit Package' : 'New Package' ?></li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold text-dark mb-0"><?= $pkgId > 0 ? 'Edit Tour Package' : 'Add New Tour Package' ?></h3>
                </div>
                <a href="<?= url('staff/packages_manage.php') ?>" class="btn btn-outline-secondary rounded-pill btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="card-body p-4 p-md-5">
                    <form action="<?= url('controllers/package_process.php') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?= $pkgId ?>">

                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label for="title" class="form-label small fw-bold text-muted">Tour Package Title *</label>
                                <input type="text" class="form-control" id="title" name="title" required value="<?= e($package['title'] ?? '') ?>" placeholder="e.g. Negombo Coastal & Mangrove Boat Safari">
                            </div>
                            <div class="col-md-4">
                                <label for="category_id" class="form-label small fw-bold text-muted">Category *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= ($package && $package['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                            <?= e($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="destination" class="form-label small fw-bold text-muted">Destinations Covered *</label>
                                <input type="text" class="form-control" id="destination" name="destination" required value="<?= e($package['destination'] ?? '') ?>" placeholder="e.g. Negombo, Sigiriya, Kandy">
                            </div>
                            <div class="col-md-3 col-6">
                                <label for="duration_days" class="form-label small fw-bold text-muted">Duration (Days) *</label>
                                <input type="number" class="form-control" id="duration_days" name="duration_days" min="1" max="30" required value="<?= $package['duration_days'] ?? 2 ?>">
                            </div>
                            <div class="col-md-3 col-6">
                                <label for="duration_nights" class="form-label small fw-bold text-muted">Duration (Nights)</label>
                                <input type="number" class="form-control" id="duration_nights" name="duration_nights" min="0" max="30" required value="<?= $package['duration_nights'] ?? 1 ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3 col-6">
                                <label for="price" class="form-label small fw-bold text-muted">Regular Price (USD) *</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required value="<?= $package['price'] ?? '' ?>" placeholder="199.00">
                            </div>
                            <div class="col-md-3 col-6">
                                <label for="discount_price" class="form-label small fw-bold text-muted">Discount Price (USD)</label>
                                <input type="number" step="0.01" class="form-control" id="discount_price" name="discount_price" value="<?= $package['discount_price'] ?? '' ?>" placeholder="Optional promo price">
                            </div>
                            <div class="col-md-3 col-6">
                                <label for="max_travelers" class="form-label small fw-bold text-muted">Max Travelers</label>
                                <input type="number" class="form-control" id="max_travelers" name="max_travelers" min="1" max="50" value="<?= $package['max_travelers'] ?? 12 ?>">
                            </div>
                            <div class="col-md-3 col-6">
                                <label for="difficulty" class="form-label small fw-bold text-muted">Difficulty</label>
                                <select class="form-select" id="difficulty" name="difficulty">
                                    <option value="Easy" <?= ($package && $package['difficulty'] === 'Easy') ? 'selected' : '' ?>>Easy</option>
                                    <option value="Moderate" <?= ($package && $package['difficulty'] === 'Moderate') ? 'selected' : '' ?>>Moderate</option>
                                    <option value="Challenging" <?= ($package && $package['difficulty'] === 'Challenging') ? 'selected' : '' ?>>Challenging</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="accommodation_info" class="form-label small fw-bold text-muted">Accommodation Details *</label>
                                <input type="text" class="form-control" id="accommodation_info" name="accommodation_info" required value="<?= e($package['accommodation_info'] ?? '4-Star Beach Resort / Boutique Eco Lodge') ?>" placeholder="e.g. Jetwing Blue Negombo Deluxe Room">
                            </div>
                            <div class="col-md-6">
                                <label for="transport_info" class="form-label small fw-bold text-muted">Transportation Details *</label>
                                <input type="text" class="form-control" id="transport_info" name="transport_info" required value="<?= e($package['transport_info'] ?? 'Air-conditioned luxury private vehicle with chauffeur') ?>" placeholder="e.g. Private Luxury AC Van & Lagoon Boat">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="image_url" class="form-label small fw-bold text-muted">Package Image File / URL</label>
                            <input type="text" class="form-control" id="image_url" name="image_url" value="<?= e($package['image'] ?? 'package_negombo.jpg') ?>" placeholder="e.g. package_negombo.jpg or https://...">
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label small fw-bold text-muted">Tour Overview Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Comprehensive highlight of this tour..."><?= e($package['description'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="itinerary" class="form-label small fw-bold text-muted">Day-by-Day Itinerary * (Line break per day)</label>
                            <textarea class="form-control font-monospace small" id="itinerary" name="itinerary" rows="5" required placeholder="Day 1: Arrival, Lagoon boat cruise, seafood dinner...&#10;Day 2: Morning Fish market and catamarans..."><?= e($package['itinerary'] ?? '') ?></textarea>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="inclusions" class="form-label small fw-bold text-muted">Inclusions (What's Included - 1 per line)</label>
                                <textarea class="form-control small" id="inclusions" name="inclusions" rows="4" placeholder="AC Transport&#10;Guide fees&#10;Breakfast & Hotel"><?= e($package['inclusions'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="exclusions" class="form-label small fw-bold text-muted">Exclusions (Not Included - 1 per line)</label>
                                <textarea class="form-control small" id="exclusions" name="exclusions" rows="4" placeholder="International flights&#10;Alcoholic beverages&#10;Tips"><?= e($package['exclusions'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="row g-3 mb-4 align-items-center">
                            <div class="col-md-6">
                                <label for="status" class="form-label small fw-bold text-muted">Publish Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?= ($package && $package['status'] === 'active') ? 'selected' : '' ?>>Active (Publicly Visible)</option>
                                    <option value="inactive" <?= ($package && $package['status'] === 'inactive') ? 'selected' : '' ?>>Inactive (Hidden)</option>
                                    <option value="draft" <?= ($package && $package['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
                                </select>
                            </div>
                            <div class="col-md-6 pt-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="featured" name="featured" <?= ($package && $package['featured']) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold text-dark small" for="featured">
                                        Feature on Homepage (Top recommendations)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= url('staff/packages_manage.php') ?>" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">
                                <i class="bi bi-check2-circle me-1"></i> Save Tour Package
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
