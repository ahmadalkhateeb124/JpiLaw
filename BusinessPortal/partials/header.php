<?php
$_admin = currentAdmin();
$_initials = mb_strtoupper(mb_substr($_admin['name'] ?? 'A', 0, 1));
$_pageTitle = $pageTitle ?? __('dashboard');
$_current   = $current ?? '';

// ── Top horizontal nav items ─────────────────────────────────────────────────
$_nav = [
    ['key' => 'index',         'label' => __('dashboard'),       'icon' => 'fa-house',             'href' => bp_url('admin/')],
    ['key' => 'posts',         'label' => __('blog_posts'),      'icon' => 'fa-newspaper',         'href' => bp_url('admin/posts.php')],
    ['key' => 'categories',    'label' => __('blog_categories'), 'icon' => 'fa-folder-tree',       'href' => bp_url('admin/categories.php')],
    ['key' => 'services',      'label' => __('services'),        'icon' => 'fa-briefcase',         'href' => bp_url('admin/services.php')],
    ['key' => 'practice',      'label' => __('practice_areas'),  'icon' => 'fa-scale-balanced',    'href' => bp_url('admin/practice.php')],
    ['key' => 'attorneys',     'label' => __('attorneys'),       'icon' => 'fa-user-tie',          'href' => bp_url('admin/attorneys.php')],
    ['key' => 'testimonials',  'label' => __('testimonials'),    'icon' => 'fa-quote-right',       'href' => bp_url('admin/testimonials.php')],
    ['key' => 'faqs',          'label' => __('faqs'),            'icon' => 'fa-circle-question',   'href' => bp_url('admin/faqs.php')],
    ['key' => 'certificates',  'label' => __('certificates'),    'icon' => 'fa-certificate',       'href' => bp_url('admin/certificates.php')],
    ['key' => 'inquiries',     'label' => __('inquiries'),       'icon' => 'fa-envelope-open-text','href' => bp_url('admin/inquiries.php')],
    ['key' => 'appointments',  'label' => __('appointments'),    'icon' => 'fa-calendar-check',    'href' => bp_url('admin/appointments.php')],
    ['key' => 'newsletter',    'label' => __('newsletter'),      'icon' => 'fa-paper-plane',       'href' => bp_url('admin/newsletter.php')],
    ['key' => 'pages_content', 'label' => __('pages_content'),   'icon' => 'fa-file-lines',        'href' => bp_url('admin/pages.php')],
    ['key' => 'settings',      'label' => __('site_settings'),   'icon' => 'fa-gear',              'href' => bp_url('admin/settings.php')],
    ['key' => 'admins',        'label' => __('admins'),          'icon' => 'fa-user-shield',       'href' => bp_url('admin/admins.php')],
    ['key' => 'activity',      'label' => __('activity_log'),    'icon' => 'fa-clock-rotate-left', 'href' => bp_url('admin/activity.php')],
];

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($_pageTitle) ?> — <?= e(__('app_name')) ?></title>
<link rel="icon" type="image/x-icon" href="<?= e(SITE_URL) ?>favicon.ico">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Top brand bar -->
<header class="bp-topbar">
  <div class="container-bp bp-topbar-inner">
    <a href="<?= e(bp_url('admin/')) ?>" class="bp-brand">
      <img src="<?= e(SITE_URL) ?>assets/img/logo.png" alt="JPI">
      <div class="brand-text">
        <span class="brand-name">JPI Law</span>
        <span class="brand-tag">مكتب المحاماة</span>
      </div>
    </a>

    <div class="bp-topbar-spacer"></div>

    <div class="bp-topbar-actions">
      <a href="<?= e(bp_url('admin/inquiries.php')) ?>" class="bp-bell" aria-label="الإشعارات">
        <i class="fa-regular fa-bell"></i>
        <?php
          $unread = (int) $pdo->query('SELECT COUNT(*) FROM Inquiries WHERE is_read = 0')->fetchColumn();
          if ($unread > 0): ?>
          <span class="dot"></span>
        <?php endif; ?>
      </a>

      <div class="bp-user" onclick="this.querySelector('.bp-user-menu').classList.toggle('open')">
        <span class="avatar"><?= e($_initials) ?></span>
        <span class="info">
          <span class="name"><?= e($_admin['name'] ?? '') ?></span>
          <span class="role"><?= e($_admin['role'] === 'superadmin' ? 'مسؤول رئيسي' : ($_admin['role'] === 'editor' ? 'محرر' : 'مسؤول')) ?></span>
        </span>
        <i class="fa-solid fa-chevron-down chev"></i>

        <div class="bp-user-menu">
          <a href="<?= e(bp_url('admin/profile.php')) ?>"><i class="fa-regular fa-user"></i> <?= e(__('profile')) ?></a>
          <a href="<?= e(bp_url('admin/settings.php')) ?>"><i class="fa-solid fa-gear"></i> <?= e(__('settings')) ?></a>
          <a href="<?= e(SITE_URL) ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> عرض الموقع</a>
          <hr>
          <a href="<?= e(bp_url('auth/logout.php')) ?>" class="danger"><i class="fa-solid fa-right-from-bracket"></i> <?= e(__('logout')) ?></a>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- Horizontal tab nav -->
<nav class="bp-nav">
  <div class="container-bp">
    <div class="bp-nav-inner">
      <?php foreach ($_nav as $item): ?>
        <a href="<?= e($item['href']) ?>" class="bp-nav-link <?= $_current === $item['key'] ? 'active' : '' ?>">
          <i class="fa-solid <?= e($item['icon']) ?>"></i>
          <?= e($item['label']) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</nav>

<!-- Toast container (auto-populated from flash messages) -->
<div class="toast-container" id="toast-container">
  <?php if ($_msg = flash('success')): ?>
    <div class="toast toast-success" data-auto-dismiss="4000">
      <i class="fa-solid fa-circle-check"></i>
      <span><?= e($_msg) ?></span>
    </div>
  <?php endif; ?>
  <?php if ($_msg = flash('error')): ?>
    <div class="toast toast-error" data-auto-dismiss="5000">
      <i class="fa-solid fa-circle-exclamation"></i>
      <span><?= e($_msg) ?></span>
    </div>
  <?php endif; ?>
</div>

<!-- Main content -->
<main class="page-content">
  <div class="container-bp">
