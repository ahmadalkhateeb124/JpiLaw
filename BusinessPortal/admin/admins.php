<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
requireLogin();
requireRole('superadmin');

$pageTitle = __('admins');
$current = 'admins';
$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'delete') {
    csrf_check();
    $delId = (int) $_POST['id'];
    if ($delId === (int) $_SESSION['admin_id']) {
        flash('error', 'لا يمكنك حذف حسابك.');
    } else {
        $pdo->prepare('DELETE FROM admins WHERE id = ?')->execute([$delId]);
        log_activity($pdo, 'delete', 'admin', $delId);
        flash('success', __('deleted_successfully'));
    }
    redirect(bp_url('admin/admins.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'save') {
    csrf_check();
    $editId   = (int) ($_POST['id'] ?? 0);
    $name     = trim((string) $_POST['full_name']);
    $email    = trim((string) $_POST['email']);
    $role     = in_array($_POST['role'] ?? 'admin', ['superadmin','admin','editor'], true) ? $_POST['role'] : 'admin';
    $active   = !empty($_POST['is_active']) ? 1 : 0;
    $password = (string) ($_POST['password'] ?? '');

    if ($editId) {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare('UPDATE admins SET full_name=?, email=?, role=?, is_active=?, password_hash=? WHERE id=?')
                ->execute([$name, $email, $role, $active, $hash, $editId]);
        } else {
            $pdo->prepare('UPDATE admins SET full_name=?, email=?, role=?, is_active=? WHERE id=?')
                ->execute([$name, $email, $role, $active, $editId]);
        }
        log_activity($pdo, 'update', 'admin', $editId);
    } else {
        $hash = password_hash($password ?: bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
        $pdo->prepare('INSERT INTO admins (full_name, email, password_hash, role, is_active) VALUES (?,?,?,?,?)')
            ->execute([$name, $email, $hash, $role, $active]);
        log_activity($pdo, 'create', 'admin', (int) $pdo->lastInsertId());
    }

    flash('success', __('saved_successfully'));
    redirect(bp_url('admin/admins.php'));
}

$row = ['id' => 0, 'full_name' => '', 'email' => '', 'role' => 'admin', 'is_active' => 1];
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
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
          <a href="<?= e(bp_url('admin/admins.php')) ?>"><?= e(__('admins')) ?></a>
          <span class="sep">/</span>
          <span><?= e($isEdit ? __('edit') : __('add_new')) ?></span>
        </div>
        <h1 class="page-title"><?= e($isEdit ? __('edit') : __('add_new')) ?> — <?= e(__('admins')) ?></h1>
      </div>
      <a href="<?= e(bp_url('admin/admins.php')) ?>" class="btn btn-outline">
        <i class="fa-solid fa-arrow-right"></i> <?= e(__('back')) ?>
      </a>
    </div>

    <form method="post">
      <?= csrf_input() ?>
      <input type="hidden" name="_op" value="save">
      <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">

      <div class="card form-page">
        <div class="card-body">
          <div class="form-grid-2">
            <div class="form-row">
              <label class="form-label"><?= e(__('name')) ?><span class="req">*</span></label>
              <input type="text" name="full_name" value="<?= e($row['full_name']) ?>" required class="form-control">
            </div>
            <div class="form-row">
              <label class="form-label"><?= e(__('email')) ?><span class="req">*</span></label>
              <input type="email" name="email" value="<?= e($row['email']) ?>" required dir="ltr" class="form-control">
            </div>
          </div>

          <div class="form-grid-2">
            <div class="form-row">
              <label class="form-label"><?= e(__('role')) ?></label>
              <select name="role" class="form-select">
                <option value="superadmin" <?= $row['role'] === 'superadmin' ? 'selected' : '' ?>>مسؤول رئيسي</option>
                <option value="admin"      <?= $row['role'] === 'admin' ? 'selected' : '' ?>>مسؤول</option>
                <option value="editor"     <?= $row['role'] === 'editor' ? 'selected' : '' ?>>محرر</option>
              </select>
            </div>
            <div class="form-row">
              <label class="form-label">
                <?= e(__('password')) ?>
                <?php if ($row['id']): ?><span style="font-weight: 400; color: var(--jpi-text-muted); font-size: 11px;">&nbsp;(اتركها فارغة لعدم التغيير)</span><?php else: ?><span class="req">*</span><?php endif; ?>
              </label>
              <input type="password" name="password" <?= $row['id'] ? '' : 'required' ?> dir="ltr" class="form-control">
            </div>
          </div>

          <div class="form-row">
            <label class="login-checkbox">
              <input type="checkbox" name="is_active" value="1" <?= !empty($row['is_active']) ? 'checked' : '' ?>>
              <?= e(__('active')) ?>
            </label>
          </div>
        </div>

        <div class="form-page-actions">
          <a href="<?= e(bp_url('admin/admins.php')) ?>" class="btn btn-outline"><?= e(__('cancel')) ?></a>
          <button type="submit" class="btn btn-gold"><i class="fa-solid fa-floppy-disk"></i> <?= e(__('save')) ?></button>
        </div>
      </div>
    </form>

    <?php
    require BP_PARTIALS . '/footer.php';
    return;
}

// ── List view ────────────────────────────────────────────────────────────────
$admins = $pdo->query('SELECT * FROM admins ORDER BY id')->fetchAll();
require BP_PARTIALS . '/header.php';
?>

<div class="page-header">
  <div>
    <div class="breadcrumb">
      <a href="<?= e(bp_url('admin/')) ?>"><i class="fa-solid fa-house"></i> <?= e(__('dashboard')) ?></a>
      <span class="sep">/</span>
      <span><?= e(__('admins')) ?></span>
    </div>
    <h1 class="page-title"><?= e(__('admins')) ?></h1>
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
          <th><?= e(__('name')) ?></th>
          <th><?= e(__('email')) ?></th>
          <th><?= e(__('role')) ?></th>
          <th>آخر دخول</th>
          <th><?= e(__('status')) ?></th>
          <th style="width: 110px;"><?= e(__('actions')) ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($admins as $a): ?>
          <tr>
            <td><strong><?= e($a['full_name']) ?></strong> <?php if ((int) $a['id'] === (int) $_SESSION['admin_id']): ?><span class="badge badge-gold" style="margin-inline-start: 6px;">أنت</span><?php endif; ?></td>
            <td><?= e($a['email']) ?></td>
            <td><span class="badge badge-gold"><?= e($a['role'] === 'superadmin' ? 'مسؤول رئيسي' : ($a['role'] === 'editor' ? 'محرر' : 'مسؤول')) ?></span></td>
            <td><?= e(fmt_date($a['last_login_at'])) ?></td>
            <td><span class="badge <?= $a['is_active'] ? 'badge-success' : 'badge-muted' ?>"><?= e($a['is_active'] ? __('active') : __('inactive')) ?></span></td>
            <td>
              <div class="row-actions">
                <a href="?action=edit&id=<?= (int) $a['id'] ?>" class="btn btn-outline btn-sm btn-icon"><i class="fa-solid fa-pen"></i></a>
                <?php if ((int) $a['id'] !== (int) $_SESSION['admin_id']): ?>
                  <form method="post" data-confirm="<?= e(__('confirm_delete')) ?>" style="display:inline;">
                    <?= csrf_input() ?>
                    <input type="hidden" name="_op" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                    <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require BP_PARTIALS . '/footer.php'; ?>
