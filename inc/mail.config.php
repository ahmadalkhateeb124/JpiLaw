<?php
/**
 * SMTP credentials for outgoing mail.
 *
 * In production, copy this file to `mail.credentials.php` (gitignored) and
 * adjust values. The handler will prefer the credentials file when present,
 * then env vars, then fall back to the values here.
 */

return [
    'host'      => getenv('SMTP_HOST') ?: 'smtp.hostinger.com',
    'port'      => (int) (getenv('SMTP_PORT') ?: 587),
    'username'  => getenv('SMTP_USER') ?: 'inquiry@jpilawfirm.com',
    'password'  => getenv('SMTP_PASS') ?: '',
    'secure'    => getenv('SMTP_SECURE') ?: 'tls',
    'from_name' => 'JPI Law Firm',
];
