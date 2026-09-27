<?php
/**
 * Helper functions
 * Dilshan Stores - Automatic Bill Calculation System
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Clean a piece of user input for safe display / storage. */
function sanitize(string $data): string
{
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/** Is a user currently logged in? */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

/** Redirect to login if the visitor is not authenticated. */
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . base_url('auth/login.php'));
        exit;
    }
}

/** Basic server-side email validation (mirrors the client-side JS check). */
function is_valid_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Build a root-relative URL that works whether the page lives at
 * project root ("index.php") or inside a subfolder ("auth/login.php").
 */
function base_url(string $path = ''): string
{
    // Figure out the folder the app is installed in on the web server,
    // e.g. "/dilshan-stores/" when running under http://localhost/dilshan-stores/
    static $root;
    if ($root === null) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
        // Strip a trailing "auth/xxx.php" or "xxx.php" to find the project root
        $root = preg_replace('#(auth/)?[^/]+\.php$#', '', $script);
    }
    return $root . ltrim($path, '/');
}

/** Flash a one-time message via session and read it back (then clear it). */
function set_flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function get_flash(string $key): ?string
{
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}
