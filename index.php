
<?php

ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);









$requestUrl = isset($_GET['url']) ? $_GET['url'] : '/';
$SP = isset($_GET['puid']) ? $_GET['puid'] : '';

// Include connection and login check for all pages except when explicitly handling differently



$path = 'pages/' . $requestUrl . '.php';



switch ($requestUrl) {
    case '/':
    case 'Home':
        include_once 'inc/conn.php';
        include_once 'parts/header.php';
        //   CheckUserLogIn($base_url);
        include_once 'pages/Home.php';
        include_once 'parts/footer.php';
        break;

    case 'B':
    case 'M':
        include_once 'inc/conn.php';
        include_once $path;
        break;





    default:
        if (file_exists($path)) {

            include_once 'inc/conn.php';
            include_once 'parts/header.php';
            //   CheckUserLogIn($base_url);

            include_once $path;
            include_once 'parts/footer.php';
        } else {
            include_once 'inc/conn.php';
            include_once 'parts/header.php';
            include_once 'pages/404.php';
            include_once 'parts/footer.php';
        }
        break;
}


// $conn->close();
// ob_end_flush();

