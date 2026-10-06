<?php
/**
 * Application Constants & Helper Functions
 * GlobeTrek Adventures
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detect Base URL dynamically whether accessed via localhost/globetrek or PHP server
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

// Determine subfolder (e.g., /globetrek)
if (strpos($scriptName, '/globetrek') === 0) {
    define('BASE_URL', $protocol . $host . '/globetrek');
} else {
    // If run directly at root (like php -S localhost:8000)
    define('BASE_URL', $protocol . $host);
}

// Business Information
define('APP_NAME', 'GlobeTrek Adventures');
define('APP_TAGLINE', 'Authentic Sri Lankan Journeys & Beyond');
define('COMPANY_LOCATION', 'Negombo, Western Province, Sri Lanka');
define('COMPANY_PHONE', '+94 31 223 8890');
define('COMPANY_HOTLINE', '+94 77 123 4567');
define('COMPANY_EMAIL', 'info@globetrekadventures.com');
define('CURRENCY_SYMBOL', '$');
define('CURRENCY_CODE', 'USD');

/**
 * URL Generator helper
 */
function url(string $path = ''): string {
    $path = ltrim($path, '/');
    return BASE_URL . ($path ? '/' . $path : '');
}

/**
 * Sanitize output
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Currency formatter
 */
function money($amount): string {
    return CURRENCY_SYMBOL . number_format((float)$amount, 2);
}

/**
 * Authentication Helpers
 */
function is_logged_in(): bool {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function current_user_id(): ?int {
    return $_SESSION['user']['id'] ?? null;
}

function current_user_role(): string {
    return $_SESSION['user']['role_name'] ?? 'guest';
}

function has_role($roles): bool {
    if (!is_logged_in()) {
        return false;
    }
    $userRole = current_user_role();
    if (is_array($roles)) {
        return in_array($userRole, $roles, true);
    }
    return $userRole === $roles;
}

/**
 * CSRF Protection
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf(?string $token): bool {
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Flash Messaging Helpers
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type'    => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Redirect helper
 */
function redirect(string $path): void {
    header("Location: " . url($path));
    exit;
}
