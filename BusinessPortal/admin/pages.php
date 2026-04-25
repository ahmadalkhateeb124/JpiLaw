<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
requireLogin();

$pageTitle = __('pages_content');
$current = 'pages_content';

// Pages we expose for editing in the admin panel
$EDITABLE_PAGES = [
    'privacy-policy'   => ['label' => 'سياسة الخصوصية',     'icon' => 'fa-user-shield', 'public_url' => 'privacy-policy'],
    'terms-conditions' => ['label' => 'الشروط والأحكام',     'icon' => 'fa-file-contract', 'public_url' => 'terms-conditions'],
    'about'            => ['label' => 'صفحة "من نحن"',       'icon' => 'fa-circle-info',   'public_url' => 'about'],
    'home-hero'        => ['label' => 'الـHero في الرئيسية', 'icon' => 'fa-house',         'public_url' => ''],
];

$slug = $_GET['slug'] ?? '';
if ($slug !== '' && !isset($EDITABLE_PAGES[$slug])) {
    flash('error', __('error_occurred'));
    redirect(bp_url('admin/pages.php'));
}

// ── Save ─────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'save') {
    csrf_check();
    $saveSlug   = (string) ($_POST['slug'] ?? '');
    if (!isset($EDITABLE_PAGES[$saveSlug])) {
        flash('error', __('error_occurred'));
        redirect(bp_url('admin/pages.php'));
    }
    $title   = trim((string) $_POST['title_ar']);
    $content = (string) $_POST['content_ar'];

    // Upsert into pages_content (page_slug + section_key='body' is the canonical body row)
    $pdo->prepare(
        'INSERT INTO pages_content (page_slug, section_key, title_ar, title_en, content_ar, content_en)
         VALUES (?, "body", ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE title_ar = VALUES(title_ar), title_en = VALUES(title_en),
                                 content_ar = VALUES(content_ar), content_en = VALUES(content_en)'
    )->execute([$saveSlug, $title, $title, $content, $content]);

    log_activity($pdo, 'update', 'pages_content', null, "Updated page: $saveSlug");
    flash('success', __('saved_successfully'));
    redirect(bp_url('admin/pages.php?slug=' . urlencode($saveSlug)));
}

// ── Edit view ────────────────────────────────────────────────────────────────
if ($slug !== '') {
    $stmt = $pdo->prepare("SELECT * FROM pages_content WHERE page_slug = ? AND section_key = 'body' LIMIT 1");
    $stmt->execute([$slug]);
    $row = $stmt->fetch() ?: ['title_ar' => '', 'content_ar' => ''];

    $meta = $EDITABLE_PAGES[$slug];

    require BP_PARTIALS . '/header.php';
    ?>
    <div class="page-header">
      <div>
        <div class="breadcrumb">
          <a href="<?= e(bp_url('admin/')) ?>"><i class="fa-solid fa-house"></i> <?= e(__('dashboard')) ?></a>
          <span class="sep">/</span>
          <a href="<?= e(bp_url('admin/pages.php')) ?>"><?= e(__('pages_content')) ?></a>
          <span class="sep">/</span>
          <span><?= e($meta['label']) ?></span>
        </div>
        <h1 class="page-title"><i class="fa-solid <?= e($meta['icon']) ?>" style="color: var(--jpi-cream-dim); margin-inline-end: 8px;"></i> <?= e($meta['label']) ?></h1>
        <p class="page-subtitle">عدّل المحتوى وسيظهر فوراً على الموقع العام.</p>
      </div>
      <div style="display: flex; gap: 8px;">
        <?php if ($meta['public_url'] !== ''): ?>
          <a href="<?= e(SITE_URL . $meta['public_url']) ?>" target="_blank" class="btn btn-outline">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> عرض على الموقع
          </a>
        <?php endif; ?>
        <a href="<?= e(bp_url('admin/pages.php')) ?>" class="btn btn-outline">
          <i class="fa-solid fa-arrow-right"></i> <?= e(__('back')) ?>
        </a>
      </div>
    </div>

    <form method="post">
      <?= csrf_input() ?>
      <input type="hidden" name="_op" value="save">
      <input type="hidden" name="slug" value="<?= e($slug) ?>">

      <div class="card form-page">
        <div class="card-body">
          <div class="form-row">
            <label class="form-label">عنوان الصفحة <span class="req">*</span></label>
            <input type="text" name="title_ar" value="<?= e($row['title_ar'] ?? '') ?>" required dir="rtl" class="form-control" placeholder="مثال: سياسة الخصوصية">
          </div>

          <div class="form-row">
            <label class="form-label">المحتوى <span class="req">*</span></label>
            <textarea name="content_ar" rows="22" dir="rtl" class="form-control" required placeholder="اكتب محتوى الصفحة هنا..."><?= e($row['content_ar'] ?? '') ?></textarea>
            <p class="form-help">يدعم HTML للتنسيق (h2, h3, p, ul, ol, li, blockquote, strong, a …)</p>
          </div>
        </div>

        <div class="form-page-actions">
          <a href="<?= e(bp_url('admin/pages.php')) ?>" class="btn btn-outline"><?= e(__('cancel')) ?></a>
          <button type="submit" class="btn btn-gold"><i class="fa-solid fa-floppy-disk"></i> <?= e(__('save')) ?></button>
        </div>
      </div>
    </form>

    <?php
    require BP_PARTIALS . '/footer.php';
    return;
}

// ── List view ────────────────────────────────────────────────────────────────
// Find which pages have content already
$existing = $pdo->query("SELECT page_slug, updated_at FROM pages_content WHERE section_key='body'")->fetchAll(PDO::FETCH_KEY_PAIR);

require BP_PARTIALS . '/header.php';
?>

<div class="page-header">
  <div>
    <div class="breadcrumb">
      <a href="<?= e(bp_url('admin/')) ?>"><i class="fa-solid fa-house"></i> <?= e(__('dashboard')) ?></a>
      <span class="sep">/</span>
      <span><?= e(__('pages_content')) ?></span>
    </div>
    <h1 class="page-title"><?= e(__('pages_content')) ?></h1>
    <p class="page-subtitle">إدارة محتوى الصفحات الثابتة في الموقع</p>
  </div>
</div>

<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>الصفحة</th>
          <th>المعرّف (slug)</th>
          <th>آخر تحديث</th>
          <th>الحالة</th>
          <th style="width: 130px;"><?= e(__('actions')) ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($EDITABLE_PAGES as $sl => $meta):
            $hasContent = isset($existing[$sl]);
        ?>
          <tr>
            <td>
              <strong><i class="fa-solid <?= e($meta['icon']) ?>" style="color: var(--jpi-cream-dim); margin-inline-end: 8px;"></i> <?= e($meta['label']) ?></strong>
            </td>
            <td><code style="font-size: 11px; color: var(--jpi-text-muted);"><?= e($sl) ?></code></td>
            <td><?= $hasContent ? e(fmt_date($existing[$sl])) : '<span style="color: var(--jpi-text-muted);">—</span>' ?></td>
            <td>
              <?php if ($hasContent): ?>
                <span class="badge badge-success">محرّر</span>
              <?php else: ?>
                <span class="badge badge-muted">يستخدم الافتراضي</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="?slug=<?= e($sl) ?>" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-pen"></i> <?= e(__('edit')) ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require BP_PARTIALS . '/footer.php'; ?>
