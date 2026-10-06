<?php
/**
 * Customer Inquiry & Query Controller
 * GlobeTrek Adventures
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_guard.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$db = Database::getConnection();

// --- 1. CUSTOMER SUBMIT QUERY ---
if ($action === 'submit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Security verification failed.');
        redirect('views/contact.php');
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $userId = current_user_id(); // null if guest

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        set_flash('danger', 'Please complete all required fields.');
        redirect('views/contact.php');
    }

    $stmt = $db->prepare("
        INSERT INTO inquiries (user_id, name, email, phone, subject, message, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    
    if ($stmt->execute([$userId, $name, $email, $phone, $subject, $message])) {
        set_flash('success', 'Thank you! Your travel inquiry has been sent to our Negombo team. We will respond promptly.');
    } else {
        set_flash('danger', 'Failed to send your inquiry. Please try again or reach our hotline.');
    }
    redirect('views/contact.php');
}

// --- 2. STAFF REPLY TO QUERY ---
if ($action === 'reply' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_role(['staff', 'admin']);

    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Security token failed.');
        redirect('staff/inquiries_manage.php');
    }

    $inquiryId = (int)($_POST['inquiry_id'] ?? 0);
    $reply = trim($_POST['reply'] ?? '');
    $staffId = current_user_id();

    if (empty($reply)) {
        set_flash('danger', 'Reply message cannot be empty.');
        redirect('staff/inquiries_manage.php');
    }

    $stmt = $db->prepare("
        UPDATE inquiries 
        SET reply = ?, replied_by = ?, status = 'replied', replied_at = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$reply, $staffId, $inquiryId]);

    set_flash('success', 'Reply recorded and sent to customer successfully.');
    redirect('staff/inquiries_manage.php');
}

redirect('index.php');
