<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
requireLogin();

$pageTitle = __('inquiries');
$current = 'inquiries';
$action = $_GET['action'] ?? 'list';
$id     = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'delete') {
    csrf_check();
    $pdo->prepare('DELETE FROM Inquiries WHERE id = ?')->execute([(int) $_POST['id']]);
    log_activity($pdo, 'delete', 'inquiry', (int) $_POST['id']);
    flash('success', __('deleted_successfully'));
    redirect(bp_url('admin/inquiries.php'));
}

// ── View page ────────────────────────────────────────────────────────────────
if ($action === 'view' && $id) {
    $stmt = $pdo->prepare('SELECT * FROM Inquiries WHERE id = ?');
    $stmt->execute([$id]);
    $inq = $stmt->fetch();
    if (!$inq) {
        flash('error', __('error_occurred'));
        redirect(bp_url('admin/inquiries.php'));
    }
    if (empty($inq['is_read'])) {
        $pdo->prepare('UPDATE Inquiries SET is_read = 1 WHERE id = ?')->execute([$id]);
    }

    require BP_PARTIALS . '/header.php';
    ?>
    <div class="page-header">
      <div>
        <div class="breadcrumb">
          <a href="<?= e(bp_url('admin/')) ?>"><i class="fa-solid fa-house"></i> <?= e(__('dashboard')) ?></a>
          <span class="sep">/</span>
          <a href="<?= e(bp_url('admin/inquiries.php')) ?>"><?= e(__('inquiries')) ?></a>
          <span class="sep">/</span>
          <span><?= e($inq['Subject'] ?: '(بدون موضوع)') ?></span>
        </div>
        <h1 class="page-title"><?= e($inq['Subject'] ?: '(بدون موضوع)') ?></h1>
        <p class="page-subtitle">من <?= e($inq['FullName']) ?> &middot; <?= e(fmt_date($inq['created_at'], 'l, j F Y - H:i')) ?></p>
      </div>
      <a href="<?= e(bp_url('admin/inquiries.php')) ?>" class="btn btn-outline">
        <i class="fa-solid fa-arrow-right"></i> <?= e(__('back')) ?>
      </a>
    </div>

    <div class="card form-page">
      <div class="card-body">
        <div style="display: grid; grid-template-columns: 130px 1fr; gap: 12px 20px; padding: 18px; background: #fcfaf6; border-radius: 10px; margin-bottom: 22px; font-size: 14px;">
          <strong><?= e(__('name')) ?>:</strong>     <span><?= e($inq['FullName']) ?></span>
          <strong><?= e(__('email')) ?>:</strong>    <span><a href="mailto:<?= e($inq['Email']) ?>"><?= e($inq['Email']) ?></a></span>
          <strong><?= e(__('phone')) ?>:</strong>    <span><?= e($inq['Mobile'] ?: '—') ?></span>
          <strong><?= e(__('subject')) ?>:</strong>  <span><?= e($inq['Subject'] ?: '—') ?></span>
        </div>

        <div>
          <strong style="display: block; margin-bottom: 10px; font-size: 14px;"><?= e(__('message')) ?>:</strong>
          <div style="white-space: pre-wrap; line-height: 1.8; padding: 18px; background: #fff; border: 1px solid var(--jpi-border-soft); border-radius: 10px; font-size: 14.5px;"><?= e($inq['Msg']) ?></div>
        </div>
      </div>

      <div class="form-page-actions">
        <form method="post" data-confirm="<?= e(__('confirm_delete')) ?>" style="display:inline;">
          <?= csrf_input() ?>
          <input type="hidden" name="_op" value="delete">
          <input type="hidden" name="id" value="<?= (int) $inq['id'] ?>">
          <button class="btn btn-danger"><i class="fa-solid fa-trash"></i> <?= e(__('delete')) ?></button>
        </form>
      </div>
    </div>

    <?php
    require BP_PARTIALS . '/footer.php';
    return;
}

// ── List view ────────────────────────────────────────────────────────────────
$rows = $pdo->query('SELECT * FROM Inquiries ORDER BY id DESC LIMIT 200')->fetchAll();
require BP_PARTIALS . '/header.php';
?>

<div class="page-header">
  <div>
    <div class="breadcrumb">
      <a href="<?= e(bp_url('admin/')) ?>"><i class="fa-solid fa-house"></i> <?= e(__('dashboard')) ?></a>
      <span class="sep">/</span>
      <span><?= e(__('inquiries')) ?></span>
    </div>
    <h1 class="page-title"><?= e(__('inquiries')) ?></h1>
  </div>
</div>

<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th><?= e(__('name')) ?></th>
          <th><?= e(__('email')) ?></th>
          <th><?= e(__('subject')) ?></th>
          <th><?= e(__('created_at')) ?></th>
          <th><?= e(__('status')) ?></th>
          <th style="width: 110px;"><?= e(__('actions')) ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="6" class="empty-state"><i class="fa-regular fa-envelope-open"></i><?= e(__('no_records')) ?></td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr style="<?= empty($r['is_read']) ? 'background: rgba(159,128,84,0.04);' : '' ?>">
            <td><strong><?= e($r['FullName']) ?></strong></td>
            <td><?= e($r['Email']) ?></td>
            <td><?= e(mb_strimwidth((string) $r['Subject'], 0, 40, '…')) ?></td>
            <td><?= e(fmt_date($r['created_at'])) ?></td>
            <td>
              <span class="badge <?= empty($r['is_read']) ? 'badge-gold' : 'badge-muted' ?>">
                <?= empty($r['is_read']) ? 'جديد' : 'مقروء' ?>
              </span>
            </td>
            <td>
              <div class="row-actions">
                <a href="?action=view&id=<?= (int) $r['id'] ?>" class="btn btn-outline btn-sm btn-icon" title="<?= e(__('view')) ?>"><i class="fa-solid fa-eye"></i></a>
                <form method="post" data-confirm="<?= e(__('confirm_delete')) ?>" style="display:inline;">
                  <?= csrf_input() ?>
                  <input type="hidden" name="_op" value="delete">
                  <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                  <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
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
