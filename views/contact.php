<?php
/**
 * Contact Us & Customer Inquiry Desk
 * GlobeTrek Adventures
 */
$pageTitle = 'Contact & Inquiries';
require_once __DIR__ . '/../includes/header.php';

$user = current_user();
?>

<!-- Banner -->
<div class="bg-dark text-white py-5 position-relative" style="background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.9)), url('https://images.unsplash.com/photo-1546708973-b339540b5162?auto=format&fit=crop&w=1600&q=80') center/cover;">
    <div class="container py-3">
        <span class="badge bg-warning text-dark fw-bold text-uppercase px-3 py-2 rounded-pill mb-2">Customer Desk</span>
        <h1 class="display-4 fw-bold">Get In Touch With Us</h1>
        <p class="lead text-white-50 mb-0">Have questions about our tour packages, custom itineraries, or airport transfers? Our Negombo team is here to assist.</p>
    </div>
</div>

<div class="container py-5 my-3">
    <div class="row g-5">
        <!-- Inquiry Form -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white">
                <h3 class="fw-bold text-dark mb-2">Submit Your Travel Query</h3>
                <p class="text-muted small mb-4">Fill out the form below. Our travel consultants respond to all queries within 24 business hours.</p>

                <form action="<?= url('controllers/inquiry_process.php') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="submit">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label small fw-bold text-muted">Your Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required value="<?= e($user['full_name'] ?? '') ?>" placeholder="e.g. John Doe">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-bold text-muted">Your Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?= e($user['email'] ?? '') ?>" placeholder="name@domain.com">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label small fw-bold text-muted">Phone / WhatsApp</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= e($user['phone'] ?? '') ?>" placeholder="+94 77 123 4567">
                        </div>
                        <div class="col-md-6">
                            <label for="subject" class="form-label small fw-bold text-muted">Subject / Topic *</label>
                            <input type="text" class="form-control" id="subject" name="subject" required placeholder="e.g. Sigiriya Day Tour or Airport Pickup">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="message" class="form-label small fw-bold text-muted">Your Message or Question *</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Tell us about your travel dates, questions about hotel bookings, transport, or specific requests..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-send me-1"></i> Send Query to Administration
                    </button>
                </form>
            </div>
        </div>

        <!-- Headquarters Details & Map -->
        <div class="col-lg-5">
            <div class="d-flex flex-column gap-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill text-warning me-2"></i> Negombo Headquarters</h5>
                    <p class="text-muted small mb-3">
                        Visit our hospitality and operations lounge located directly on Lewis Place, right across the Negombo golden beach.
                    </p>
                    <ul class="list-unstyled small text-muted d-flex flex-column gap-3 mb-0">
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-pin-map-fill text-primary mt-1"></i>
                            <span>No. 48, Lewis Place, Negombo Beach Road, Negombo, Sri Lanka</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-telephone-fill text-primary"></i>
                            <span>Office: <?= COMPANY_PHONE ?></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-whatsapp text-success"></i>
                            <span>24/7 WhatsApp Hotline: <?= COMPANY_HOTLINE ?></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-envelope-fill text-primary"></i>
                            <span><?= COMPANY_EMAIL ?></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-fill text-primary"></i>
                            <span>Monday - Sunday: 8:00 AM - 9:00 PM (IST)</span>
                        </li>
                    </ul>
                </div>

                <!-- Google Map Embed of Negombo -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63339.42953285741!2d79.81525651586561!3d7.209637255146914!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2ee9c6bb2f73b%3A0xa51626e0b593f994!2sNegombo%2C%20Sri%20Lanka!5e0!3m2!1sen!2slk!4v1700000000000!5m2!1sen!2slk" 
                        width="100%" 
                        height="260" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
