<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
requireLogin();

$pageTitle = __('appointments');
$current = 'appointments';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'delete') {
    csrf_check();
    $pdo->prepare('DELETE FROM appointments WHERE id = ?')->execute([(int) $_POST['id']]);
    flash('success', __('deleted_successfully'));
    redirect(bp_url('admin/appointments.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'status') {
    csrf_check();
    $pdo->prepare('UPDATE appointments SET status = ?, admin_notes = ? WHERE id = ?')
        ->execute([$_POST['status'], $_POST['admin_notes'] ?? '', (int) $_POST['id']]);
    flash('success', __('saved_successfully'));
    redirect(bp_url('admin/appointments.php'));
}

$rows = $pdo->query('SELECT * FROM appointments ORDER BY id DESC LIMIT 200')->fetchAll();

$statusLabels = [
    'new'        => 'جديد',
    'contacted'  => 'تم التواصل',
    'scheduled'  => 'مُجدول',
    'completed'  => 'مُكتمل',
    'cancelled'  => 'مُلغى',
];
$statusBadge = ['new'=>'badge-gold','contacted'=>'badge-warning','scheduled'=>'badge-success','completed'=>'badge-muted','cancelled'=>'badge-danger'];

require BP_PARTIALS . '/header.php';
?>

<div class="page-header">
  <div>
    <div class="breadcrumb"><a href="<?= e(bp_url('admin/')) ?>"><?= e(__('dashboard')) ?></a> &middot; <?= e(__('appointments')) ?></div>
    <h1 class="page-title"><?= e(__('appointments')) ?></h1>
  </div>
</div>

<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th><?= e(__('name')) ?></th>
          <th><?= e(__('email')) ?> / <?= e(__('phone')) ?></th>
          <th><?= e(__('subject')) ?></th>
          <th><?= 'الموعد المطلوب' ?></th>
          <th><?= e(__('status')) ?></th>
          <th><?= e(__('created_at')) ?></th>
          <th style="width: 80px;"><?= e(__('actions')) ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="7" class="empty-state"><i class="fa-regular fa-calendar"></i><?= e(__('no_records')) ?></td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td><strong><?= e($r['full_name']) ?></strong></td>
            <td>
              <a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a><br>
              <span style="color: var(--jpi-text-muted); font-size: 11px;"><?= e($r['phone'] ?? '—') ?></span>
            </td>
            <td><?= e(mb_strimwidth((string) $r['subject'], 0, 40, '…')) ?></td>
            <td>
              <?= e($r['preferred_date'] ?? '—') ?>
              <?php if (!empty($r['preferred_time'])): ?><br><span style="font-size: 11px; color: var(--jpi-text-muted);"><?= e(substr((string) $r['preferred_time'], 0, 5)) ?></span><?php endif; ?>
            </td>
            <td>
              <form method="post" style="display:inline;">
                <?= csrf_input() ?>
                <input type="hidden" name="_op" value="status">
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <input type="hidden" name="admin_notes" value="<?= e($r['admin_notes'] ?? '') ?>">
                <select name="status" onchange="this.form.submit()" class="form-select" style="padding: 4px 8px; font-size: 12px;">
                  <?php foreach ($statusLabels as $k => $v): ?>
                    <option value="<?= e($k) ?>" <?= $r['status'] === $k ? 'selected' : '' ?>><?= e($v) ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            </td>
            <td><?= e(fmt_date($r['created_at'])) ?></td>
            <td>
              <form method="post" data-confirm="<?= e(__('confirm_delete')) ?>" style="display:inline;">
                <?= csrf_input() ?>
                <input type="hidden" name="_op" value="delete">
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require BP_PARTIALS . '/footer.php'; ?>
