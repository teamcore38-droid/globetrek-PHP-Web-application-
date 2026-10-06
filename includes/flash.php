<?php
/**
 * Render Flash Messages Component
 */
$flash = get_flash();
if ($flash): ?>
    <div class="container my-3">
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert">
            <?php if ($flash['type'] === 'success'): ?>
                <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
            <?php elseif ($flash['type'] === 'danger'): ?>
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-danger"></i>
            <?php elseif ($flash['type'] === 'warning'): ?>
                <i class="bi bi-exclamation-circle-fill me-2 fs-5 text-warning"></i>
            <?php else: ?>
                <i class="bi bi-info-circle-fill me-2 fs-5 text-info"></i>
            <?php endif; ?>
            <div class="flex-grow-1">
                <?= $flash['message'] ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>
