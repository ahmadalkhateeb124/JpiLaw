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

    // $conn = mysqli_connect("localhost", "u366180362_k14ncom", "KayanTechCo@2002#", "u366180362_KayanTechCo");
    $base_url = "https://jpilawfirm.com/";

    $base_path = $_SERVER['DOCUMENT_ROOT'];
}

// if (!$conn) {
//     die("Connection failed: " . mysqli_connect_error());
// }


// $web_info = [];
// $sel_info = "select * from SystemInfo where Lang='$lang'";
// $res = mysqli_query($conn, $sel_info);
// while ($data = mysqli_fetch_assoc($res)) {
//     $web_info[] = $data;
// }


// $currentPageURL = $_SERVER['REQUEST_URI'];
// $currentPageName = basename($currentPageURL, ".php");
// echo "Opened page name: " . $currentPageName;


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


// include_once($base_path . 'inc/UpdateVisitCount.php');
// include_once($base_path . 'inc/UpdateVisitCount.php');
// include_once($base_path . 'inc/get_info.php');
