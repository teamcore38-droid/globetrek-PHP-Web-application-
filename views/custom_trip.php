<?php
/**
 * Tailor-Made & Custom Trip Planning Form
 * GlobeTrek Adventures
 */
$pageTitle = 'Tailor-Made Tour Planner';
require_once __DIR__ . '/../includes/header.php';

$user = current_user();
?>

<!-- Banner -->
<div class="bg-dark text-white py-5 position-relative" style="background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.9)), url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1600&q=80') center/cover;">
    <div class="container py-3">
        <span class="badge bg-warning text-dark fw-bold text-uppercase px-3 py-2 rounded-pill mb-2">Bespoke Experience</span>
        <h1 class="display-4 fw-bold">Design Your Custom Sri Lanka Trip</h1>
        <p class="lead text-white-50 mb-0">Tell us where you want to go and what you love to do. Our Negombo travel consultants will design your personalized itinerary.</p>
    </div>
</div>

<div class="container py-5 my-3">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="card-body p-4 p-md-5">
                    <form action="<?= url('controllers/custom_trip_process.php') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="submit">

                        <!-- Section 1: Contact Information -->
                        <h5 class="fw-bold text-primary mb-3 d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-2 p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                            Your Contact Details
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Full Name *</label>
                                <input type="text" name="full_name" class="form-control" required value="<?= e($user['full_name'] ?? '') ?>" placeholder="Your Name">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Email Address *</label>
                                <input type="email" name="email" class="form-control" required value="<?= e($user['email'] ?? '') ?>" placeholder="name@domain.com">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Phone / WhatsApp *</label>
                                <input type="tel" name="phone" class="form-control" required value="<?= e($user['phone'] ?? '') ?>" placeholder="+94 77 123 4567">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section 2: Destinations of Interest -->
                        <h5 class="fw-bold text-primary mb-3 d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-2 p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                            Destinations You Want to Explore
                        </h5>

                        <div class="row g-2 mb-4">
                            <?php 
                            $dests = [
                                'Negombo (Canal & Beaches)', 'Sigiriya (Lion Rock)', 'Kandy (Temple of Tooth)', 
                                'Nuwara Eliya (Tea Hills)', 'Ella (Nine Arch Bridge)', 'Yala National Park (Safari)',
                                'Galle Fort & Unawatuna', 'Mirissa (Whale Watching)', 'Knuckles Rainforest', 
                                'Trincomalee / Pasikuda', 'Wilpattu National Park', 'Colombo City Tour'
                            ];
                            foreach ($dests as $d): ?>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check p-2 border rounded-3 bg-light">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="destinations[]" value="<?= $d ?>" id="dest_<?= md5($d) ?>">
                                        <label class="form-check-label small fw-semibold text-dark" for="dest_<?= md5($d) ?>">
                                            <?= $d ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr class="my-4">

                        <!-- Section 3: Dates & Party Size -->
                        <h5 class="fw-bold text-primary mb-3 d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-2 p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                            Travel Dates & Party Size
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label small fw-bold text-muted">Estimated Departure Date *</label>
                                <input type="date" name="departure_date" class="form-control" required min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d', strtotime('+14 days')) ?>">
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label small fw-bold text-muted">Tour Duration (Days) *</label>
                                <input type="number" name="duration_days" class="form-control" min="1" max="30" value="5" required>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label small fw-bold text-muted">Adults (12+ yrs)</label>
                                <input type="number" name="adults_count" class="form-control" min="1" max="25" value="2" required>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label small fw-bold text-muted">Children (Under 12 yrs)</label>
                                <input type="number" name="children_count" class="form-control" min="0" max="15" value="0">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section 4: Travel Style & Preferences -->
                        <h5 class="fw-bold text-primary mb-3 d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-2 p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">4</span>
                            Style, Comfort & Budget
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Accommodation Level</label>
                                <select name="accommodation_type" class="form-select">
                                    <option value="3-Star Hotel">3-Star Comfortable Hotels</option>
                                    <option value="4-Star Luxury">4-Star Deluxe Resorts & Spas</option>
                                    <option value="5-Star Resort / Villa">5-Star Luxury Resorts / Private Villas</option>
                                    <option value="Budget Guesthouse">Budget Eco-Lodges & Guesthouses</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Transportation Preference</label>
                                <select name="transport_type" class="form-select">
                                    <option value="Private Car (Sedan)">Private AC Car (Toyota Prius / Axio)</option>
                                    <option value="Luxury Mini-Van">Private Luxury Van (Toyota KDH HiAce)</option>
                                    <option value="SUV / 4WD">Luxury 4WD / SUV</option>
                                    <option value="Public Scenic Train + TukTuk">Highland Train & Tuk-Tuk Adventures</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Estimated Budget per Person (USD)</label>
                                <input type="number" name="estimated_budget_usd" class="form-control" placeholder="e.g. 500" step="50">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Special Interests or Notes</label>
                                <textarea name="special_interests" class="form-control" rows="3" placeholder="Tell us if you want wildlife safari drives, culinary classes, beach relaxation, hiking peaks, photography, or dietary requests..."></textarea>
                            </div>
                        </div>

                        <div class="text-center pt-2">
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold">
                                <i class="bi bi-send-check me-2"></i> Submit Custom Trip Request
                            </button>
                            <span class="d-block text-muted small mt-2">No upfront payment required for custom inquiries. We respond within 24 hours.</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
