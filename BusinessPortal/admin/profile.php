<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
requireLogin();

$pageTitle = __('profile');
$current = '';   // not in main nav

$adminId = (int) $_SESSION['admin_id'];
$stmt = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
$stmt->execute([$adminId]);
$me = $stmt->fetch();

if (!$me) {
    flash('error', __('error_occurred'));
    redirect(bp_url('auth/logout.php'));
}

// ── Save profile ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'save_profile') {
    csrf_check();
    $name  = trim((string) $_POST['full_name']);
    $email = trim((string) $_POST['email']);

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'الاسم أو البريد الإلكتروني غير صحيح.');
        redirect(bp_url('admin/profile.php'));
    }

    // Check email uniqueness
    $check = $pdo->prepare('SELECT id FROM admins WHERE email = ? AND id <> ?');
    $check->execute([$email, $adminId]);
    if ($check->fetch()) {
        flash('error', 'هذا البريد الإلكتروني مستخدم من مسؤول آخر.');
        redirect(bp_url('admin/profile.php'));
    }

    $pdo->prepare('UPDATE admins SET full_name = ?, email = ? WHERE id = ?')
        ->execute([$name, $email, $adminId]);

    // Update session
    $_SESSION['admin_name']  = $name;
    $_SESSION['admin_email'] = $email;

    log_activity($pdo, 'profile_update', 'admin', $adminId, 'Updated own profile');
    flash('success', __('saved_successfully'));
    redirect(bp_url('admin/profile.php'));
}

// ── Change password ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'change_password') {
    csrf_check();
    $current  = (string) ($_POST['current_password']  ?? '');
    $newPw    = (string) ($_POST['new_password']      ?? '');
    $confirm  = (string) ($_POST['confirm_password']  ?? '');

    if (!password_verify($current, $me['password_hash'])) {
        flash('error', 'كلمة المرور الحالية غير صحيحة.');
        redirect(bp_url('admin/profile.php'));
    }
    if (strlen($newPw) < 8) {
        flash('error', 'كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل.');
        redirect(bp_url('admin/profile.php'));
    }
    if ($newPw !== $confirm) {
        flash('error', 'كلمتا المرور الجديدتان غير متطابقتين.');
        redirect(bp_url('admin/profile.php'));
    }

    $hash = password_hash($newPw, PASSWORD_DEFAULT);
    $pdo->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')
        ->execute([$hash, $adminId]);

    log_activity($pdo, 'password_change', 'admin', $adminId, 'Changed own password');
    flash('success', 'تم تغيير كلمة المرور بنجاح.');
    redirect(bp_url('admin/profile.php'));
}

$initials = mb_strtoupper(mb_substr($me['full_name'], 0, 1));

$roleLabel = $me['role'] === 'superadmin' ? 'مسؤول رئيسي' : ($me['role'] === 'editor' ? 'محرر' : 'مسؤول');

require BP_PARTIALS . '/header.php';
?>

<div class="page-header">
  <div>
    <div class="breadcrumb">
      <a href="<?= e(bp_url('admin/')) ?>"><i class="fa-solid fa-house"></i> <?= e(__('dashboard')) ?></a>
      <span class="sep">/</span>
      <span><?= e(__('profile')) ?></span>
    </div>
    <h1 class="page-title"><?= e(__('profile')) ?></h1>
    <p class="page-subtitle">إدارة بيانات حسابك الشخصي وكلمة المرور.</p>
  </div>
</div>

<div class="profile-grid">
  <!-- Profile card -->
  <div>
    <div class="card" style="text-align: center; padding: 30px 20px;">
      <div style="width: 96px; height: 96px; border-radius: 50%; background: linear-gradient(135deg, #1a1a1a, #4a4a4a); color: #ebcfa7; display: inline-flex; align-items: center; justify-content: center; font-size: 38px; font-weight: 700; margin-bottom: 18px; font-family: 'Tajawal', sans-serif;">
        <?= e($initials) ?>
      </div>
      <h3 style="margin: 0 0 4px; font-size: 18px; font-weight: 700;"><?= e($me['full_name']) ?></h3>
      <div style="color: var(--jpi-text-muted); font-size: 13px; margin-bottom: 14px;"><?= e($me['email']) ?></div>
      <span class="badge badge-gold" style="font-size: 11px;"><?= e($roleLabel) ?></span>

      <hr style="border: 0; border-top: 1px solid var(--jpi-border-soft); margin: 22px 0 18px;">

      <div style="display: grid; gap: 12px; text-align: start; font-size: 13px;">
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--jpi-text-muted);">آخر دخول:</span>
          <strong><?= e(fmt_date($me['last_login_at'])) ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--jpi-text-muted);">تاريخ الإنشاء:</span>
          <strong><?= e(fmt_date($me['created_at'], 'Y-m-d')) ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--jpi-text-muted);">الحالة:</span>
          <span class="badge <?= $me['is_active'] ? 'badge-success' : 'badge-muted' ?>" style="font-size: 10px;">
            <?= e($me['is_active'] ? __('active') : __('inactive')) ?>
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Forms -->
  <div>
    <!-- Profile info form -->
    <form method="post">
      <?= csrf_input() ?>
      <input type="hidden" name="_op" value="save_profile">

      <div class="card form-page" style="margin-bottom: 16px;">
        <div class="card-header">
          <h3 class="card-title"><i class="fa-regular fa-user"></i> البيانات الشخصية</h3>
        </div>
        <div class="card-body">
          <div class="form-grid-2">
            <div class="form-row">
              <label class="form-label"><?= e(__('name')) ?><span class="req">*</span></label>
              <input type="text" name="full_name" value="<?= e($me['full_name']) ?>" required class="form-control">
            </div>
            <div class="form-row">
              <label class="form-label"><?= e(__('email')) ?><span class="req">*</span></label>
              <input type="email" name="email" value="<?= e($me['email']) ?>" required dir="ltr" class="form-control">
            </div>
          </div>
        </div>
        <div class="form-page-actions">
          <button type="submit" class="btn btn-gold"><i class="fa-solid fa-floppy-disk"></i> حفظ البيانات</button>
        </div>
      </div>
    </form>

    <!-- Change password form -->
    <form method="post">
      <?= csrf_input() ?>
      <input type="hidden" name="_op" value="change_password">

      <div class="card form-page">
        <div class="card-header">
          <h3 class="card-title"><i class="fa-solid fa-lock"></i> تغيير كلمة المرور</h3>
        </div>
        <div class="card-body">
          <div class="form-row">
            <label class="form-label">كلمة المرور الحالية<span class="req">*</span></label>
            <input type="password" name="current_password" required dir="ltr" class="form-control" autocomplete="current-password">
          </div>
          <div class="form-grid-2">
            <div class="form-row">
              <label class="form-label">كلمة المرور الجديدة<span class="req">*</span></label>
              <input type="password" name="new_password" required dir="ltr" class="form-control" autocomplete="new-password" minlength="8">
              <p class="form-help">8 أحرف على الأقل.</p>
            </div>
            <div class="form-row">
              <label class="form-label">تأكيد كلمة المرور<span class="req">*</span></label>
              <input type="password" name="confirm_password" required dir="ltr" class="form-control" autocomplete="new-password" minlength="8">
            </div>
          </div>
        </div>
        <div class="form-page-actions">
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-key"></i> تحديث كلمة المرور</button>
        </div>
      </div>
    </form>
  </div>
</div>

<style>
.profile-grid { display: grid; grid-template-columns: 320px 1fr; gap: 20px; }
@media (max-width: 900px) { .profile-grid { grid-template-columns: 1fr; } }
</style>

<?php require BP_PARTIALS . '/footer.php'; ?>
