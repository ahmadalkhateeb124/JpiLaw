<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
requireLogin();

$pageTitle = __('blog_categories');
$current = 'categories';
$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'delete') {
    csrf_check();
    $delId = (int) ($_POST['id'] ?? 0);
    $pdo->prepare('DELETE FROM blog_categories WHERE id = ?')->execute([$delId]);
    log_activity($pdo, 'delete', 'blog_category', $delId);
    flash('success', __('deleted_successfully'));
    redirect(bp_url('admin/categories.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'save') {
    csrf_check();
    $editId   = (int) ($_POST['id'] ?? 0);
    $nameAr   = trim((string) $_POST['name_ar']);
    $slug     = trim((string) $_POST['slug']) ?: slugify($nameAr);
    $slug     = uniqueSlug($pdo, 'blog_categories', $slug, $editId ?: null);
    $descAr   = trim((string) $_POST['description_ar']);
    $sort     = (int) ($_POST['sort_order'] ?? 0);
    $active   = !empty($_POST['is_active']) ? 1 : 0;

    if ($editId) {
        $pdo->prepare(
            'UPDATE blog_categories SET slug=?, name_ar=?, name_en=?, description_ar=?, description_en=?, sort_order=?, is_active=? WHERE id=?'
        )->execute([$slug, $nameAr, $nameAr, $descAr, $descAr, $sort, $active, $editId]);
        log_activity($pdo, 'update', 'blog_category', $editId);
    } else {
        $pdo->prepare(
            'INSERT INTO blog_categories (slug, name_ar, name_en, description_ar, description_en, sort_order, is_active) VALUES (?,?,?,?,?,?,?)'
        )->execute([$slug, $nameAr, $nameAr, $descAr, $descAr, $sort, $active]);
        log_activity($pdo, 'create', 'blog_category', (int) $pdo->lastInsertId());
    }

    flash('success', __('saved_successfully'));
    redirect(bp_url('admin/categories.php'));
}

$row = ['id' => 0, 'slug' => '', 'name_ar' => '', 'description_ar' => '', 'sort_order' => 0, 'is_active' => 1];
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare('SELECT * FROM blog_categories WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch() ?: $row;
}

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
          <a href="<?= e(bp_url('admin/categories.php')) ?>"><?= e(__('blog_categories')) ?></a>
          <span class="sep">/</span>
          <span><?= e($isEdit ? __('edit') : __('add_new')) ?></span>
        </div>
        <h1 class="page-title"><?= e($isEdit ? __('edit') : __('add_new')) ?> — <?= e(__('blog_categories')) ?></h1>
      </div>
      <a href="<?= e(bp_url('admin/categories.php')) ?>" class="btn btn-outline">
        <i class="fa-solid fa-arrow-right"></i> <?= e(__('back')) ?>
      </a>
    </div>

    <form method="post">
      <?= csrf_input() ?>
      <input type="hidden" name="_op" value="save">
      <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">

      <div class="card form-page">
        <div class="card-body">
          <div class="form-row">
            <label class="form-label"><?= e(__('name')) ?><span class="req">*</span></label>
            <input type="text" name="name_ar" value="<?= e($row['name_ar']) ?>" required dir="rtl" class="form-control" placeholder="مثال: القانون التجاري">
          </div>

          <div class="form-grid-2">
            <div class="form-row">
              <label class="form-label"><?= e(__('slug')) ?></label>
              <input type="text" name="slug" value="<?= e($row['slug']) ?>" dir="ltr" class="form-control" placeholder="auto">
            </div>
            <div class="form-row">
              <label class="form-label"><?= e(__('sort_order')) ?></label>
              <input type="number" name="sort_order" value="<?= (int) $row['sort_order'] ?>" class="form-control" placeholder="0">
            </div>
          </div>

          <div class="form-row">
            <label class="form-label"><?= e(__('description')) ?></label>
            <textarea name="description_ar" rows="3" dir="rtl" class="form-control" placeholder="وصف اختياري للتصنيف..."><?= e($row['description_ar'] ?? '') ?></textarea>
          </div>

          <div class="form-row">
            <label class="login-checkbox">
              <input type="checkbox" name="is_active" value="1" <?= !empty($row['is_active']) ? 'checked' : '' ?>>
              <?= e(__('active')) ?>
            </label>
          </div>
        </div>

        <div class="form-page-actions">
          <a href="<?= e(bp_url('admin/categories.php')) ?>" class="btn btn-outline"><?= e(__('cancel')) ?></a>
          <button type="submit" class="btn btn-gold"><i class="fa-solid fa-floppy-disk"></i> <?= e(__('save')) ?></button>
        </div>
      </div>
    </form>

    <?php
    require BP_PARTIALS . '/footer.php';
    return;
}

// ── List view ────────────────────────────────────────────────────────────────
$categories = $pdo->query(
    'SELECT c.*, (SELECT COUNT(*) FROM blog_posts p WHERE p.category_id = c.id) AS posts_count
     FROM blog_categories c ORDER BY sort_order, name_ar'
)->fetchAll();

require BP_PARTIALS . '/header.php';
?>

<div class="page-header">
  <div>
    <div class="breadcrumb">
      <a href="<?= e(bp_url('admin/')) ?>"><i class="fa-solid fa-house"></i> <?= e(__('dashboard')) ?></a>
      <span class="sep">/</span>
      <span><?= e(__('blog_categories')) ?></span>
    </div>
    <h1 class="page-title"><?= e(__('blog_categories')) ?></h1>
  </div>
  <a href="?action=new" class="btn btn-gold">
    <i class="fa-solid fa-plus"></i> <?= e(__('add_new')) ?>
  </a>
</div>

<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th><?= e(__('title')) ?></th>
          <th><?= e(__('slug')) ?></th>
          <th>المقالات</th>
          <th><?= e(__('sort_order')) ?></th>
          <th><?= e(__('status')) ?></th>
          <th style="width: 110px;"><?= e(__('actions')) ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$categories): ?>
          <tr><td colspan="6" class="empty-state"><i class="fa-regular fa-folder-open"></i><?= e(__('no_records')) ?></td></tr>
        <?php else: foreach ($categories as $c): ?>
          <tr>
            <td><strong><?= e($c['name_ar']) ?></strong></td>
            <td><code style="font-size: 11px; color: var(--jpi-text-muted);"><?= e($c['slug']) ?></code></td>
            <td><?= (int) $c['posts_count'] ?></td>
            <td><?= (int) $c['sort_order'] ?></td>
            <td>
              <span class="badge <?= $c['is_active'] ? 'badge-success' : 'badge-muted' ?>">
                <?= e($c['is_active'] ? __('active') : __('inactive')) ?>
              </span>
            </td>
            <td>
              <div class="row-actions">
                <a href="?action=edit&id=<?= (int) $c['id'] ?>" class="btn btn-outline btn-sm btn-icon" title="<?= e(__('edit')) ?>"><i class="fa-solid fa-pen"></i></a>
                <form method="post" data-confirm="<?= e(__('confirm_delete')) ?>" style="display:inline;">
                  <?= csrf_input() ?>
                  <input type="hidden" name="_op" value="delete">
                  <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm btn-icon" title="<?= e(__('delete')) ?>"><i class="fa-solid fa-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require BP_PARTIALS . '/footer.php'; ?>
