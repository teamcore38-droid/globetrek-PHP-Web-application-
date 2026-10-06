<?php
/**
 * Customer Registration Page
 * GlobeTrek Adventures
 */
$pageTitle = 'Create Traveler Account';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth_guard.php';

if (is_logged_in()) {
    redirect('views/customer/dashboard.php');
}
?>

<div class="container py-5 my-4">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white text-center py-4 border-0" style="background: linear-gradient(135deg, #028090, #00606e) !important;">
                    <div class="brand-icon mx-auto rounded-circle p-2 d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-person-plus-fill fs-4 text-white"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Create Your Account</h4>
                    <p class="text-white-50 small mb-0">Join GlobeTrek Adventures & unlock customized travel planning</p>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="<?= url('controllers/auth_process.php') ?>" method="POST" id="registerForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="register">

                        <div class="mb-3">
                            <label for="fullName" class="form-label small fw-bold text-muted">Full Name *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="fullName" name="full_name" required placeholder="e.g. John Doe">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label for="email" class="form-label small fw-bold text-muted">Email Address *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required placeholder="john@example.com">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="phone" class="form-label small fw-bold text-muted">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="+94 77 123 4567">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label for="password" class="form-label small fw-bold text-muted">Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required minlength="6" placeholder="Min. 6 chars">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="confirm_password" class="form-label small fw-bold text-muted">Confirm Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-shield-check"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6" placeholder="Re-enter password">
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="termsAgree" required checked>
                            <label class="form-check-label small text-muted" for="termsAgree">
                                I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> & <a href="#" class="text-decoration-none">Privacy Policy</a> of GlobeTrek Adventures.
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">
                            <i class="bi bi-check2-circle me-1"></i> Complete Registration
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <span class="small text-muted">Already have a registered account?</span>
                        <a href="<?= url('views/login.php') ?>" class="small fw-bold text-primary text-decoration-none ms-1">
                            Sign In Here
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
