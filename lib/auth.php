<?php
// Authentication helpers

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate unique ID
 */
function generateId() {
    return uniqid('', true);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 */
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Guard - require login (redirect to login if not authenticated)
 */
function requireLogin($redirectUrl = '/login.php') {
    if (!isLoggedIn()) {
        header('Location: ' . $redirectUrl);
        exit();
    }
}

/**
 * Guard - require admin (redirect if not admin)
 */
function requireAdmin($redirectUrl = '/admin/index.php') {
    if (!isAdmin()) {
        header('Location: ' . $redirectUrl);
        exit();
    }
}

/**
 * Set user session after successful login
 */
function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['is_admin'] = false;
}

/**
 * Set admin session
 */
function setAdminSession($email) {
    $_SESSION['admin_email'] = $email;
    $_SESSION['is_admin'] = true;
}

/**
 * Destroy session (logout)
 */
function destroySession() {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

/**
 * Redirect helper
 */
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

/**
 * Sanitize input
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? 'Guest';
}
?>
