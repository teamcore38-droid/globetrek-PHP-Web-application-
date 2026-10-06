<?php
/**
 * Customer Inquiry Management & Reply Desk (Staff / Admin)
 * GlobeTrek Adventures
 */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_guard.php';

require_role(['staff', 'admin']);

$pageTitle = 'Customer Inquiries & Queries';
require_once __DIR__ . '/../includes/header.php';

$db = Database::getConnection();

// Fetch all inquiries
$inquiries = $db->query("
    SELECT i.*, u.full_name as replier_name
    FROM inquiries i
    LEFT JOIN users u ON i.replied_by = u.id
    ORDER BY i.status ASC, i.created_at DESC
")->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('staff/dashboard.php') ?>" class="text-decoration-none">Staff Desk</a></li>
                    <li class="breadcrumb-item active">Customer Queries</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0">Customer Inquiries & Assistance Desk</h3>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table custom-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date & Status</th>
                        <th>Sender Contact</th>
                        <th>Subject & Query Message</th>
                        <th>Staff Official Reply</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inquiries)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No customer inquiries found.</td>
                        </tr>
                    <?php else: foreach ($inquiries as $inq): ?>
                        <tr>
                            <td style="width: 150px;">
                                <div class="small text-muted mb-1"><?= date('M d, Y H:i', strtotime($inq['created_at'])) ?></div>
                                <?php if ($inq['status'] === 'replied'): ?>
                                    <span class="badge bg-success-subtle text-success fw-bold px-2 py-1"><i class="bi bi-check-circle me-1"></i> Replied</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger fw-bold px-2 py-1"><i class="bi bi-exclamation-circle me-1"></i> Awaiting Reply</span>
                                <?php endif; ?>
                            </td>
                            <td style="width: 200px;">
                                <div class="fw-bold text-dark"><?= e($inq['name']) ?></div>
                                <div class="small text-muted"><i class="bi bi-envelope me-1"></i> <?= e($inq['email']) ?></div>
                                <?php if ($inq['phone']): ?>
                                    <div class="small text-muted"><i class="bi bi-telephone me-1"></i> <?= e($inq['phone']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-1"><?= e($inq['subject']) ?></div>
                                <p class="small text-muted mb-0"><?= nl2br(e($inq['message'])) ?></p>
                            </td>
                            <td>
                                <?php if ($inq['reply']): ?>
                                    <div class="p-2 bg-light rounded small text-dark border-start border-3 border-success">
                                        <?= nl2br(e($inq['reply'])) ?>
                                        <div class="text-muted mt-1" style="font-size: 0.7rem;">
                                            By <?= e($inq['replier_name'] ?: 'Staff') ?> on <?= date('M d, Y H:i', strtotime($inq['replied_at'])) ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="small text-muted fst-italic">No reply recorded yet</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end" style="width: 110px;">
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#replyModal<?= $inq['id'] ?>">
                                    <i class="bi bi-reply-fill me-1"></i> Reply
                                </button>
                            </td>
                        </tr>

                        <!-- Reply Modal -->
                        <div class="modal fade" id="replyModal<?= $inq['id'] ?>" tabindex="-1" aria-labelledby="replyLabel<?= $inq['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header bg-light border-0">
                                        <h5 class="modal-title fw-bold" id="replyLabel<?= $inq['id'] ?>">
                                            Reply to: <?= e($inq['name']) ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="<?= url('controllers/inquiry_process.php') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="reply">
                                        <input type="hidden" name="inquiry_id" value="<?= $inq['id'] ?>">

                                        <div class="modal-body p-4">
                                            <div class="p-3 bg-light rounded-3 mb-3">
                                                <div class="small fw-bold text-dark mb-1"><?= e($inq['subject']) ?></div>
                                                <div class="small text-muted"><?= nl2br(e($inq['message'])) ?></div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">Your Response / Answer *</label>
                                                <textarea name="reply" class="form-control" rows="5" required placeholder="Type the official response here..."><?= e($inq['reply'] ?? '') ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 bg-light">
                                            <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill fw-semibold px-4">Save & Send Response</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
