<?php
/**
 * Role-Based Access Control Guards
 * GlobeTrek Adventures
 */

require_once __DIR__ . '/../config/constants.php';

function require_login(string $redirectPath = 'views/login.php'): void {
    if (!is_logged_in()) {
        set_flash('warning', 'Please sign in to access this page.');
        redirect($redirectPath);
    }
}

function require_role($roles, string $fallbackPath = ''): void {
    require_login();
    if (!has_role($roles)) {
        set_flash('danger', 'Unauthorized access! You do not have permission to view that resource.');
        redirect($fallbackPath ?: 'index.php');
    }
}

function require_guest(string $redirectPath = 'index.php'): void {
    if (is_logged_in()) {
        redirect($redirectPath);
    }
}
