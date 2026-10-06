<?php
/**
 * Navigation Bar Component
 * GlobeTrek Adventures
 */
$currentUser = current_user();
$currentRole = current_user_role();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm py-2" id="mainNavbar">
    <div class="container">
        <!-- Brand Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url('index.php') ?>">
            <span class="brand-icon d-flex align-items-center justify-content-center bg-primary text-white rounded-circle p-2">
                <i class="bi bi-compass-fill fs-5"></i>
            </span>
            <div>
                <span class="fw-bold tracking-tight text-white fs-5">GlobeTrek</span>
                <span class="text-warning fw-semibold fs-5">Adventures</span>
                <span class="d-block text-secondary small" style="font-size: 0.68rem; margin-top: -3px;">NEGOMBO &bull; SRI LANKA</span>
            </div>
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?= url('index.php') ?>">
                        <i class="bi bi-house-door me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?= url('views/packages.php') ?>">
                        <i class="bi bi-geo-alt me-1"></i> Tour Packages
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?= url('views/custom_trip.php') ?>">
                        <i class="bi bi-sliders me-1"></i> Tailor-Made Trip
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?= url('views/about.php') ?>">
                        <i class="bi bi-building me-1"></i> About Us
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?= url('views/contact.php') ?>">
                        <i class="bi bi-envelope me-1"></i> Contact & Queries
                    </a>
                </li>
            </ul>

            <!-- Right Actions (Auth / User Portals) -->
            <div class="d-flex align-items-center gap-2">
                <?php if (!is_logged_in()): ?>
                    <a href="<?= url('views/login.php') ?>" class="btn btn-outline-light btn-sm px-3 rounded-pill">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                    </a>
                    <a href="<?= url('views/register.php') ?>" class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold">
                        <i class="bi bi-person-plus me-1"></i> Register
                    </a>
                <?php else: ?>
                    <!-- If Admin -->
                    <?php if ($currentRole === 'admin'): ?>
                        <a href="<?= url('admin/dashboard.php') ?>" class="btn btn-warning btn-sm rounded-pill fw-semibold text-dark me-1">
                            <i class="bi bi-speedometer2 me-1"></i> Admin Portal
                        </a>
                    <?php endif; ?>

                    <!-- If Staff -->
                    <?php if ($currentRole === 'staff' || $currentRole === 'admin'): ?>
                        <a href="<?= url('staff/dashboard.php') ?>" class="btn btn-info btn-sm rounded-pill fw-semibold text-dark me-1">
                            <i class="bi bi-briefcase me-1"></i> Staff Desk
                        </a>
                    <?php endif; ?>

                    <!-- User Account Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle rounded-pill d-flex align-items-center gap-2 px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-6"></i>
                            <span class="d-inline-block text-truncate" style="max-width: 140px;"><?= e($currentUser['full_name']) ?></span>
                            <span class="badge bg-primary text-uppercase" style="font-size: 0.65rem;"><?= e($currentUser['role_name']) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li class="px-3 py-2 border-bottom">
                                <p class="mb-0 fw-bold small text-dark"><?= e($currentUser['full_name']) ?></p>
                                <p class="mb-0 text-muted small text-truncate"><?= e($currentUser['email']) ?></p>
                            </li>
                            
                            <?php if ($currentRole === 'customer'): ?>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= url('views/customer/dashboard.php') ?>">
                                        <i class="bi bi-calendar2-check text-primary me-2"></i> My Bookings & Vouchers
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= url('views/custom_trip.php') ?>">
                                        <i class="bi bi-pencil-square text-success me-2"></i> Plan Custom Trip
                                    </a>
                                </li>
                            <?php elseif ($currentRole === 'staff'): ?>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= url('staff/dashboard.php') ?>">
                                        <i class="bi bi-grid text-primary me-2"></i> Staff Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= url('staff/packages_manage.php') ?>">
                                        <i class="bi bi-journal-text text-success me-2"></i> Manage Tour Packages
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= url('staff/bookings_manage.php') ?>">
                                        <i class="bi bi-bookmark-check text-info me-2"></i> Review Bookings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= url('staff/inquiries_manage.php') ?>">
                                        <i class="bi bi-chat-dots text-warning me-2"></i> Customer Queries
                                    </a>
                                </li>
                            <?php elseif ($currentRole === 'admin'): ?>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= url('admin/dashboard.php') ?>">
                                        <i class="bi bi-graph-up text-primary me-2"></i> Analytics Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= url('admin/users_manage.php') ?>">
                                        <i class="bi bi-people text-success me-2"></i> Manage Users & Staff
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= url('admin/reports.php') ?>">
                                        <i class="bi bi-file-earmark-bar-graph text-warning me-2"></i> Business Reports
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= url('staff/dashboard.php') ?>">
                                        <i class="bi bi-briefcase text-secondary me-2"></i> Staff Operations Desk
                                    </a>
                                </li>
                            <?php endif; ?>

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="<?= url('controllers/auth_process.php?action=logout') ?>">
                                    <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
