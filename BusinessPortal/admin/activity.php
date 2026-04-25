x<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
requireLogin();

$pageTitle = __('activity_log');
$current = 'activity';

$rows = $pdo->query(
    'SELECT a.*, ad.full_name AS admin_name
     FROM activity_log a LEFT JOIN admins ad ON ad.id = a.admin_id
     ORDER BY a.id DESC LIMIT 200'
)->fetchAll();

require BP_PARTIALS . '/header.php';
?>

<div class="page-header">
  <div>
    <div class="breadcrumb"><a href="<?= e(bp_url('admin/')) ?>"><?= e(__('dashboard')) ?></a> &middot; <?= e(__('activity_log')) ?></div>
    <h1 class="page-title"><?= e(__('activity_log')) ?></h1>
  </div>
</div>

<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th><?= e(__('created_at')) ?></th>
          <th><?= e(__('name')) ?></th>
          <th><?= 'الإجراء' ?></th>
          <th><?= 'الكيان' ?></th>
          <th>IP</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="5" class="empty-state"><i class="fa-regular fa-clock"></i><?= e(__('no_records')) ?></td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td><?= e(fmt_date($r['created_at'])) ?></td>
            <td><?= e($r['admin_name'] ?? '—') ?></td>
            <td><span class="badge badge-gold"><?= e($r['action']) ?></span></td>
            <td><code style="font-size: 11px;"><?= e($r['entity_type'] ?? '') ?>#<?= (int) $r['entity_id'] ?></code></td>
            <td style="font-family: monospace; font-size: 11px; color: var(--jpi-text-muted);"><?= e($r['ip_address']) ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require BP_PARTIALS . '/footer.php'; ?>
