<?php
/**
 * Package Management Controller (Staff / Admin CRUD)
 * GlobeTrek Adventures
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_guard.php';

require_role(['staff', 'admin']);
$db = Database::getConnection();
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// --- SAVE (CREATE OR UPDATE) PACKAGE ---
if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Security verification failed.');
        redirect('staff/packages_manage.php');
    }

    $pkgId = (int)($_POST['id'] ?? 0);
    $categoryId = (int)($_POST['category_id'] ?? 1);
    $title = trim($_POST['title'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $durationDays = max(1, (int)($_POST['duration_days'] ?? 1));
    $durationNights = max(0, (int)($_POST['duration_nights'] ?? 0));
    $price = (float)($_POST['price'] ?? 0);
    $discountPrice = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    $maxTravelers = max(1, (int)($_POST['max_travelers'] ?? 12));
    $difficulty = $_POST['difficulty'] ?? 'Easy';
    $featured = isset($_POST['featured']) ? 1 : 0;
    $accommodation = trim($_POST['accommodation_info'] ?? '');
    $transport = trim($_POST['transport_info'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $itinerary = trim($_POST['itinerary'] ?? '');
    $inclusions = trim($_POST['inclusions'] ?? '');
    $exclusions = trim($_POST['exclusions'] ?? '');
    $status = $_POST['status'] ?? 'active';

    if (empty($title) || empty($destination) || $price <= 0 || empty($description)) {
        set_flash('danger', 'Please provide a valid package title, destination, price, and description.');
        redirect($pkgId > 0 ? "staff/package_edit.php?id=$pkgId" : "staff/package_edit.php");
    }

    // Slug generator
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    
    // Default image fallback or uploaded/input image
    $image = trim($_POST['image_url'] ?? '');
    if (empty($image)) {
        $image = 'package_negombo.jpg';
    }

    if ($pkgId > 0) {
        // Update
        $stmt = $db->prepare("
            UPDATE packages SET
                category_id = ?, title = ?, slug = ?, destination = ?, duration_days = ?, duration_nights = ?,
                price = ?, discount_price = ?, max_travelers = ?, difficulty = ?, featured = ?,
                image = ?, description = ?, itinerary = ?, inclusions = ?, exclusions = ?,
                accommodation_info = ?, transport_info = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([
            $categoryId, $title, $slug, $destination, $durationDays, $durationNights,
            $price, $discountPrice, $maxTravelers, $difficulty, $featured,
            $image, $description, $itinerary, $inclusions, $exclusions,
            $accommodation, $transport, $status, $pkgId
        ]);
        set_flash('success', "Tour package '$title' updated successfully.");
    } else {
        // Create
        // Ensure slug uniqueness
        $check = $db->prepare("SELECT id FROM packages WHERE slug = ?");
        $check->execute([$slug]);
        if ($check->fetch()) {
            $slug .= '-' . rand(100, 999);
        }

        $stmt = $db->prepare("
            INSERT INTO packages (
                category_id, title, slug, destination, duration_days, duration_nights,
                price, discount_price, max_travelers, difficulty, featured,
                image, description, itinerary, inclusions, exclusions,
                accommodation_info, transport_info, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $categoryId, $title, $slug, $destination, $durationDays, $durationNights,
            $price, $discountPrice, $maxTravelers, $difficulty, $featured,
            $image, $description, $itinerary, $inclusions, $exclusions,
            $accommodation, $transport, $status
        ]);
        set_flash('success', "New tour package '$title' created successfully!");
    }

    redirect('staff/packages_manage.php');
}

// --- DELETE / DEACTIVATE PACKAGE ---
if ($action === 'delete') {
    $pkgId = (int)($_GET['id'] ?? 0);
    // Check if bookings exist
    $checkBookings = $db->prepare("SELECT COUNT(*) FROM bookings WHERE package_id = ?");
    $checkBookings->execute([$pkgId]);
    if ($checkBookings->fetchColumn() > 0) {
        // Deactivate instead of hard delete to maintain foreign key integrity
        $stmt = $db->prepare("UPDATE packages SET status = 'inactive' WHERE id = ?");
        $stmt->execute([$pkgId]);
        set_flash('warning', 'Package has active historical bookings, so it was set to Inactive instead of permanently deleted.');
    } else {
        $stmt = $db->prepare("DELETE FROM packages WHERE id = ?");
        $stmt->execute([$pkgId]);
        set_flash('success', 'Package removed successfully.');
    }
    redirect('staff/packages_manage.php');
}

redirect('staff/packages_manage.php');
