<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
requireLogin();

$pageTitle = __('blog_posts');
$current = 'posts';
$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'delete') {
    csrf_check();
    $delId = (int) $_POST['id'];
    $pdo->prepare('DELETE FROM blog_posts WHERE id = ?')->execute([$delId]);
    log_activity($pdo, 'delete', 'blog_post', $delId);
    flash('success', __('deleted_successfully'));
    redirect(bp_url('admin/posts.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'save') {
    csrf_check();
    $editId = (int) ($_POST['id'] ?? 0);

    $titleAr   = trim((string) $_POST['title_ar']);
    $slug      = trim((string) $_POST['slug']) ?: slugify($titleAr);
    $slug      = uniqueSlug($pdo, 'blog_posts', $slug, $editId ?: null);
    $excerptAr = trim((string) ($_POST['excerpt_ar'] ?? ''));
    $contentAr = (string) $_POST['content_ar'];
    $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
    $status    = in_array($_POST['status'] ?? 'draft', ['draft','published','archived'], true) ? $_POST['status'] : 'draft';
    $metaTitleAr = trim((string) ($_POST['meta_title_ar'] ?? ''));
    $metaDescAr  = trim((string) ($_POST['meta_description_ar'] ?? ''));
    $metaKwAr    = trim((string) ($_POST['meta_keywords_ar'] ?? ''));

    $featured = $_POST['featured_image_existing'] ?? '';
    if (!empty($_FILES['featured_image']['name'])) {
        $url = upload_image($_FILES['featured_image'], 'blog');
        if ($url) $featured = $url;
    }

    $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

    if ($editId) {
        $pdo->prepare(
            'UPDATE blog_posts SET slug=?, category_id=?, title_ar=?, title_en=?, excerpt_ar=?, excerpt_en=?,
                content_ar=?, content_en=?, featured_image=?, meta_title_ar=?, meta_title_en=?,
                meta_description_ar=?, meta_description_en=?, meta_keywords_ar=?, meta_keywords_en=?,
                status=?, published_at = COALESCE(published_at, ?) WHERE id=?'
        )->execute([
            $slug, $categoryId, $titleAr, $titleAr, $excerptAr, $excerptAr,
            $contentAr, $contentAr, $featured, $metaTitleAr, $metaTitleAr,
            $metaDescAr, $metaDescAr, $metaKwAr, $metaKwAr, $status, $publishedAt, $editId,
        ]);
        log_activity($pdo, 'update', 'blog_post', $editId);
    } else {
        $pdo->prepare(
            'INSERT INTO blog_posts (slug, category_id, author_id, title_ar, title_en, excerpt_ar, excerpt_en,
                content_ar, content_en, featured_image, meta_title_ar, meta_title_en,
                meta_description_ar, meta_description_en, meta_keywords_ar, meta_keywords_en, status, published_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
        )->execute([
            $slug, $categoryId, $_SESSION['admin_id'], $titleAr, $titleAr, $excerptAr, $excerptAr,
            $contentAr, $contentAr, $featured, $metaTitleAr, $metaTitleAr,
            $metaDescAr, $metaDescAr, $metaKwAr, $metaKwAr, $status, $publishedAt,
        ]);
        log_activity($pdo, 'create', 'blog_post', (int) $pdo->lastInsertId());
    }

    flash('success', __('saved_successfully'));
    redirect(bp_url('admin/posts.php'));
}

$row = [
    'id' => 0, 'slug' => '', 'category_id' => '', 'title_ar' => '',
    'excerpt_ar' => '', 'content_ar' => '',
    'featured_image' => '', 'status' => 'draft',
    'meta_title_ar' => '', 'meta_description_ar' => '', 'meta_keywords_ar' => '',
];
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch() ?: $row;
}

$categories = $pdo->query('SELECT id, name_ar FROM blog_categories WHERE is_active = 1 ORDER BY sort_order, name_ar')->fetchAll();

// ── Form view ────────────────────────────────────────────────────────────────
if ($action === 'new' || $action === 'edit') {
    require BP_PARTIALS . '/header.php';
    $isEdit = !empty($row['id']);
    ?>
    <div class="page-header">
      <div>
        <div class="breadcrumb">
          <a href="<?= e(bp_url('admin/')) ?>"><i class="fa-solid fa-house"></i> <?= e(__('dashboard')) ?></a>
          <span class="sep">/</span>
          <a href="<?= e(bp_url('admin/posts.php')) ?>"><?= e(__('blog_posts')) ?></a>
          <span class="sep">/</span>
          <span><?= e($isEdit ? __('edit') : __('add_new')) ?></span>
        </div>
        <h1 class="page-title"><?= e($isEdit ? __('edit') : __('add_new')) ?> — <?= e(__('blog_posts')) ?></h1>
      </div>
      <a href="<?= e(bp_url('admin/posts.php')) ?>" class="btn btn-outline">
        <i class="fa-solid fa-arrow-right"></i> <?= e(__('back')) ?>
      </a>
    </div>

    <form method="post" enctype="multipart/form-data">
      <?= csrf_input() ?>
      <input type="hidden" name="_op" value="save">
      <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">

      <div class="card form-page">
        <div class="card-body">
          <div class="form-row">
            <label class="form-label"><?= e(__('title')) ?><span class="req">*</span></label>
            <input type="text" name="title_ar" value="<?= e($row['title_ar']) ?>" required dir="rtl" class="form-control" placeholder="مثال: حقوقك القانونية في عقود العمل">
          </div>

          <div class="form-grid-2">
            <div class="form-row">
              <label class="form-label"><?= e(__('status')) ?></label>
              <select name="status" class="form-select">
                <option value="draft" <?= $row['status'] === 'draft' ? 'selected' : '' ?>><?= e(__('draft')) ?></option>
                <option value="published" <?= $row['status'] === 'published' ? 'selected' : '' ?>><?= e(__('published')) ?></option>
                <option value="archived" <?= $row['status'] === 'archived' ? 'selected' : '' ?>><?= e(__('archived')) ?></option>
              </select>
            </div>
            <div class="form-row">
              <label class="form-label"><?= e(__('category')) ?></label>
              <select name="category_id" class="form-select">
                <option value="">— بدون تصنيف —</option>
                <?php foreach ($categories as $c): ?>
                  <option value="<?= (int) $c['id'] ?>" <?= (int) $row['category_id'] === (int) $c['id'] ? 'selected' : '' ?>>
                    <?= e($c['name_ar']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <label class="form-label"><?= e(__('excerpt')) ?></label>
            <textarea name="excerpt_ar" rows="2" dir="rtl" class="form-control" placeholder="ملخّص قصير يظهر في صفحة المدونة..."><?= e($row['excerpt_ar']) ?></textarea>
          </div>

          <div class="form-row">
            <label class="form-label"><?= e(__('content')) ?><span class="req">*</span></label>
            <textarea name="content_ar" rows="14" dir="rtl" class="form-control" required placeholder="اكتب محتوى المقال هنا..."><?= e($row['content_ar']) ?></textarea>
            <p class="form-help">يدعم HTML للتنسيق (h2, h3, p, ul, blockquote …)</p>
          </div>

          <div class="form-row">
            <label class="form-label"><?= e(__('featured_image')) ?></label>
            <?php if (!empty($row['featured_image'])): ?>
              <img src="<?= e($row['featured_image']) ?>" alt="" style="max-height: 140px; border-radius: 8px; margin-bottom: 10px; border: 1px solid var(--jpi-border-soft); display: block;">
              <input type="hidden" name="featured_image_existing" value="<?= e($row['featured_image']) ?>">
            <?php endif; ?>
            <input type="file" name="featured_image" accept="image/*" class="form-control">
          </div>

          <div class="form-row">
            <label class="form-label"><?= e(__('slug')) ?></label>
            <input type="text" name="slug" value="<?= e($row['slug']) ?>" dir="ltr" class="form-control" placeholder="auto">
          </div>

          <details style="margin-top: 12px; border: 1px solid var(--jpi-border-soft); border-radius: 10px; padding: 14px 18px;">
            <summary style="cursor: pointer; font-weight: 700; color: var(--jpi-dark); font-size: 13px;"><i class="fa-solid fa-magnifying-glass" style="color: var(--jpi-gold); margin-inline-end: 6px;"></i> إعدادات SEO <span style="color: var(--jpi-text-muted); font-weight: 400; font-size: 11px;">(اختياري)</span></summary>
            <div style="margin-top: 16px;">
              <div class="form-row">
                <label class="form-label"><?= e(__('meta_title')) ?></label>
                <input type="text" name="meta_title_ar" value="<?= e($row['meta_title_ar']) ?>" dir="rtl" class="form-control" placeholder="عنوان SEO المخصص">
              </div>
              <div class="form-row">
                <label class="form-label"><?= e(__('meta_description')) ?></label>
                <textarea name="meta_description_ar" rows="2" dir="rtl" class="form-control" placeholder="وصف 150-160 حرف يظهر في نتائج البحث"><?= e($row['meta_description_ar']) ?></textarea>
              </div>
              <div class="form-row">
                <label class="form-label"><?= e(__('meta_keywords')) ?></label>
                <input type="text" name="meta_keywords_ar" value="<?= e($row['meta_keywords_ar']) ?>" dir="rtl" class="form-control" placeholder="كلمات مفصولة بفواصل">
              </div>
            </div>
          </details>
        </div>

        <div class="form-page-actions">
          <a href="<?= e(bp_url('admin/posts.php')) ?>" class="btn btn-outline"><?= e(__('cancel')) ?></a>
          <button type="submit" class="btn btn-gold"><i class="fa-solid fa-floppy-disk"></i> <?= e(__('save')) ?></button>
        </div>
      </div>
    </form>

    <?php
    require BP_PARTIALS . '/footer.php';
    return;
}

// ── List view ────────────────────────────────────────────────────────────────
$q       = trim((string) ($_GET['q'] ?? ''));
$catFilt = (int) ($_GET['cat'] ?? 0);
$status  = $_GET['status'] ?? '';
$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 15;

$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(p.title_ar LIKE ?)';
    $params[] = "%$q%";
}
if ($catFilt) {
    $where[] = 'p.category_id = ?';
    $params[] = $catFilt;
}
if (in_array($status, ['draft','published','archived'], true)) {
    $where[] = 'p.status = ?';
    $params[] = $status;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts p $whereSql");
$totalStmt->execute($params);
$total = (int) $totalStmt->fetchColumn();
$pg = paginate($total, $perPage, $page);

$listStmt = $pdo->prepare(
    "SELECT p.*, c.name_ar AS cat_name
     FROM blog_posts p LEFT JOIN blog_categories c ON c.id = p.category_id
     $whereSql ORDER BY p.id DESC LIMIT $perPage OFFSET {$pg['offset']}"
);
$listStmt->execute($params);
$posts = $listStmt->fetchAll();

require BP_PARTIALS . '/header.php';
?>

<div class="page-header">
  <div>
    <div class="breadcrumb">
      <a href="<?= e(bp_url('admin/')) ?>"><i class="fa-solid fa-house"></i> <?= e(__('dashboard')) ?></a>
      <span class="sep">/</span>
      <span><?= e(__('blog_posts')) ?></span>
    </div>
    <h1 class="page-title"><?= e(__('blog_posts')) ?></h1>
  </div>
  <a href="?action=new" class="btn btn-gold">
    <i class="fa-solid fa-plus"></i> <?= e(__('add_new')) ?>
  </a>
</div>

<div class="card">
  <form method="get" style="display: flex; gap: 10px; flex-wrap: wrap; padding: 16px 20px; border-bottom: 1px solid var(--jpi-border-soft);">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="<?= e(__('search')) ?>..." class="form-control" style="flex: 1; min-width: 200px;">
    <select name="cat" class="form-select" style="width: auto;">
      <option value="0">كل التصنيفات</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?= (int) $c['id'] ?>" <?= $catFilt === (int) $c['id'] ? 'selected' : '' ?>>
          <?= e($c['name_ar']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <select name="status" class="form-select" style="width: auto;">
      <option value=""><?= e(__('all')) ?></option>
      <option value="published" <?= $status === 'published' ? 'selected' : '' ?>><?= e(__('published')) ?></option>
      <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>><?= e(__('draft')) ?></option>
      <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>><?= e(__('archived')) ?></option>
    </select>
    <button class="btn btn-outline"><i class="fa-solid fa-filter"></i> <?= e(__('search')) ?></button>
  </form>
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th style="width: 70px;"></th>
          <th><?= e(__('title')) ?></th>
          <th><?= e(__('category')) ?></th>
          <th><?= e(__('status')) ?></th>
          <th><?= e(__('created_at')) ?></th>
          <th style="width: 110px;"><?= e(__('actions')) ?></th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$posts): ?>
        <tr><td colspan="6" class="empty-state"><i class="fa-regular fa-newspaper"></i><?= e(__('no_records')) ?></td></tr>
      <?php else: foreach ($posts as $p): ?>
        <tr>
          <td>
            <?php if (!empty($p['featured_image'])): ?>
              <img src="<?= e($p['featured_image']) ?>" style="width: 50px; height: 40px; object-fit: cover; border-radius: 4px;">
            <?php else: ?>
              <div style="width: 50px; height: 40px; border-radius: 4px; background: var(--jpi-bg); display: flex; align-items: center; justify-content: center; color: var(--jpi-text-muted);"><i class="fa-regular fa-image"></i></div>
            <?php endif; ?>
          </td>
          <td>
            <strong><?= e($p['title_ar']) ?></strong>
            <div style="color: var(--jpi-text-muted); font-size: 11px;"><code><?= e($p['slug']) ?></code></div>
          </td>
          <td>
            <?php if ($p['cat_name']): ?>
              <span class="badge badge-gold"><?= e($p['cat_name']) ?></span>
            <?php else: ?>—<?php endif; ?>
          </td>
          <td>
            <?php $cls = $p['status'] === 'published' ? 'badge-success' : ($p['status'] === 'draft' ? 'badge-warning' : 'badge-muted'); ?>
            <span class="badge <?= $cls ?>"><?= e(__($p['status'])) ?></span>
          </td>
          <td><?= e(fmt_date($p['created_at'])) ?></td>
          <td>
            <div class="row-actions">
              <a href="?action=edit&id=<?= (int) $p['id'] ?>" class="btn btn-outline btn-sm btn-icon"><i class="fa-solid fa-pen"></i></a>
              <form method="post" data-confirm="<?= e(__('confirm_delete')) ?>" style="display:inline;">
                <?= csrf_input() ?>
                <input type="hidden" name="_op" value="delete">
                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pg['total_pages'] > 1): ?>
    <div style="padding: 16px 20px; border-top: 1px solid var(--jpi-border-soft); display: flex; justify-content: space-between; align-items: center;">
      <div style="color: var(--jpi-text-muted); font-size: 12px;">صفحة <?= $pg['page'] ?> من <?= $pg['total_pages'] ?></div>
      <div class="pagination">
        <?php for ($i = 1; $i <= $pg['total_pages']; $i++):
            $qs = http_build_query(array_merge($_GET, ['page' => $i])); ?>
          <a href="?<?= e($qs) ?>" class="<?= $i === $pg['page'] ? 'current' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php require BP_PARTIALS . '/footer.php'; ?>
