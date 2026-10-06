<?php
/**
 * Package Management (Staff / Admin)
 * GlobeTrek Adventures
 */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_guard.php';

require_role(['staff', 'admin']);

$pageTitle = 'Manage Tour Packages';
require_once __DIR__ . '/../includes/header.php';

$db = Database::getConnection();

// Fetch all packages including inactive
$packages = $db->query("
    SELECT p.*, c.name as category_name
    FROM packages p
    JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
")->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('staff/dashboard.php') ?>" class="text-decoration-none">Staff Desk</a></li>
                    <li class="breadcrumb-item active">Tour Packages</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0">Tour Packages Management</h3>
        </div>
        <a href="<?= url('staff/package_edit.php') ?>" class="btn btn-primary rounded-pill px-4 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i> Create New Tour Package
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table custom-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Tour Details</th>
                        <th>Category</th>
                        <th>Destination</th>
                        <th>Duration</th>
                        <th>Pricing</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($packages)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No tour packages found.</td>
                        </tr>
                    <?php else: foreach ($packages as $pkg): ?>
                        <tr>
                            <td class="text-muted fw-bold">#<?= $pkg['id'] ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= e($pkg['title']) ?></div>
                                <span class="small text-muted">Max: <?= $pkg['max_travelers'] ?> persons &bull; <?= e($pkg['difficulty']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border"><?= e($pkg['category_name']) ?></span>
                            </td>
                            <td>
                                <span class="small fw-semibold text-dark"><i class="bi bi-geo-alt me-1 text-danger"></i> <?= e($pkg['destination']) ?></span>
                            </td>
                            <td>
                                <span class="small text-muted"><?= $pkg['duration_days'] ?>D / <?= $pkg['duration_nights'] ?>N</span>
                            </td>
                            <td>
                                <div class="fw-bold text-primary"><?= money($pkg['discount_price'] ?: $pkg['price']) ?></div>
                                <?php if ($pkg['discount_price']): ?>
                                    <span class="small text-decoration-line-through text-muted"><?= money($pkg['price']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($pkg['status'] === 'active'): ?>
                                    <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('views/package_detail.php?id=' . $pkg['id']) ?>" target="_blank" class="btn btn-outline-secondary" title="Preview Public Page">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= url('staff/package_edit.php?id=' . $pkg['id']) ?>" class="btn btn-outline-primary" title="Edit Package">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?= url('controllers/package_process.php?action=delete&id=' . $pkg['id']) ?>" 
                                       class="btn btn-outline-danger confirm-action" 
                                       data-confirm-message="Are you sure you want to deactivate/delete package: <?= e($pkg['title']) ?>?" 
                                       title="Delete / Deactivate">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
