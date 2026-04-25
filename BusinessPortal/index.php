<?php
require_once __DIR__ . '/config/config.php';
require_once BP_INCLUDES . '/auth.php';

if (isLoggedIn()) {
    redirect(BP_URL . 'admin/');
} else {
    redirect(BP_URL . 'auth/login.php');
}
