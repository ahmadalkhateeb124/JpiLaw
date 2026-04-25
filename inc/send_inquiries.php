<?php
/**
 * Contact / inquiry form handler.
 * - Saves submission to `Inquiries` table (visible in the admin panel)
 * - Emails admin (recipient = site_settings.contact_email)
 * - Emails the user a confirmation
 * - Redirects back with ?sent=1 / ?error=...
 */

require_once __DIR__ . '/conn.php';
require_once __DIR__ . '/mail_handler.php';

function _redirect_back(string $params = ''): void
{
    $ref = $_SERVER['HTTP_REFERER'] ?? ($GLOBALS['base_url'] ?? '/') . 'contact';
    $sep = (strpos($ref, '?') === false) ? '?' : '&';
    header('Location: ' . $ref . $sep . $params);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    _redirect_back('error=method');
}

$sanitize = fn(?string $s) => htmlspecialchars(strip_tags(trim((string) $s)), ENT_QUOTES, 'UTF-8');

$name    = $sanitize($_POST['name']         ?? '');
$tel     = $sanitize($_POST['phone_number'] ?? $_POST['tel'] ?? '');
$email   = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_SANITIZE_EMAIL);
$subject = $sanitize($_POST['msg_subject']  ?? $_POST['subject'] ?? '');
$msg     = $sanitize($_POST['message']      ?? $_POST['msg'] ?? '');

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $msg === '') {
    _redirect_back('error=invalid');
}

// ── 1. Save to DB ────────────────────────────────────────────────────────────
try {
    $stmt = $conn->prepare(
        'INSERT INTO Inquiries (FullName, Mobile, Email, Subject, Msg) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('sssss', $name, $tel, $email, $subject, $msg);
    $stmt->execute();
    $stmt->close();
} catch (Throwable $e) {
    error_log('Inquiry insert failed: ' . $e->getMessage());
    _redirect_back('error=db');
}

// ── 2. Email admin ───────────────────────────────────────────────────────────
$adminEmail = jpi_admin_email($pdo);

$adminBody = '
<div style="font-family: Tajawal, Arial, sans-serif; max-width: 600px; margin: auto; direction: rtl;">
  <div style="background: #1a1a1a; color: #ebcfa7; padding: 24px; text-align: center;">
    <h2 style="margin: 0; font-size: 22px;">استفسار جديد من الموقع</h2>
    <p style="margin: 8px 0 0; opacity: .7; font-size: 13px;">JPI Law Firm</p>
  </div>
  <div style="padding: 24px; background: #fff; border: 1px solid #e8e8e8;">
    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
      <tr><td style="padding: 8px 0; color: #6c707a; width: 120px;">الاسم:</td>           <td style="padding: 8px 0; font-weight: bold;">' . $name . '</td></tr>
      <tr><td style="padding: 8px 0; color: #6c707a;">البريد الإلكتروني:</td>            <td style="padding: 8px 0;"><a href="mailto:' . $email . '">' . $email . '</a></td></tr>
      <tr><td style="padding: 8px 0; color: #6c707a;">الهاتف:</td>                       <td style="padding: 8px 0;">' . ($tel ?: '—') . '</td></tr>
      <tr><td style="padding: 8px 0; color: #6c707a;">الموضوع:</td>                      <td style="padding: 8px 0;">' . ($subject ?: '—') . '</td></tr>
    </table>
    <hr style="border: 0; border-top: 1px dashed #e8e8e8; margin: 16px 0;">
    <p style="color: #6c707a; font-size: 12px; margin-bottom: 6px;">الرسالة:</p>
    <div style="background: #f7f5f0; padding: 14px; border-radius: 8px; line-height: 1.7; white-space: pre-wrap;">' . nl2br($msg) . '</div>
  </div>
  <div style="text-align: center; padding: 16px; color: #9aa0aa; font-size: 11px;">
    تم الاستلام في ' . date('Y-m-d H:i') . '
  </div>
</div>';

send_jpi_mail($adminEmail, 'استفسار جديد: ' . ($subject ?: $name), $adminBody, $email);

// ── 3. Confirmation email to the user ────────────────────────────────────────
$userBody = '
<div style="font-family: Tajawal, Arial, sans-serif; max-width: 600px; margin: auto; direction: rtl;">
  <div style="background: #1a1a1a; color: #ebcfa7; padding: 28px; text-align: center;">
    <h2 style="margin: 0; font-size: 22px;">شكراً لتواصلك معنا</h2>
  </div>
  <div style="padding: 28px; background: #fff; border: 1px solid #e8e8e8; line-height: 1.8;">
    <p>مرحباً <strong>' . $name . '</strong>،</p>
    <p>وصلنا استفسارك بنجاح، وسيقوم أحد محامينا بالردّ عليك خلال 24 ساعة.</p>
    <p style="background: #f7f5f0; padding: 14px; border-radius: 8px; border-right: 4px solid #ebcfa7;">
      <strong>ملخّص رسالتك:</strong><br>' . nl2br(htmlspecialchars(mb_strimwidth($msg, 0, 300, '…'))) . '
    </p>
    <p>للاستفسار العاجل تواصل معنا على <a href="tel:+962796286204">+962 79 628 6204</a> أو واتساب.</p>
    <p style="margin-top: 24px;">
      تحياتنا،<br>
      <strong>فريق JPI للمحاماة</strong>
    </p>
  </div>
  <div style="text-align: center; padding: 16px; color: #9aa0aa; font-size: 11px;">
    JPI Law Firm — عمّان · رام الله
  </div>
</div>';

send_jpi_mail($email, 'تم استلام استفسارك — JPI Law Firm', $userBody);

_redirect_back('sent=1');
