<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
requireLogin();

$pageTitle = __('newsletter');
$current = 'newsletter';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_op'] ?? '') === 'delete') {
    csrf_check();
    $pdo->prepare('DELETE FROM NewsletterMails WHERE id = ?')->execute([(int) $_POST['id']]);
    log_activity($pdo, 'delete', 'newsletter', (int) $_POST['id']);
    flash('success', __('deleted_successfully'));
    redirect(bp_url('admin/newsletter.php'));
}

if (isset($_GET['export'])) {
    $rows = $pdo->query('SELECT Email, created_at FROM NewsletterMails ORDER BY id DESC')->fetchAll();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=newsletter-' . date('Y-m-d') . '.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Email', 'Subscribed at']);
    foreach ($rows as $r) fputcsv($out, [$r['Email'], $r['created_at']]);
    fclose($out);
    exit;
}

$rows = $pdo->query('SELECT * FROM NewsletterMails ORDER BY id DESC')->fetchAll();
$total = count($rows);

require BP_PARTIALS . '/header.php';
?>

<div class="page-header">
  <div>
    <div class="breadcrumb"><a href="<?= e(bp_url('admin/')) ?>"><?= e(__('dashboard')) ?></a> &middot; <?= e(__('newsletter')) ?></div>
    <h1 class="page-title"><?= e(__('newsletter')) ?></h1>
    <p class="page-subtitle">إجمالي <?= (int) $total ?> مشترك</p>
  </div>
  <a href="?export=csv" class="btn btn-gold"><i class="fa-solid fa-download"></i> <?= 'تصدير CSV' ?></a>
</div>

<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th><?= e(__('email')) ?></th>
          <th><?= e(__('created_at')) ?></th>
          <th style="width: 80px;"><?= e(__('actions')) ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="4" class="empty-state"><i class="fa-solid fa-paper-plane"></i><?= e(__('no_records')) ?></td></tr>
        <?php else: foreach ($rows as $i => $r): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><strong><?= e($r['Email']) ?></strong></td>
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
