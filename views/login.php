<?php
/**
 * User Sign In Page
 * GlobeTrek Adventures
 */
$pageTitle = 'Sign In to Your Account';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth_guard.php';

// Redirect if already logged in
if (is_logged_in()) {
    $role = current_user_role();
    if ($role === 'admin') redirect('admin/dashboard.php');
    if ($role === 'staff') redirect('staff/dashboard.php');
    redirect('views/customer/dashboard.php');
}
?>

<div class="container py-5 my-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white text-center py-4 border-0" style="background: linear-gradient(135deg, #0f172a, #1e293b) !important;">
                    <div class="brand-icon mx-auto rounded-circle p-2 d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-person-lock fs-4 text-white"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Welcome Back</h4>
                    <p class="text-white-50 small mb-0">Sign in to manage your bookings and journeys</p>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="<?= url('controllers/auth_process.php') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="login">
                        <input type="hidden" name="redirect_to" value="<?= e($_GET['redirect'] ?? '') ?>">

                        <div class="mb-3">
                            <label for="loginEmail" class="form-label small fw-bold text-muted">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="loginEmail" name="email" required placeholder="name@example.com">
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="loginPassword" class="form-label small fw-bold text-muted">Password</label>
                                <span class="small text-muted">Default: Admin@123 / Staff@123 / Traveler@123</span>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control" id="loginPassword" name="password" required placeholder="••••••••">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                        </button>
                    </form>

                    <!-- Quick Demo Credential Autofill Buttons -->
                    <div class="mt-4 pt-3 border-top text-center">
                        <span class="d-block small text-muted mb-2 fw-semibold">Quick Demo Login (Click to populate):</span>
                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            <button type="button" class="btn btn-outline-warning btn-sm rounded-pill" onclick="fillLogin('admin@globetrek.com', 'Admin@123')">
                                <i class="bi bi-shield-lock me-1"></i> Admin
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm rounded-pill" onclick="fillLogin('staff@globetrek.com', 'Staff@123')">
                                <i class="bi bi-briefcase me-1"></i> Staff
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill" onclick="fillLogin('traveler@gmail.com', 'Traveler@123')">
                                <i class="bi bi-person me-1"></i> Traveler
                            </button>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <span class="small text-muted">Don't have an account yet?</span>
                        <a href="<?= url('views/register.php') ?>" class="small fw-bold text-primary text-decoration-none ms-1">
                            Create Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillLogin(email, password) {
    document.getElementById('loginEmail').value = email;
    document.getElementById('loginPassword').value = password;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
