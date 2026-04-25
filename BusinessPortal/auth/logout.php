<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';

if (isLoggedIn()) {
    log_activity($pdo, 'logout', 'admin', (int) $_SESSION['admin_id'], 'Admin signed out');
}
logoutAdmin();
redirect(BP_URL . 'auth/login.php');
