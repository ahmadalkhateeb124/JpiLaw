<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';

if (isLoggedIn()) {
    redirect(BP_URL . 'admin/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $email    = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = __('login_failed');
    } else {
        $admin = authenticate($pdo, $email, $password);
        if ($admin) {
            loginAdmin($admin);
            log_activity($pdo, 'login', 'admin', (int) $admin['id'], 'Admin signed in');
            redirect(BP_URL . 'admin/');
        }
        $error = __('login_failed');
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e(__('sign_in')) ?> — <?= e(__('app_name')) ?></title>
<link rel="icon" type="image/x-icon" href="<?= e(SITE_URL) ?>favicon.ico">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="login-shell">

  <aside class="login-side">
    <div>
      <span class="login-pill">
        <i class="fa-solid fa-scale-balanced"></i>
        <?= e(__('app_name')) ?>
      </span>

      <h1>
        منصّة إدارة <span class="accent">شاملة</span><br>
        لمكتب المحاماة
      </h1>

      <p class="subtitle">
        أدِر المقالات، الخدمات، الاستفسارات، والحجوزات بسهولة من مكان واحد. منصّة آمنة ومصمَّمة خصيصاً لاحتياجات شركات المحاماة في الأردن وفلسطين.
      </p>

      <ul class="login-features">
        <li>إدارة كاملة للمدوّنة والمحتوى</li>
        <li>تحكم كامل في إعدادات الموقع</li>
        <li>متابعة الاستفسارات وحجوزات المواعيد</li>
        <li>إدارة فريق المحامين والخدمات</li>
      </ul>
    </div>

    <div class="footnote">© <?= date('Y') ?> JPI Law Firm — v<?= e(APP_VERSION) ?></div>
  </aside>

  <section class="login-form-side">
    <div class="login-form-card">

      <div class="brand">
        <img src="<?= e(SITE_URL) ?>assets/img/logo.png" alt="JPI">
        <div>
          <div class="brand-name">JPI Law Firm</div>
          <div class="brand-tag">مكتب المحاماة</div>
        </div>
      </div>

      <h2><?= e(__('welcome_back')) ?></h2>
      <p class="lead"><?= e(__('sign_in_subtitle')) ?></p>

      <?php if ($error): ?>
        <div class="alert alert-error">
          <i class="fa-solid fa-circle-exclamation"></i>
          <?= e($error) ?>
        </div>
      <?php endif; ?>

      <form method="post" novalidate>
        <?= csrf_input() ?>

        <div class="form-row">
          <label class="form-label" for="email"><?= e(__('email')) ?></label>
          <input
            id="email" name="email" type="email" required autofocus
            value="<?= e($_POST['email'] ?? '') ?>"
            placeholder="you@example.com"
            class="form-control" dir="ltr">
        </div>

        <div class="form-row">
          <div class="field-row-between">
            <label class="form-label" for="password"><?= e(__('password')) ?></label>
            <a href="#"><?= e(__('forgot_password')) ?></a>
          </div>
          <div class="password-wrap">
            <input id="password" name="password" type="password" required class="form-control" placeholder="••••••••" dir="ltr">
            <button type="button" class="toggle" aria-label="إظهار كلمة المرور" onclick="togglePw()"><i class="fa-regular fa-eye" id="pw-icon"></i></button>
          </div>
        </div>

        <label class="login-checkbox">
          <input type="checkbox" name="remember" value="1">
          <?= e(__('remember_me')) ?>
        </label>

        <button class="btn-login" type="submit"><?= e(__('sign_in')) ?></button>
      </form>

      <div class="login-foot">
        <i class="fa-solid fa-shield-halved"></i> &nbsp; <?= e(__('protected_area')) ?>
      </div>
    </div>
  </section>

</div>

<script>
function togglePw(){
  const i = document.getElementById('password');
  const ic = document.getElementById('pw-icon');
  if (i.type === 'password') { i.type = 'text';  ic.className = 'fa-regular fa-eye-slash'; }
  else                       { i.type = 'password'; ic.className = 'fa-regular fa-eye'; }
}
</script>
</body>
</html>
