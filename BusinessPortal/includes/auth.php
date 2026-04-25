<?php
/**
 * Admin authentication & route guards.
 *
 * Session keys:
 *   $_SESSION['admin_id']    int
 *   $_SESSION['admin_email'] string
 *   $_SESSION['admin_name']  string
 *   $_SESSION['admin_role']  'superadmin'|'admin'|'editor'
 */

declare(strict_types=1);

function isLoggedIn(): bool
{
    return !empty($_SESSION['admin_id']);
}

function currentAdmin(): ?array
{
    if (!isLoggedIn()) return null;
    return [
        'id'     => (int) $_SESSION['admin_id'],
        'email'  => $_SESSION['admin_email']  ?? '',
        'name'   => $_SESSION['admin_name']   ?? 'Admin',
        'role'   => $_SESSION['admin_role']   ?? 'admin',
        'avatar' => $_SESSION['admin_avatar'] ?? null,
    ];
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect(BP_URL . 'auth/login.php');
    }
}

function requireRole(string ...$roles): void
{
    requireLogin();
    if (!in_array($_SESSION['admin_role'] ?? '', $roles, true)) {
        http_response_code(403);
        die('Forbidden: insufficient permissions.');
    }
}

function authenticate(PDO $pdo, string $email, string $password): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if (!$admin) return null;
    if (!password_verify($password, $admin['password_hash'])) return null;

    $upd = $pdo->prepare('UPDATE admins SET last_login_at = NOW() WHERE id = ?');
    $upd->execute([$admin['id']]);

    return $admin;
}

function loginAdmin(array $admin): void
{
    session_regenerate_id(true);
    $_SESSION['admin_id']     = (int) $admin['id'];
    $_SESSION['admin_email']  = $admin['email'];
    $_SESSION['admin_name']   = $admin['full_name'];
    $_SESSION['admin_role']   = $admin['role'];
    $_SESSION['admin_avatar'] = $admin['avatar'];
}

function logoutAdmin(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
