<?php
/**
 * SMTP credentials for outgoing mail (production).
 *
 * Copy this file to `mail.credentials.php` (NOT committed) on the production
 * server and fill in the real password. `mail_handler.php` will auto-load it.
 */

return [
    'host'      => 'smtp.hostinger.com',
    'port'      => 587,
    'username'  => 'inquiry@jpilawfirm.com',
    'password'  => 'CHANGE_ME',
    'secure'    => 'tls',
    'from_name' => 'JPI Law Firm',
];
