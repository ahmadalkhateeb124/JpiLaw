<?php
/**
 * Generic CRUD scaffolding — page-based UI (Arabic-only).
 *
 * Pattern:
 *   ?action=list (default)   → table of records
 *   ?action=new              → blank form page
 *   ?action=edit&id=X        → form page pre-filled with row
 *
 * Bilingual DB columns (`_ar`/`_en`) are kept; on save the Arabic value is
 * mirrored into both columns.
 *
 * Field types: 'text', 'textarea', 'rich', 'image', 'icon', 'number',
 *              'select', 'email', 'phone', 'url', 'checkbox', 'date'.
 */

declare(strict_types=1);

function crud_run(PDO $pdo, array $cfg): void
{
    // header.php reads these from the local scope of whoever includes it.
    // We MUST define them inside this function so they reach header.php
    // when require'd below — otherwise the active nav highlight is lost.
    $pageTitle = $cfg['page_title'] ?? '';
    $current   = $cfg['current']    ?? '';

    $table        = $cfg['table'];
    $fields       = $cfg['fields'];
    $listColumns  = $cfg['list_columns'] ?? [];
    $hasSlug      = !empty($cfg['has_slug']);
    $hasActive    = $cfg['has_active'] ?? true;
    $hasOrder     = $cfg['has_order'] ?? true;
    $orderBy      = $cfg['order_by'] ?? ($hasOrder ? 'sort_order' : 'id DESC');
    $imgSubdir    = $cfg['upload_subdir'] ?? $table;
    $titleColumn  = $cfg['title_column'] ?? 'title';

    $action = $_GET['action'] ?? 'list';
    $id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'delete') {
        csrf_check();
        $delId = (int) $_POST['id'];
        $pdo->prepare("DELETE FROM `$table` WHERE id = ?")->execute([$delId]);
        log_activity($pdo, 'delete', $table, $delId);
        flash('success', __('deleted_successfully'));
        redirect(current_url());
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'save') {
        csrf_check();
        $editId = (int) ($_POST['id'] ?? 0);
        $cols = []; $vals = [];

        foreach ($fields as $f) {
            $name = $f['name'];
            $type = $f['type'];
            $bilingual = !empty($f['bilingual']);

            if ($type === 'image') {
                $existing = $_POST[$name . '_existing'] ?? '';
                $value = $existing;
                if (!empty($_FILES[$name]['name'])) {
                    $url = upload_image($_FILES[$name], $imgSubdir);
                    if ($url) $value = $url;
                }
                $cols[] = $name; $vals[] = $value;

            } elseif ($type === 'checkbox') {
                $cols[] = $name; $vals[] = !empty($_POST[$name]) ? 1 : 0;

            } elseif ($bilingual) {
                $arVal = trim((string) ($_POST[$name . '_ar'] ?? ''));
                $cols[] = $name . '_ar'; $vals[] = $arVal;
                $cols[] = $name . '_en'; $vals[] = $arVal;

            } else {
                $cols[] = $name; $vals[] = trim((string) ($_POST[$name] ?? ''));
            }
        }

        if ($hasSlug) {
            $slug = trim((string) ($_POST['slug'] ?? ''));
            if ($slug === '') {
                foreach ($fields as $f) {
                    if (!empty($f['bilingual']) && in_array($f['name'], ['name','title'], true)) {
                        $slug = slugify((string) ($_POST[$f['name'].'_ar'] ?? ''));
                        break;
                    }
                }
                if ($slug === '') $slug = slugify((string) reset($vals));
            }
            $slug = uniqueSlug($pdo, $table, $slug, $editId ?: null);
            $cols[] = 'slug'; $vals[] = $slug;
        }
        if ($hasOrder) {
            $cols[] = 'sort_order'; $vals[] = (int) ($_POST['sort_order'] ?? 0);
        }
        if ($hasActive) {
            $cols[] = 'is_active'; $vals[] = !empty($_POST['is_active']) ? 1 : 0;
        }

        if ($editId) {
            $set = implode(', ', array_map(fn($c) => "`$c` = ?", $cols));
            $vals[] = $editId;
            $pdo->prepare("UPDATE `$table` SET $set WHERE id = ?")->execute($vals);
            log_activity($pdo, 'update', $table, $editId);
        } else {
            $colsSql = implode(',', array_map(fn($c) => "`$c`", $cols));
            $ph      = implode(',', array_fill(0, count($cols), '?'));
            $pdo->prepare("INSERT INTO `$table` ($colsSql) VALUES ($ph)")->execute($vals);
            log_activity($pdo, 'create', $table, (int) $pdo->lastInsertId());
        }

        flash('success', __('saved_successfully'));
        redirect(current_url());
    }

    // ── Decide which view to render ──────────────────────────────────────────
    if ($action === 'new' || $action === 'edit') {
        $row = ['id' => 0];
        if ($hasSlug)   $row['slug'] = '';
        if ($hasActive) $row['is_active'] = 1;
        if ($hasOrder)  $row['sort_order'] = 0;
        foreach ($fields as $f) {
            if (!empty($f['bilingual'])) {
                $row[$f['name'] . '_ar'] = '';
                $row[$f['name'] . '_en'] = '';
            } else {
                $row[$f['name']] = $f['default'] ?? '';
            }
        }
        if ($action === 'edit' && $id) {
            $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch() ?: $row;
        }

        require BP_PARTIALS . '/header.php';
        crud_render_form($cfg, $action, $row);
        require BP_PARTIALS . '/footer.php';
        return;
    }

    // List view
    $rows = $pdo->query("SELECT * FROM `$table` ORDER BY $orderBy")->fetchAll();

    require BP_PARTIALS . '/header.php';
    crud_render_list($cfg, $rows, $listColumns);
    require BP_PARTIALS . '/footer.php';
}

/* ────────────────────────────────────────────────────────────────────────── */
function crud_render_list(array $cfg, array $rows, array $listColumns): void
{
    $pageTitle = $cfg['page_title'] ?? '';
    $titleCol  = $cfg['title_column'] ?? 'name';
    ?>
    <div class="page-header">
      <div>
        <div class="breadcrumb">
          <a href="<?= e(bp_url('admin/')) ?>"><i class="fa-solid fa-house"></i> <?= e(__('dashboard')) ?></a>
          <span class="sep">/</span>
          <span><?= e($pageTitle) ?></span>
        </div>
        <h1 class="page-title"><?= e($pageTitle) ?></h1>
        <?php if (!empty($cfg['subtitle'])): ?>
          <p class="page-subtitle"><?= e($cfg['subtitle']) ?></p>
        <?php endif; ?>
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
              <?php if (!empty($cfg['list_image'])): ?><th style="width: 70px;"></th><?php endif; ?>
              <th><?= e(__($titleCol === 'name' ? 'name' : 'title')) ?></th>
              <?php foreach ($listColumns as $col): ?>
                <th><?= e($col['label']) ?></th>
              <?php endforeach; ?>
              <?php if (!empty($cfg['has_active'])): ?><th><?= e(__('status')) ?></th><?php endif; ?>
              <th style="width: 110px;"><?= e(__('actions')) ?></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$rows): ?>
              <tr><td colspan="20" class="empty-state"><i class="fa-regular fa-folder-open"></i><?= e(__('no_records')) ?></td></tr>
            <?php else: foreach ($rows as $r): ?>
              <tr>
                <?php if (!empty($cfg['list_image'])):
                      $imgField = $cfg['list_image']; ?>
                  <td>
                    <?php if (!empty($r[$imgField])): ?>
                      <img src="<?= e($r[$imgField]) ?>" style="width: 50px; height: 40px; object-fit: cover; border-radius: 6px;">
                    <?php else: ?>
                      <div style="width: 50px; height: 40px; border-radius: 6px; background: var(--jpi-bg); display: flex; align-items: center; justify-content: center; color: var(--jpi-text-muted);"><i class="fa-regular fa-image"></i></div>
                    <?php endif; ?>
                  </td>
                <?php endif; ?>
                <td><strong><?= e(localized($r, $titleCol)) ?></strong></td>
                <?php foreach ($listColumns as $col):
                    $val = $r[$col['field']] ?? '';
                    if (!empty($col['date'])) $val = fmt_date($val);
                    ?>
                  <td><?= e((string) $val) ?></td>
                <?php endforeach; ?>
                <?php if (!empty($cfg['has_active'])): ?>
                  <td>
                    <span class="badge <?= $r['is_active'] ? 'badge-success' : 'badge-muted' ?>">
                      <?= e($r['is_active'] ? __('active') : __('inactive')) ?>
                    </span>
                  </td>
                <?php endif; ?>
                <td>
                  <div class="row-actions">
                    <a href="?action=edit&id=<?= (int) $r['id'] ?>" class="btn btn-outline btn-sm btn-icon" title="<?= e(__('edit')) ?>"><i class="fa-solid fa-pen"></i></a>
                    <form method="post" data-confirm="<?= e(__('confirm_delete')) ?>" style="display:inline;">
                      <?= csrf_input() ?>
                      <input type="hidden" name="_op" value="delete">
                      <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
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
    <?php
}

/* ────────────────────────────────────────────────────────────────────────── */
function crud_render_form(array $cfg, string $action, array $row): void
{
    $isEdit = ($action === 'edit' && !empty($row['id']));
    $pageTitle = $cfg['page_title'] ?? '';
    ?>
    <div class="page-header">
      <div>
        <div class="breadcrumb">
          <a href="<?= e(bp_url('admin/')) ?>"><i class="fa-solid fa-house"></i> <?= e(__('dashboard')) ?></a>
          <span class="sep">/</span>
          <a href="<?= e(current_url()) ?>"><?= e($pageTitle) ?></a>
          <span class="sep">/</span>
          <span><?= e($isEdit ? __('edit') : __('add_new')) ?></span>
        </div>
        <h1 class="page-title"><?= e($isEdit ? __('edit') : __('add_new')) ?> — <?= e($pageTitle) ?></h1>
      </div>
      <a href="<?= e(current_url()) ?>" class="btn btn-outline">
        <i class="fa-solid fa-arrow-right"></i> <?= e(__('back')) ?>
      </a>
    </div>

    <form method="post" enctype="multipart/form-data">
      <?= csrf_input() ?>
      <input type="hidden" name="_op" value="save">
      <input type="hidden" name="id" value="<?= (int) ($row['id'] ?? 0) ?>">

      <div class="card form-page">
        <div class="card-body">
          <?php
          $bilingualFields = array_filter($cfg['fields'], fn($f) => !empty($f['bilingual']));
          $regularFields   = array_filter($cfg['fields'], fn($f) => empty($f['bilingual']));
          ?>

          <?php foreach ($bilingualFields as $f):
              $name = $f['name'] . '_ar';
              $val  = $row[$name] ?? '';
              $req  = !empty($f['required']) ? ' required' : '';
              $reqMark = !empty($f['required']) ? '<span class="req">*</span>' : '';
              $label = $f['label_ar'] ?? $f['label'] ?? $f['name'];
              ?>
            <div class="form-row">
              <label class="form-label"><?= e($label) ?> <?= $reqMark ?></label>
              <?php if ($f['type'] === 'textarea' || $f['type'] === 'rich'): ?>
                <textarea name="<?= e($name) ?>" rows="<?= (int) ($f['rows'] ?? 4) ?>" dir="rtl" class="form-control"<?= $req ?>><?= e((string) $val) ?></textarea>
                <?php if ($f['type'] === 'rich'): ?><p class="form-help">يدعم HTML للتنسيق.</p><?php endif; ?>
              <?php else: ?>
                <input type="text" name="<?= e($name) ?>" value="<?= e((string) $val) ?>" dir="rtl" class="form-control"<?= $req ?>>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>

          <?php if ($regularFields): ?>
            <?php if ($bilingualFields): ?>
              <hr style="border: 0; border-top: 1px dashed var(--jpi-border); margin: 22px 0;">
            <?php endif; ?>
            <div class="form-grid-2">
              <?php foreach ($regularFields as $f):
                  $name = $f['name'];
                  $val  = $row[$name] ?? '';
                  $type = $f['type'];
                  ?>
                <?php if ($type === 'image'): ?>
                  <div class="form-row" style="grid-column: 1/-1;">
                    <label class="form-label"><?= e($f['label'] ?? __('image')) ?></label>
                    <?php if (!empty($val)): ?>
                      <img src="<?= e((string) $val) ?>" alt="" style="max-height: 100px; border: 1px solid var(--jpi-border-soft); border-radius: 6px; padding: 4px; background: #fff; margin-bottom: 8px; display: block;">
                      <input type="hidden" name="<?= e($name) ?>_existing" value="<?= e((string) $val) ?>">
                    <?php endif; ?>
                    <input type="file" name="<?= e($name) ?>" accept="image/*" class="form-control">
                  </div>

                <?php elseif ($type === 'checkbox'): ?>
                  <div class="form-row">
                    <label class="login-checkbox">
                      <input type="checkbox" name="<?= e($name) ?>" value="1" <?= !empty($val) ? 'checked' : '' ?>>
                      <?= e($f['label'] ?? $name) ?>
                    </label>
                  </div>

                <?php elseif ($type === 'textarea'): ?>
                  <div class="form-row" style="grid-column: 1/-1;">
                    <label class="form-label"><?= e($f['label'] ?? $name) ?></label>
                    <textarea name="<?= e($name) ?>" rows="<?= (int) ($f['rows'] ?? 3) ?>" class="form-control"><?= e((string) $val) ?></textarea>
                  </div>

                <?php elseif ($type === 'select'): ?>
                  <div class="form-row">
                    <label class="form-label"><?= e($f['label'] ?? $name) ?></label>
                    <select name="<?= e($name) ?>" class="form-select">
                      <?php foreach ($f['options'] as $optV => $optL): ?>
                        <option value="<?= e((string) $optV) ?>" <?= (string) $val === (string) $optV ? 'selected' : '' ?>><?= e($optL) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                <?php else: ?>
                  <div class="form-row">
                    <label class="form-label"><?= e($f['label'] ?? $name) ?></label>
                    <input type="<?= e($type === 'phone' ? 'tel' : $type) ?>" name="<?= e($name) ?>" value="<?= e((string) $val) ?>" class="form-control">
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>

              <?php if (!empty($cfg['has_slug'])): ?>
                <div class="form-row">
                  <label class="form-label"><?= e(__('slug')) ?></label>
                  <input type="text" name="slug" value="<?= e($row['slug'] ?? '') ?>" dir="ltr" class="form-control" placeholder="auto">
                </div>
              <?php endif; ?>
              <?php if (!empty($cfg['has_order'])): ?>
                <div class="form-row">
                  <label class="form-label"><?= e(__('sort_order')) ?></label>
                  <input type="number" name="sort_order" value="<?= (int) ($row['sort_order'] ?? 0) ?>" class="form-control">
                </div>
              <?php endif; ?>
            </div>

            <?php if (!empty($cfg['has_active'])): ?>
              <div class="form-row">
                <label class="login-checkbox">
                  <input type="checkbox" name="is_active" value="1" <?= !empty($row['is_active']) ? 'checked' : '' ?>>
                  <?= e(__('active')) ?>
                </label>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <div class="form-page-actions">
          <a href="<?= e(current_url()) ?>" class="btn btn-outline"><?= e(__('cancel')) ?></a>
          <button type="submit" class="btn btn-gold"><i class="fa-solid fa-floppy-disk"></i> <?= e(__('save')) ?></button>
        </div>
      </div>
    </form>
    <?php
}
