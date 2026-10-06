<?php
/**
 * Global Footer Component
 * GlobeTrek Adventures
 */
?>
<footer class="bg-dark text-white pt-5 pb-3 mt-auto border-top border-secondary">
    <div class="container">
        <div class="row g-4 mb-4">
            <!-- Company Info & Negombo Presence -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-compass-fill fs-5"></i>
                    </span>
                    <span class="fw-bold fs-4 text-white">GlobeTrek <span class="text-warning">Adventures</span></span>
                </div>
                <p class="text-white-50 small mb-3">
                    Your trusted gateway to unforgettable Sri Lankan holidays. Based in the coastal haven of Negombo, we craft authentic wildlife safaris, ancient cultural journeys, and bespoke island adventures.
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="bi bi-whatsapp"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="bi bi-tripadvisor"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase fw-bold text-warning mb-3">Quick Links</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="<?= url('index.php') ?>" class="text-white-50 text-decoration-none hover-white"><i class="bi bi-chevron-right text-primary me-1"></i> Home</a></li>
                    <li><a href="<?= url('views/packages.php') ?>" class="text-white-50 text-decoration-none hover-white"><i class="bi bi-chevron-right text-primary me-1"></i> Tour Packages</a></li>
                    <li><a href="<?= url('views/custom_trip.php') ?>" class="text-white-50 text-decoration-none hover-white"><i class="bi bi-chevron-right text-primary me-1"></i> Tailor-Made Tours</a></li>
                    <li><a href="<?= url('views/about.php') ?>" class="text-white-50 text-decoration-none hover-white"><i class="bi bi-chevron-right text-primary me-1"></i> About Negombo</a></li>
                    <li><a href="<?= url('views/contact.php') ?>" class="text-white-50 text-decoration-none hover-white"><i class="bi bi-chevron-right text-primary me-1"></i> Inquiries & Support</a></li>
                </ul>
            </div>

            <!-- Sri Lanka Highlights -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-bold text-warning mb-3">Signature Journeys</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="<?= url('views/packages.php?category=1') ?>" class="text-white-50 text-decoration-none hover-white"><i class="bi bi-water text-info me-1"></i> Negombo Dutch Canal & Lagoon</a></li>
                    <li><a href="<?= url('views/packages.php?category=2') ?>" class="text-white-50 text-decoration-none hover-white"><i class="bi bi-bank text-info me-1"></i> Sigiriya Lion Rock Fortress</a></li>
                    <li><a href="<?= url('views/packages.php?category=3') ?>" class="text-white-50 text-decoration-none hover-white"><i class="bi bi-tree text-info me-1"></i> Ella Mountain Scenic Train</a></li>
                    <li><a href="<?= url('views/packages.php?category=4') ?>" class="text-white-50 text-decoration-none hover-white"><i class="bi bi-binoculars text-info me-1"></i> Yala Leopard Safari & Whales</a></li>
                    <li><a href="<?= url('views/packages.php?category=5') ?>" class="text-white-50 text-decoration-none hover-white"><i class="bi bi-compass text-info me-1"></i> Knuckles Cloud Rainforest</a></li>
                </ul>
            </div>

            <!-- Negombo Headquarters & Contact -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-bold text-warning mb-3">Headquarters</h6>
                <div class="small text-white-50 d-flex flex-column gap-2">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt-fill text-warning mt-1"></i>
                        <span>No. 48, Lewis Place, Negombo Beach Road, Negombo, Sri Lanka</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-telephone-fill text-warning"></i>
                        <span>+94 31 223 8890 (Office)</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-whatsapp text-success"></i>
                        <span>+94 77 123 4567 (24/7 Hotline)</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-envelope-fill text-warning"></i>
                        <span>info@globetrekadventures.com</span>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="row align-items-center small text-white-50">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                &copy; <?= date('Y') ?> <strong>GlobeTrek Adventures (Pvt) Ltd</strong>. All rights reserved.
            </div>
            <div class="col-md-6 text-center text-md-end">
                <span>Cardiff Met / ICBT - CSE5009 Web Application Development</span>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5.3.3 Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 for modern interactive dialogues -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom App Scripts -->
<script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
