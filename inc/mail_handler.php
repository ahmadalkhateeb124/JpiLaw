<?php
/**
 * Unified outgoing-mail helper.
 *
 *   send_jpi_mail($to, $subject, $htmlBody, $replyTo = null): bool
 *
 * Reads SMTP credentials from `inc/mail.credentials.php` (gitignored) or
 * `inc/mail.config.php` (committed defaults). In local dev with no SMTP, it
 * silently logs the message instead of failing.
 */

declare(strict_types=1);

require_once __DIR__ . '/../PHPMail/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMail/src/SMTP.php';
require_once __DIR__ . '/../PHPMail/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

function _jpi_mail_config(): array
{
    $credFile   = __DIR__ . '/mail.credentials.php';
    $configFile = __DIR__ . '/mail.config.php';

    if (is_file($credFile))   return require $credFile;
    if (is_file($configFile)) return require $configFile;

    return [
        'host' => '', 'port' => 587, 'username' => '', 'password' => '',
        'secure' => 'tls', 'from_name' => 'JPI Law Firm',
    ];
}

/**
 * Send an HTML email. Returns true on success, false on failure.
 * If SMTP isn't configured (local dev), logs to /uploads/mail.log and returns true.
 */
function send_jpi_mail(string $to, string $subject, string $htmlBody, ?string $replyTo = null): bool
{
    $cfg = _jpi_mail_config();

    // Local dev / unconfigured — log instead of attempting SMTP
    if (empty($cfg['host']) || empty($cfg['password'])) {
        $logDir = __DIR__ . '/../uploads';
        if (!is_dir($logDir)) @mkdir($logDir, 0775, true);
        $line = sprintf(
            "[%s] TO=%s | SUBJECT=%s | REPLY=%s\n%s\n%s\n",
            date('Y-m-d H:i:s'), $to, $subject, $replyTo ?? '-',
            str_repeat('-', 60), $htmlBody
        );
        @file_put_contents($logDir . '/mail.log', $line . "\n", FILE_APPEND);
        return true;
    }

    $mail = new PHPMailer(true);
    try {
        $mail->CharSet  = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isSMTP();
        $mail->isHTML(true);
        $mail->Host       = $cfg['host'];
        $mail->Port       = (int) $cfg['port'];
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = $cfg['secure'];
        $mail->Username   = $cfg['username'];
        $mail->Password   = $cfg['password'];

        $mail->setFrom($cfg['username'], $cfg['from_name'] ?? 'JPI Law Firm');
        $mail->addAddress($to);
        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }

        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = trim(strip_tags($htmlBody));

        return $mail->send();

    } catch (PHPMailerException $e) {
        error_log('JPI Mail error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Read the admin recipient email from site_settings (fallback to default).
 */
function jpi_admin_email(PDO $pdo): string
{
    try {
        $stmt = $pdo->query("SELECT value_ar FROM site_settings WHERE `key` = 'contact_email' LIMIT 1");
        $val = $stmt ? (string) $stmt->fetchColumn() : '';
        if ($val !== '' && filter_var($val, FILTER_VALIDATE_EMAIL)) return $val;
    } catch (Throwable $e) { /* fall through */ }
    return 'info@jpilawfirm.com';
}
