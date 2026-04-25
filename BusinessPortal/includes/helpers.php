<?php
/**
 * Generic helpers — escaping, slugs, redirects, flash messages, uploads.
 */

declare(strict_types=1);

// ── Output ───────────────────────────────────────────────────────────────────

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function eAttr(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// ── Routing ──────────────────────────────────────────────────────────────────

function redirect(string $url): void
{
    // Strip .php from redirect targets so the browser's URL stays clean.
    $url = preg_replace('/\.php(\?|#|$)/', '$1', $url);
    header("Location: $url");
    exit;
}

function bp_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    // Strip .php extension for clean URLs (works with .htaccess rewrite).
    $path = preg_replace('/\.php(\?|$)/', '$1', $path);
    return BP_URL . $path;
}

/**
 * Current URL without query string — used for self-referencing links.
 * Always returns the clean (extensionless) URL the user typed.
 */
function current_url(): string
{
    return strtok($_SERVER['REQUEST_URI'] ?? '', '?');
}

function site_url(string $path = ''): string
{
    return SITE_URL . ltrim($path, '/');
}

function asset(string $path): string
{
    return BP_ASSETS . ltrim($path, '/');
}

// ── Flash messages ───────────────────────────────────────────────────────────

function flash(string $key, ?string $message = null)
{
    if ($message === null) {
        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
    $_SESSION['_flash'][$key] = $message;
}

// ── Slug ─────────────────────────────────────────────────────────────────────

function slugify(string $text, string $fallback = 'item'): string
{
    $text = trim($text);
    if ($text === '') return $fallback;

    // Keep Arabic letters & digits; replace everything else with '-'
    $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text);
    $text = preg_replace('/-+/', '-', $text ?? '');
    $text = trim((string) $text, '-');
    $text = mb_strtolower($text, 'UTF-8');

    return $text === '' ? $fallback : $text;
}

function uniqueSlug(PDO $pdo, string $table, string $slug, ?int $ignoreId = null): string
{
    $base = $slug;
    $i = 1;
    while (true) {
        $sql = "SELECT id FROM `$table` WHERE slug = ?";
        $params = [$slug];
        if ($ignoreId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $ignoreId;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $base . '-' . (++$i);
    }
}

// ── CSRF ─────────────────────────────────────────────────────────────────────

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="_csrf" value="' . eAttr(csrf_token()) . '">';
}

function csrf_check(): void
{
    $submitted = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals(csrf_token(), (string) $submitted)) {
        http_response_code(403);
        die('CSRF token mismatch.');
    }
}

// ── File uploads ─────────────────────────────────────────────────────────────

/**
 * Move an uploaded image into /uploads (shared with the public site).
 * Returns the public URL or null on failure.
 */
function upload_image(array $file, string $subdir = ''): ?string
{
    if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, $allowed, true)) {
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'bin');
    $name = uniqid('jpi_', true) . '.' . preg_replace('/[^a-z0-9]/', '', $ext);

    $dir = SITE_UPLOADS_DIR . ($subdir ? '/' . trim($subdir, '/') : '');
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    $dest = $dir . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }

    $rel = ($subdir ? trim($subdir, '/') . '/' : '') . $name;
    return SITE_UPLOADS . $rel;
}

// ── Activity log ─────────────────────────────────────────────────────────────

function log_activity(PDO $pdo, string $action, ?string $entityType = null, ?int $entityId = null, ?string $description = null): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO activity_log (admin_id, action, entity_type, entity_id, description, ip_address, user_agent)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $_SESSION['admin_id'] ?? null,
        $action,
        $entityType,
        $entityId,
        $description,
        $_SERVER['REMOTE_ADDR'] ?? null,
        substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
    ]);
}

// ── Pagination ───────────────────────────────────────────────────────────────

function paginate(int $total, int $perPage, int $page): array
{
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    return [
        'page'        => $page,
        'per_page'    => $perPage,
        'total'       => $total,
        'total_pages' => $totalPages,
        'offset'      => ($page - 1) * $perPage,
    ];
}

// ── Date format ──────────────────────────────────────────────────────────────

function fmt_date(?string $datetime, string $format = 'Y-m-d H:i'): string
{
    if (!$datetime) return '—';
    $ts = strtotime($datetime);
    return $ts ? date($format, $ts) : '—';
}
