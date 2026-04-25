<?php
date_default_timezone_set('Asia/Amman');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$url = $_SERVER['HTTP_HOST'];
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

if (strpos($url, 'localhost') !== false || strpos($url, '127.0.0.1') !== false) {
    // Local development path
    $base_url = $protocol . '://' . $url . '/JpiLaw/';
    $base_path = $_SERVER['DOCUMENT_ROOT'] . '/JpiLaw/';
} else {

    $base_url = "https://jpilawfirm.com/";

    $base_path = $_SERVER['DOCUMENT_ROOT'];
}

// ── Database connection ──────────────────────────────────────────────────────
$_isLocal = strpos($url, 'localhost') !== false || strpos($url, '127.0.0.1') !== false;
if ($_isLocal) {
    $_db = ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'name' => 'jpilaw', 'port' => 3306];
} else {
    $_credFile = __DIR__ . '/db.credentials.php';
    $_db = is_file($_credFile)
        ? require $_credFile
        : ['host' => 'localhost', 'user' => '', 'pass' => '', 'name' => '', 'port' => 3306];
}

$conn = @new mysqli($_db['host'], $_db['user'], $_db['pass'], $_db['name'], $_db['port']);
if ($conn->connect_error) {
    error_log('JpiLaw DB connection failed: ' . $conn->connect_error);
} else {
    $conn->set_charset('utf8mb4');
}

// PDO instance for new code
try {
    $pdo = new PDO(
        "mysql:host={$_db['host']};port={$_db['port']};dbname={$_db['name']};charset=utf8mb4",
        $_db['user'], $_db['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (Throwable $e) {
    error_log('JpiLaw PDO failed: ' . $e->getMessage());
    $pdo = null;
}

function getCurrentURL()
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];

    // Combine the parts to form the complete URL
    $url = $protocol . "://" . $host . $uri;

    return $url;
}

// Usage example:
$currentURL = getCurrentURL();

