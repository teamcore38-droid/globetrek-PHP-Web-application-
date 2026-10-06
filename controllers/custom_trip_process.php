<?php
/**
 * Tailor-Made & Custom Trip Planning Controller
 * GlobeTrek Adventures
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$db = Database::getConnection();

if ($action === 'submit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Security token expired.');
        redirect('views/custom_trip.php');
    }

    $userId = current_user_id();
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $phone = trim($_POST['phone'] ?? '');
    $destinations = is_array($_POST['destinations'] ?? null) ? implode(', ', $_POST['destinations']) : trim($_POST['destinations'] ?? '');
    $departureDate = trim($_POST['departure_date'] ?? '');
    $durationDays = max(1, (int)($_POST['duration_days'] ?? 1));
    $adults = max(1, (int)($_POST['adults_count'] ?? 1));
    $children = max(0, (int)($_POST['children_count'] ?? 0));
    $accommodation = trim($_POST['accommodation_type'] ?? '3-Star Hotel');
    $transport = trim($_POST['transport_type'] ?? 'Private Car (Sedan)');
    $budget = !empty($_POST['estimated_budget_usd']) ? (float)$_POST['estimated_budget_usd'] : null;
    $specialInterests = trim($_POST['special_interests'] ?? '');

    if (empty($fullName) || empty($email) || empty($destinations) || empty($departureDate)) {
        set_flash('danger', 'Please provide your contact details, selected destinations, and travel date.');
        redirect('views/custom_trip.php');
    }

    $stmt = $db->prepare("
        INSERT INTO custom_trips (
            user_id, full_name, email, phone, destinations, departure_date,
            duration_days, adults_count, children_count, accommodation_type,
            transport_type, estimated_budget_usd, special_interests, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");

    if ($stmt->execute([
        $userId, $fullName, $email, $phone, $destinations, $departureDate,
        $durationDays, $adults, $children, $accommodation,
        $transport, $budget, $specialInterests
    ])) {
        set_flash('success', 'Your custom itinerary request has been submitted! Our Negombo travel consultants will design your personalized itinerary.');
        if (is_logged_in()) {
            redirect('views/customer/dashboard.php');
        } else {
            redirect('views/custom_trip.php');
        }
    } else {
        set_flash('danger', 'Unable to submit trip request. Please try again.');
        redirect('views/custom_trip.php');
    }
}

redirect('views/custom_trip.php');
