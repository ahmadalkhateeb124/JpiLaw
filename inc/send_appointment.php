<?php
/**
 * Appointment booking handler.
 * - Saves to `appointments` table (visible in admin → الحجوزات)
 * - Emails admin
 * - Sends confirmation to user
 */

require_once __DIR__ . '/conn.php';
require_once __DIR__ . '/mail_handler.php';

function _redirect_back_appt(string $params = ''): void
{
    $ref = $_SERVER['HTTP_REFERER'] ?? ($GLOBALS['base_url'] ?? '/') . 'appointment';
    $sep = (strpos($ref, '?') === false) ? '?' : '&';
    header('Location: ' . $ref . $sep . $params);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    _redirect_back_appt('error=method');
}

$sanitize = fn(?string $s) => htmlspecialchars(strip_tags(trim((string) $s)), ENT_QUOTES, 'UTF-8');

$name           = $sanitize($_POST['name']          ?? '');
$phone          = $sanitize($_POST['phone']         ?? '');
$email          = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_SANITIZE_EMAIL);
$subject        = $sanitize($_POST['subject']       ?? '');
$message        = $sanitize($_POST['message']       ?? '');
$preferredDate  = $sanitize($_POST['preferred_date']?? '');
$preferredTime  = $sanitize($_POST['preferred_time']?? '');

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    _redirect_back_appt('error=invalid');
}

// ── 1. Save to DB ────────────────────────────────────────────────────────────
try {
    if ($pdo instanceof PDO) {
        $pdo->prepare(
            'INSERT INTO appointments (full_name, email, phone, subject, message, preferred_date, preferred_time, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, "new")'
        )->execute([
            $name, $email, $phone, $subject, $message,
            $preferredDate ?: null, $preferredTime ?: null,
        ]);
    }
} catch (Throwable $e) {
    error_log('Appointment insert failed: ' . $e->getMessage());
    _redirect_back_appt('error=db');
}

// ── 2. Email admin ───────────────────────────────────────────────────────────
$adminEmail = jpi_admin_email($pdo);

$prettyDate = $preferredDate ?: 'لم يُحدّد';
$prettyTime = $preferredTime ?: '—';

$adminBody = '
<div style="font-family: Tajawal, Arial, sans-serif; max-width: 600px; margin: auto; direction: rtl;">
  <div style="background: #1a1a1a; color: #ebcfa7; padding: 24px; text-align: center;">
    <h2 style="margin: 0; font-size: 22px;">حجز موعد جديد</h2>
    <p style="margin: 8px 0 0; opacity: .7; font-size: 13px;">JPI Law Firm</p>
  </div>
  <div style="padding: 24px; background: #fff; border: 1px solid #e8e8e8;">
    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
      <tr><td style="padding: 8px 0; color: #6c707a; width: 130px;">الاسم:</td>           <td style="padding: 8px 0; font-weight: bold;">' . $name . '</td></tr>
      <tr><td style="padding: 8px 0; color: #6c707a;">البريد:</td>                        <td style="padding: 8px 0;"><a href="mailto:' . $email . '">' . $email . '</a></td></tr>
      <tr><td style="padding: 8px 0; color: #6c707a;">الهاتف:</td>                        <td style="padding: 8px 0;"><a href="tel:' . $phone . '">' . ($phone ?: '—') . '</a></td></tr>
      <tr><td style="padding: 8px 0; color: #6c707a;">الموضوع:</td>                       <td style="padding: 8px 0;">' . ($subject ?: '—') . '</td></tr>
      <tr><td style="padding: 8px 0; color: #6c707a;">الموعد المطلوب:</td>                <td style="padding: 8px 0; font-weight: bold; color: #1a1a1a;">' . $prettyDate . ' &nbsp; ' . $prettyTime . '</td></tr>
    </table>
    <hr style="border: 0; border-top: 1px dashed #e8e8e8; margin: 16px 0;">
    <p style="color: #6c707a; font-size: 12px; margin-bottom: 6px;">ملاحظات:</p>
    <div style="background: #f7f5f0; padding: 14px; border-radius: 8px; line-height: 1.7; white-space: pre-wrap;">' . nl2br($message ?: '—') . '</div>
    <p style="margin-top: 16px; padding: 12px; background: #f5e9d4; border-radius: 8px; font-size: 13px;">
      💡 يمكنك إدارة هذا الحجز من <a href="' . ($GLOBALS['base_url'] ?? '/') . 'BusinessPortal/admin/appointments.php" style="color: #1a1a1a; font-weight: bold;">لوحة التحكم</a>
    </p>
  </div>
</div>';

send_jpi_mail($adminEmail, 'حجز موعد جديد: ' . $name, $adminBody, $email);

// ── 3. Confirmation to user ──────────────────────────────────────────────────
$userBody = '
<div style="font-family: Tajawal, Arial, sans-serif; max-width: 600px; margin: auto; direction: rtl;">
  <div style="background: #1a1a1a; color: #ebcfa7; padding: 28px; text-align: center;">
    <h2 style="margin: 0;">تم استلام طلب الحجز</h2>
  </div>
  <div style="padding: 28px; background: #fff; border: 1px solid #e8e8e8; line-height: 1.8;">
    <p>مرحباً <strong>' . $name . '</strong>،</p>
    <p>شكراً لاختيارك مكتب JPI للمحاماة. تمّ استلام طلب الحجز الخاصّ بك، وسنتواصل معك خلال 24 ساعة لتأكيد الموعد.</p>
    <table style="width: 100%; background: #f7f5f0; border-radius: 8px; padding: 16px; margin: 16px 0; font-size: 14px;">
      <tr><td style="padding: 6px 0; color: #6c707a;">الموعد المطلوب:</td>  <td style="padding: 6px 0; font-weight: bold;">' . $prettyDate . ' &nbsp; ' . $prettyTime . '</td></tr>
      <tr><td style="padding: 6px 0; color: #6c707a;">الموضوع:</td>           <td style="padding: 6px 0;">' . ($subject ?: '—') . '</td></tr>
    </table>
    <p>للاستفسار العاجل: <a href="tel:+962796286204">+962 79 628 6204</a></p>
    <p style="margin-top: 24px;">
      تحياتنا،<br>
      <strong>فريق JPI للمحاماة</strong>
    </p>
  </div>
</div>';

send_jpi_mail($email, 'تم استلام طلب الحجز — JPI Law Firm', $userBody);

_redirect_back_appt('sent=1');
