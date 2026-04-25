<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
requireLogin();

$pageTitle = __('dashboard');
$current = 'index';

$stats = [
    'posts'      => (int) $pdo->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn(),
    'inquiries'  => (int) $pdo->query('SELECT COUNT(*) FROM Inquiries')->fetchColumn(),
    'newsletter' => (int) $pdo->query('SELECT COUNT(*) FROM NewsletterMails')->fetchColumn(),
    'appts'      => (int) $pdo->query("SELECT COUNT(*) FROM appointments WHERE status IN ('new','contacted')")->fetchColumn(),
];

$recentInquiries = $pdo->query(
    'SELECT id, FullName, Email, Subject, created_at FROM Inquiries ORDER BY id DESC LIMIT 5'
)->fetchAll();

$recentPosts = $pdo->query(
    "SELECT id, title_ar, status, created_at FROM blog_posts ORDER BY id DESC LIMIT 5"
)->fetchAll();

require BP_PARTIALS . '/header.php';
?>

<div class="page-header">
  <div>
    <div class="breadcrumb"><i class="fa-solid fa-house"></i> <?= e(__('dashboard')) ?></div>
    <h1 class="page-title">أهلاً بعودتك، <?= e($_admin['name']) ?> 👋</h1>
    <p class="page-subtitle">هذا ملخّص نشاط موقعك اليوم.</p>
  </div>
  <a href="<?= e(bp_url('admin/posts.php?action=new')) ?>" class="btn btn-gold">
    <i class="fa-solid fa-plus"></i> مقال جديد
  </a>
</div>

<div class="stat-grid">
  <div class="stat-card">
    <div>
      <div class="label"><?= e(__('total_posts')) ?></div>
      <div class="value" data-count="<?= (int) $stats['posts'] ?>"><?= (int) $stats['posts'] ?></div>
      <div class="meta"><a href="<?= e(bp_url('admin/posts.php')) ?>"><?= e(__('view_all')) ?> <i class="fa-solid fa-arrow-left" style="font-size: 10px;"></i></a></div>
    </div>
    <div class="icon-wrap"><i class="fa-solid fa-newspaper"></i></div>
  </div>

  <div class="stat-card">
    <div>
      <div class="label"><?= e(__('total_inquiries')) ?></div>
      <div class="value" data-count="<?= (int) $stats['inquiries'] ?>"><?= (int) $stats['inquiries'] ?></div>
      <div class="meta"><a href="<?= e(bp_url('admin/inquiries.php')) ?>"><?= e(__('view_all')) ?> <i class="fa-solid fa-arrow-left" style="font-size: 10px;"></i></a></div>
    </div>
    <div class="icon-wrap" style="background: rgba(46,125,79,0.12); color: var(--jpi-success);"><i class="fa-solid fa-envelope-open-text"></i></div>
  </div>

  <div class="stat-card">
    <div>
      <div class="label"><?= e(__('newsletter_subs')) ?></div>
      <div class="value" data-count="<?= (int) $stats['newsletter'] ?>"><?= (int) $stats['newsletter'] ?></div>
      <div class="meta"><a href="<?= e(bp_url('admin/newsletter.php')) ?>"><?= e(__('view_all')) ?> <i class="fa-solid fa-arrow-left" style="font-size: 10px;"></i></a></div>
    </div>
    <div class="icon-wrap" style="background: rgba(58,110,165,0.12); color: var(--jpi-info);"><i class="fa-solid fa-paper-plane"></i></div>
  </div>

  <div class="stat-card">
    <div>
      <div class="label"><?= e(__('pending_appts')) ?></div>
      <div class="value" data-count="<?= (int) $stats['appts'] ?>"><?= (int) $stats['appts'] ?></div>
      <div class="meta"><a href="<?= e(bp_url('admin/appointments.php')) ?>"><?= e(__('view_all')) ?> <i class="fa-solid fa-arrow-left" style="font-size: 10px;"></i></a></div>
    </div>
    <div class="icon-wrap" style="background: rgba(201,138,31,0.14); color: var(--jpi-warning);"><i class="fa-solid fa-calendar-check"></i></div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="dash-grid">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><i class="fa-solid fa-envelope"></i> <?= e(__('recent_inquiries')) ?></h3>
      <a href="<?= e(bp_url('admin/inquiries.php')) ?>" class="btn btn-outline btn-sm"><?= e(__('view_all')) ?></a>
    </div>
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th><?= e(__('name')) ?></th>
            <th><?= e(__('subject')) ?></th>
            <th><?= e(__('created_at')) ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$recentInquiries): ?>
            <tr><td colspan="3" class="empty-state"><i class="fa-regular fa-folder-open"></i><?= e(__('no_records')) ?></td></tr>
          <?php else: foreach ($recentInquiries as $r): ?>
            <tr>
              <td><strong><?= e($r['FullName']) ?></strong><br><span style="color: var(--jpi-text-muted); font-size: 11px;"><?= e($r['Email']) ?></span></td>
              <td><?= e($r['Subject'] ?: '—') ?></td>
              <td><?= e(fmt_date($r['created_at'])) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><i class="fa-solid fa-newspaper"></i> آخر المقالات</h3>
      <a href="<?= e(bp_url('admin/posts.php')) ?>" class="btn btn-outline btn-sm"><?= e(__('view_all')) ?></a>
    </div>
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th><?= e(__('title')) ?></th>
            <th><?= e(__('status')) ?></th>
            <th><?= e(__('created_at')) ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$recentPosts): ?>
            <tr><td colspan="3" class="empty-state"><i class="fa-regular fa-newspaper"></i><?= e(__('no_records')) ?></td></tr>
          <?php else: foreach ($recentPosts as $p): ?>
            <tr>
              <td><strong><?= e($p['title_ar']) ?></strong></td>
              <td>
                <?php
                  $s = $p['status'];
                  $cls = $s === 'published' ? 'badge-success' : ($s === 'draft' ? 'badge-warning' : 'badge-muted');
                ?>
                <span class="badge <?= $cls ?>"><?= e(__($s)) ?></span>
              </td>
              <td><?= e(fmt_date($p['created_at'])) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>@media (max-width: 992px){.dash-grid{grid-template-columns: 1fr !important;}}</style>

<?php require BP_PARTIALS . '/footer.php'; ?>
