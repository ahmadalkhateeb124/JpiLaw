<?php
date_default_timezone_set('Asia/Amman');
session_start();

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

