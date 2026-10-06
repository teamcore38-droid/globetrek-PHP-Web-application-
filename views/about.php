<?php
/**
 * About Us & Company Profile
 * GlobeTrek Adventures
 */
$pageTitle = 'About Us - Our Negombo Story';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Banner -->
<div class="bg-dark text-white py-5 position-relative" style="background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.85)), url('https://images.unsplash.com/photo-1546708973-b339540b5162?auto=format&fit=crop&w=1600&q=80') center/cover;">
    <div class="container py-4 text-center">
        <span class="badge bg-warning text-dark fw-bold text-uppercase px-3 py-2 rounded-pill mb-2">Company Profile</span>
        <h1 class="display-4 fw-bold">Welcome to GlobeTrek Adventures</h1>
        <p class="lead text-white-50 mx-auto mb-0" style="max-width: 750px;">
            Headquartered in Negombo, Sri Lanka — crafting immersive, secure, and authentic island voyages for world travelers since 2024.
        </p>
    </div>
</div>

<!-- Story Section -->
<div class="container py-5 my-3">
    <div class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
            <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase px-3 py-2 rounded-pill mb-3">Our Roots</span>
            <h2 class="display-6 fw-bold mb-4">Born by the Shores of Negombo</h2>
            <p class="text-muted">
                GlobeTrek Adventures was founded in Negombo to revolutionize the way travelers experience Sri Lanka. Situated right along the sun-drenched Indian Ocean and just 15 minutes from Bandaranaike International Airport (CMB), we provide travelers with seamless arrivals, personalized greetings, and immediate access to the wonders of Sri Lanka.
            </p>
            <p class="text-muted">
                Whether you wish to glide along the historic Dutch canals, conquer the rocky summit of Sigiriya Lion Rock, wander colonial tea estates in Nuwara Eliya, or witness elusive leopards in Yala National Park, our team ensures every mile is safe, memorable, and extraordinary.
            </p>
            <div class="row g-3 mt-2">
                <div class="col-sm-6">
                    <div class="border-start border-3 border-primary ps-3">
                        <h5 class="fw-bold mb-1">Our Mission</h5>
                        <p class="small text-muted mb-0">To deliver sustainable, culturally rich, and hassle-free travel experiences backed by transparent pricing.</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="border-start border-3 border-warning ps-3">
                        <h5 class="fw-bold mb-1">Our Vision</h5>
                        <p class="small text-muted mb-0">To be Sri Lanka's most trusted independent travel brand celebrated for bespoke local journeys.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row g-3">
                <div class="col-6">
                    <img src="https://images.unsplash.com/photo-1546708973-b339540b5162?auto=format&fit=crop&w=600&q=80" alt="Negombo Beach" class="img-fluid rounded-4 shadow">
                </div>
                <div class="col-6">
                    <img src="https://images.unsplash.com/photo-1588598198321-9735fd52455f?auto=format&fit=crop&w=600&q=80" alt="Sigiriya Fortress" class="img-fluid rounded-4 shadow mb-3">
                    <img src="https://images.unsplash.com/photo-1578330138716-765c4e2b4d1a?auto=format&fit=crop&w=600&q=80" alt="Ella Train" class="img-fluid rounded-4 shadow">
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <!-- Transportation & Fleet Showcase -->
    <div class="mb-5">
        <div class="text-center mb-5">
            <span class="badge bg-info-subtle text-info-emphasis fw-bold text-uppercase px-3 py-2 rounded-pill mb-2">Safe & Comfortable Transit</span>
            <h2 class="display-6 fw-bold">Our Modern Vehicle Fleet</h2>
            <p class="text-muted mx-auto" style="max-width: 650px;">
                All our vehicles are fully insured, climate-controlled, maintained to international tourist standards, and driven by English-speaking chauffeurs.
            </p>
        </div>

        <div class="row g-4 text-center">
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                    <div class="icon-circle bg-primary-subtle text-primary mx-auto mb-3">
                        <i class="bi bi-car-front-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Private Sedan Cars</h5>
                    <p class="small text-muted mb-0">Toyota Axio / Prius with AC, perfect for couples and solo travelers with luggage.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                    <div class="icon-circle bg-warning-subtle text-warning mx-auto mb-3">
                        <i class="bi bi-truck-front-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Luxury Passenger Vans</h5>
                    <p class="small text-muted mb-0">Toyota KDH HiAce with high roof, reclinable seats, and dual AC for family groups.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                    <div class="icon-circle bg-success-subtle text-success mx-auto mb-3">
                        <i class="bi bi-compass-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">4x4 Open Safari Jeeps</h5>
                    <p class="small text-muted mb-0">Customized Toyota Land Cruisers with elevated seating for Yala & Udawalawe safaris.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                    <div class="icon-circle bg-info-subtle text-info mx-auto mb-3">
                        <i class="bi bi-water"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Negombo Lagoon Boats</h5>
                    <p class="small text-muted mb-0">Licensed fiberglass safari boats and catamarans equipped with certified life jackets.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Accommodation Partners -->
    <div class="card border-0 shadow-sm rounded-4 p-5 bg-light mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-5">
                <span class="badge bg-warning text-dark fw-bold text-uppercase px-3 py-2 rounded-pill mb-2">Hospitality Standards</span>
                <h3 class="fw-bold mb-3">Premium Hotel & Eco-Lodge Partners</h3>
                <p class="text-muted small mb-0">
                    We partner with certified 3-Star, 4-Star, and 5-Star hospitality brands across Sri Lanka, including Heritance, Jetwing, Cinnamon, and boutique eco-villas, ensuring spotless sanitation, stunning views, and exquisite local dining.
                </p>
            </div>
            <div class="col-lg-7">
                <div class="row g-3 text-center">
                    <div class="col-4">
                        <div class="p-3 bg-white rounded-3 shadow-sm fw-bold text-dark">
                            <i class="bi bi-building text-primary fs-3 d-block mb-1"></i> Jetwing Hotels
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 bg-white rounded-3 shadow-sm fw-bold text-dark">
                            <i class="bi bi-tree text-success fs-3 d-block mb-1"></i> Heritance Resorts
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 bg-white rounded-3 shadow-sm fw-bold text-dark">
                            <i class="bi bi-shield-check text-warning fs-3 d-block mb-1"></i> Cinnamon Lodges
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
