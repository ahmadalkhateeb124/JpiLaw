<?php
/**
 * BusinessPortal — global config & bootstrap.
 * Included at the top of every admin/auth file.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Amman');
mb_internal_encoding('UTF-8');

error_reporting(E_ALL);
ini_set('display_errors', '1');

// ── Paths ────────────────────────────────────────────────────────────────────
define('BP_ROOT',  realpath(__DIR__ . '/..'));
define('BP_CONFIG', BP_ROOT . '/config');
define('BP_INCLUDES', BP_ROOT . '/includes');
define('BP_PARTIALS', BP_ROOT . '/partials');
define('BP_UPLOADS_DIR', BP_ROOT . '/uploads');
define('SITE_ROOT', realpath(BP_ROOT . '/..'));
define('SITE_UPLOADS_DIR', SITE_ROOT . '/uploads');

// ── URLs ─────────────────────────────────────────────────────────────────────
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';

if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
    define('SITE_URL', $protocol . '://' . $host . '/JpiLaw/');
} else {
    define('SITE_URL', 'https://jpilawfirm.com/');
}
define('BP_URL',      SITE_URL . 'BusinessPortal/');
define('BP_ASSETS',   BP_URL   . 'assets/');
define('BP_UPLOADS',  BP_URL   . 'uploads/');
define('SITE_UPLOADS', SITE_URL . 'uploads/');

// ── App ──────────────────────────────────────────────────────────────────────
define('APP_NAME', 'JPI Law Admin');
define('APP_VERSION', '1.0.0');

// ── Bootstrap connection + helpers ───────────────────────────────────────────
require_once BP_CONFIG . '/database.php';
require_once BP_INCLUDES . '/lang.php';
require_once BP_INCLUDES . '/helpers.php';
