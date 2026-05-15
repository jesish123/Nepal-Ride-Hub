<?php
// includes/csrf.php — CSRF Protection Helpers

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate (or retrieve existing) CSRF token stored in session.
 */
function generateCSRF(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate a submitted CSRF token using timing-safe comparison.
 */
function validateCSRF(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Render a hidden CSRF input field (use inside forms).
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCSRF(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Abort with JSON error if CSRF is invalid (use in API endpoints).
 */
function requireValidCSRF(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCSRF($token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh the page and try again.']);
        exit;
    }
}
?>
