<?php
/**
 * Authentication & User Process Controller
 * GlobeTrek Adventures
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$db = Database::getConnection();

// --- 1. USER REGISTRATION ---
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Security token expired. Please try again.');
        redirect('views/register.php');
    }

    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validations
    if (empty($fullName) || empty($email) || empty($password)) {
        set_flash('danger', 'Please fill in all required fields.');
        redirect('views/register.php');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('danger', 'Please enter a valid email address.');
        redirect('views/register.php');
    }

    if (strlen($password) < 6) {
        set_flash('danger', 'Password must be at least 6 characters long.');
        redirect('views/register.php');
    }

    if ($password !== $confirmPassword) {
        set_flash('danger', 'Password confirmation does not match.');
        redirect('views/register.php');
    }

    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        set_flash('warning', 'An account with that email already exists. Please log in.');
        redirect('views/login.php');
    }

    // Create user (Role 3 = customer)
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $insertStmt = $db->prepare("
        INSERT INTO users (role_id, full_name, email, password, phone, status, created_at)
        VALUES (3, ?, ?, ?, ?, 'active', NOW())
    ");
    
    if ($insertStmt->execute([$fullName, $email, $passwordHash, $phone])) {
        $userId = $db->lastInsertId();
        
        // Auto-login newly registered user
        $_SESSION['user'] = [
            'id'        => (int)$userId,
            'role_id'   => 3,
            'role_name' => 'customer',
            'full_name' => $fullName,
            'email'     => $email,
            'phone'     => $phone,
        ];

        set_flash('success', "Welcome to GlobeTrek Adventures, $fullName! Your account is created.");
        redirect('views/customer/dashboard.php');
    } else {
        set_flash('danger', 'Failed to register account due to a system error. Please try again.');
        redirect('views/register.php');
    }
}

// --- 2. USER LOGIN ---
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Security token expired. Please try again.');
        redirect('views/login.php');
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        set_flash('danger', 'Please provide both email and password.');
        redirect('views/login.php');
    }

    $stmt = $db->prepare("
        SELECT u.id, u.role_id, u.full_name, u.email, u.password, u.phone, u.status, r.name as role_name
        FROM users u
        JOIN roles r ON u.role_id = r.id
        WHERE u.email = ?
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] !== 'active') {
            set_flash('danger', 'Your account has been deactivated. Please contact support.');
            redirect('views/login.php');
        }

        // Set session
        $_SESSION['user'] = [
            'id'        => (int)$user['id'],
            'role_id'   => (int)$user['role_id'],
            'role_name' => $user['role_name'],
            'full_name' => $user['full_name'],
            'email'     => $user['email'],
            'phone'     => $user['phone']
        ];

        set_flash('success', "Welcome back, " . htmlspecialchars($user['full_name']) . "!");

        // Role-based redirect
        if ($user['role_name'] === 'admin') {
            redirect('admin/dashboard.php');
        } elseif ($user['role_name'] === 'staff') {
            redirect('staff/dashboard.php');
        } else {
            // Customer
            $redirectTarget = $_POST['redirect_to'] ?? 'views/customer/dashboard.php';
            redirect($redirectTarget);
        }
    } else {
        set_flash('danger', 'Invalid email or password credentials. Please try again.');
        redirect('views/login.php');
    }
}

// --- 3. USER LOGOUT ---
if ($action === 'logout') {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    
    // Start fresh session to display flash message
    session_start();
    set_flash('info', 'You have been successfully signed out.');
    redirect('views/login.php');
}

// Default fallback
redirect('index.php');
