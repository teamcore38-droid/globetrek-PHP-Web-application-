<?php
/**
 * User & Staff Account Management (Administrator Only)
 * GlobeTrek Adventures
 */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_guard.php';

require_role('admin');

$db = Database::getConnection();

// --- HANDLE POST REQUESTS (Create staff, toggle status, reset password) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Security verification failed.');
        redirect('admin/users_manage.php');
    }

    $action = $_POST['action'] ?? '';

    // 1. Create Staff / Admin user
    if ($action === 'create_user') {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleId = (int)($_POST['role_id'] ?? 2); // 2 = staff, 1 = admin

        if (empty($fullName) || empty($email) || empty($password)) {
            set_flash('danger', 'Please provide name, email, and password.');
            redirect('admin/users_manage.php');
        }

        // Check duplicate email
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            set_flash('danger', 'A user with that email already exists.');
            redirect('admin/users_manage.php');
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $insert = $db->prepare("
            INSERT INTO users (role_id, full_name, email, password, phone, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'active', NOW())
        ");
        $insert->execute([$roleId, $fullName, $email, $hash, $phone]);

        set_flash('success', "New staff account for '$fullName' created successfully!");
        redirect('admin/users_manage.php');
    }

    // 2. Toggle Status (Active / Inactive)
    if ($action === 'toggle_status') {
        $targetId = (int)($_POST['user_id'] ?? 0);
        if ($targetId === current_user_id()) {
            set_flash('danger', 'You cannot deactivate your own administrative account.');
            redirect('admin/users_manage.php');
        }

        $currentStatus = $_POST['current_status'] ?? 'active';
        $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

        $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $targetId]);

        set_flash('info', "User #$targetId status changed to '$newStatus'.");
        redirect('admin/users_manage.php');
    }

    // 3. Reset Password
    if ($action === 'reset_password') {
        $targetId = (int)($_POST['user_id'] ?? 0);
        $newPass = $_POST['new_password'] ?? '';
        if (strlen($newPass) < 6) {
            set_flash('danger', 'New password must be at least 6 characters.');
            redirect('admin/users_manage.php');
        }

        $hash = password_hash($newPass, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $targetId]);

        set_flash('success', "Password for user #$targetId has been reset successfully.");
        redirect('admin/users_manage.php');
    }
}

// Fetch all users with role names
$roleFilter = trim($_GET['role'] ?? 'all');
$sql = "
    SELECT u.*, r.name as role_name
    FROM users u
    JOIN roles r ON u.role_id = r.id
";
$params = [];
if ($roleFilter !== 'all' && in_array($roleFilter, ['admin', 'staff', 'customer'])) {
    $sql .= " WHERE r.name = ?";
    $params[] = $roleFilter;
}
$sql .= " ORDER BY u.id ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

$pageTitle = 'Manage Staff & User Accounts';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('admin/dashboard.php') ?>" class="text-decoration-none">Admin Panel</a></li>
                    <li class="breadcrumb-item active">Users & Staff</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0">System Users & Staff Directory</h3>
        </div>
        <div class="d-flex gap-2">
            <!-- Role Filters -->
            <div class="btn-group btn-group-sm rounded-pill p-1 bg-white shadow-sm border">
                <a href="<?= url('admin/users_manage.php?role=all') ?>" class="btn rounded-pill <?= $roleFilter === 'all' ? 'btn-primary' : 'btn-light' ?>">All</a>
                <a href="<?= url('admin/users_manage.php?role=staff') ?>" class="btn rounded-pill <?= $roleFilter === 'staff' ? 'btn-primary' : 'btn-light' ?>">Staff</a>
                <a href="<?= url('admin/users_manage.php?role=customer') ?>" class="btn rounded-pill <?= $roleFilter === 'customer' ? 'btn-primary' : 'btn-light' ?>">Customers</a>
                <a href="<?= url('admin/users_manage.php?role=admin') ?>" class="btn rounded-pill <?= $roleFilter === 'admin' ? 'btn-primary' : 'btn-light' ?>">Admins</a>
            </div>
            <!-- Add Staff Button -->
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#createStaffModal">
                <i class="bi bi-person-plus me-1"></i> Add Staff Account
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table custom-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>User Profile</th>
                        <th>Role</th>
                        <th>Contact Phone</th>
                        <th>Account Status</th>
                        <th>Registered Date</th>
                        <th class="text-end">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="text-muted fw-bold">#<?= $u['id'] ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= e($u['full_name']) ?></div>
                                <span class="small text-muted"><i class="bi bi-envelope me-1"></i><?= e($u['email']) ?></span>
                            </td>
                            <td>
                                <?php if ($u['role_name'] === 'admin'): ?>
                                    <span class="badge bg-danger-subtle text-danger fw-bold px-2 py-1"><i class="bi bi-shield-lock me-1"></i> Administrator</span>
                                <?php elseif ($u['role_name'] === 'staff'): ?>
                                    <span class="badge bg-info-subtle text-info fw-bold px-2 py-1"><i class="bi bi-briefcase me-1"></i> Agency Staff</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1"><i class="bi bi-person me-1"></i> Traveler</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-dark"><?= e($u['phone'] ?: 'N/A') ?></td>
                            <td>
                                <?php if ($u['status'] === 'active'): ?>
                                    <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger fw-bold px-2 py-1">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <!-- Toggle Status Form -->
                                    <?php if ($u['id'] !== current_user_id()): ?>
                                        <form action="<?= url('admin/users_manage.php') ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="current_status" value="<?= $u['status'] ?>">
                                            <button type="submit" class="btn btn-outline-<?= $u['status'] === 'active' ? 'warning' : 'success' ?>" title="<?= $u['status'] === 'active' ? 'Deactivate' : 'Activate' ?>">
                                                <i class="bi bi-<?= $u['status'] === 'active' ? 'slash-circle' : 'check-circle' ?>"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Reset Password Modal Trigger -->
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#resetModal<?= $u['id'] ?>" title="Reset Password">
                                        <i class="bi bi-key"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Reset Password Modal -->
                        <div class="modal fade" id="resetModal<?= $u['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header bg-light border-0">
                                        <h5 class="modal-title fw-bold">Reset Password for <?= e($u['full_name']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="<?= url('admin/users_manage.php') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="reset_password">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">

                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">New Password *</label>
                                                <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="Enter new password (min. 6 chars)">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 bg-light">
                                            <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill fw-semibold px-4">Update Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Create New Staff Account -->
<div class="modal fade" id="createStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-badge text-primary me-2"></i> Create Agency Staff Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url('admin/users_manage.php') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create_user">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" required placeholder="e.g. Ruwan Silva">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Email Address *</label>
                        <input type="email" name="email" class="form-control" required placeholder="ruwan@globetrek.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Initial Password *</label>
                        <input type="password" name="password" class="form-control" required minlength="6" placeholder="Min. 6 chars (e.g. Staff@123)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Contact Phone</label>
                        <input type="tel" name="phone" class="form-control" placeholder="+94 77 000 0000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Role Assignment</label>
                        <select name="role_id" class="form-select">
                            <option value="2" selected>Agency Staff (Manage packages & bookings)</option>
                            <option value="1">System Administrator (Full access)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill fw-semibold px-4">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
