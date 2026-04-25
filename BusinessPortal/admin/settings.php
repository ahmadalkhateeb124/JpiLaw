<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
requireLogin();

$pageTitle = __('site_settings');
$current = 'settings';

// ── Save ─────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $values = $_POST['value_ar'] ?? [];

    $upd = $pdo->prepare('UPDATE site_settings SET value_ar = ?, value_en = ? WHERE `key` = ?');

    foreach ($values as $key => $val) {
        // Mirror the Arabic value into _en column for forward-compat.
        $upd->execute([$val, $val, $key]);
    }

    if (!empty($_FILES['image_files']['name'])) {
        foreach ($_FILES['image_files']['name'] as $key => $name) {
            if (empty($name)) continue;
            $f = [
                'name'     => $_FILES['image_files']['name'][$key],
                'type'     => $_FILES['image_files']['type'][$key],
                'tmp_name' => $_FILES['image_files']['tmp_name'][$key],
                'error'    => $_FILES['image_files']['error'][$key],
                'size'     => $_FILES['image_files']['size'][$key],
            ];
            $url = upload_image($f, 'settings');
            if ($url) $upd->execute([$url, $url, $key]);
        }
    }

    log_activity($pdo, 'settings_update', 'site_settings', null, 'Updated site settings');
    flash('success', __('saved_successfully'));
    redirect(bp_url('admin/settings.php'));
}

$rows = $pdo->query('SELECT * FROM site_settings ORDER BY `group`, sort_order, `key`')->fetchAll();
$groups = [];
foreach ($rows as $r) {
    $groups[$r['group']][] = $r;
}

$groupLabels = [
    'general' => 'عام',
    'contact' => 'الاتصال',
    'social'  => 'وسائل التواصل',
    'seo'     => 'تحسين البحث',
];
$groupIcons = [
    'general' => 'fa-circle-info',
    'contact' => 'fa-phone',
    'social'  => 'fa-share-nodes',
    'seo'     => 'fa-magnifying-glass',
];

$activeGroup = $_GET['g'] ?? array_key_first($groups);

require BP_PARTIALS . '/header.php';
?>

<div class="page-header">
  <div>
    <div class="breadcrumb">
      <a href="<?= e(bp_url('admin/')) ?>"><i class="fa-solid fa-house"></i> <?= e(__('dashboard')) ?></a>
      <span class="sep">/</span>
      <span><?= e(__('site_settings')) ?></span>
    </div>
    <h1 class="page-title"><?= e(__('site_settings')) ?></h1>
    <p class="page-subtitle">تحكّم في كل بيانات الموقع من مكان واحد.</p>
  </div>
</div>

<form method="post" enctype="multipart/form-data" style="display: grid; grid-template-columns: 240px 1fr; gap: 20px;" class="settings-grid">
  <?= csrf_input() ?>

  <aside class="card" style="padding: 8px; height: fit-content; position: sticky; top: calc(var(--header-h) + var(--nav-h) + 16px);">
    <?php foreach ($groups as $gKey => $_): ?>
      <a href="?g=<?= e($gKey) ?>" style="display: flex; align-items: center; gap: 10px; padding: 11px 14px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; color: <?= $activeGroup === $gKey ? 'var(--jpi-cream)' : 'var(--jpi-text)' ?>; background: <?= $activeGroup === $gKey ? 'var(--jpi-dark)' : 'transparent' ?>; margin-bottom: 4px; transition: all .25s;">
        <i class="fa-solid <?= e($groupIcons[$gKey] ?? 'fa-folder') ?>" style="width: 18px;"></i>
        <?= e($groupLabels[$gKey] ?? ucfirst($gKey)) ?>
      </a>
    <?php endforeach; ?>
  </aside>

  <div>
    <?php foreach ($groups as $gKey => $items):
        if ($gKey !== $activeGroup) continue; ?>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fa-solid <?= e($groupIcons[$gKey] ?? 'fa-folder') ?>"></i>
            <?= e($groupLabels[$gKey] ?? ucfirst($gKey)) ?>
          </h3>
          <button type="submit" class="btn btn-gold btn-sm"><i class="fa-solid fa-floppy-disk"></i> <?= e(__('save')) ?></button>
        </div>
        <div class="card-body">
          <?php foreach ($items as $s): ?>
            <div class="form-row">
              <label class="form-label"><?= e($s['label_ar']) ?>
                <span style="color: var(--jpi-text-muted); font-weight: 400; font-size: 11px;"><code><?= e($s['key']) ?></code></span>
              </label>

              <?php if ($s['type'] === 'image'): ?>
                <?php if (!empty($s['value_ar'])): ?>
                  <div style="margin-bottom: 8px;">
                    <img src="<?= e(strpos($s['value_ar'], 'http') === 0 ? $s['value_ar'] : SITE_URL . $s['value_ar']) ?>" alt="" style="max-height: 80px; border: 1px solid var(--jpi-border-soft); border-radius: 6px; padding: 6px; background: #fff;">
                  </div>
                <?php endif; ?>
                <input type="file" name="image_files[<?= e($s['key']) ?>]" accept="image/*" class="form-control">
                <input type="hidden" name="value_ar[<?= e($s['key']) ?>]" value="<?= e($s['value_ar']) ?>">

              <?php elseif ($s['type'] === 'textarea'): ?>
                <textarea name="value_ar[<?= e($s['key']) ?>]" class="form-control" rows="3" dir="rtl"><?= e($s['value_ar']) ?></textarea>

              <?php elseif (in_array($s['type'], ['email','url','phone','number'], true)): ?>
                <input type="<?= $s['type'] === 'phone' ? 'tel' : ($s['type'] === 'number' ? 'number' : $s['type']) ?>"
                       name="value_ar[<?= e($s['key']) ?>]"
                       value="<?= e($s['value_ar']) ?>" class="form-control" dir="ltr">

              <?php else: ?>
                <input type="text" name="value_ar[<?= e($s['key']) ?>]" value="<?= e($s['value_ar']) ?>" class="form-control" dir="rtl">
              <?php endif; ?>
            </div>
          <?php endforeach; ?>

          <div style="text-align: end; padding-top: 12px; border-top: 1px solid var(--jpi-border-soft); margin-top: 14px;">
            <button type="submit" class="btn btn-gold"><i class="fa-solid fa-floppy-disk"></i> <?= e(__('save')) ?></button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</form>

<style>
@media (max-width: 768px) { .settings-grid { grid-template-columns: 1fr !important; } }
</style>

<?php require BP_PARTIALS . '/footer.php'; ?>
