<?php
/** Inquiries received through the website. */
require __DIR__ . '/_admin.php';
$user = require_admin();

$statuses = inquiry_statuses();
$status = isset($_GET['status']) && isset($statuses[$_GET['status']]) ? $_GET['status'] : '';
$page = max(1, isset($_GET['page']) ? (int) $_GET['page'] : 1);
$perPage = 25;

$counts = ['' => 0];
foreach (db_all('SELECT status, COUNT(*) AS n FROM inquiries GROUP BY status') as $row) {
    $counts[$row['status']] = (int) $row['n'];
    $counts[''] += (int) $row['n'];
}

$where = $status !== '' ? 'WHERE status = ?' : '';
$params = $status !== '' ? [$status] : [];
$total = isset($counts[$status]) ? $counts[$status] : 0;
$pages = max(1, (int) ceil($total / $perPage));
$page = min($page, $pages);
$offset = ($page - 1) * $perPage;

// LIMIT values are cast to int above, so they are safe to place in the SQL.
$rows = db_all(
    "SELECT id, full_name, organization, email, system_name, inquiry_type, status, created_at
       FROM inquiries $where
      ORDER BY created_at DESC, id DESC
      LIMIT $perPage OFFSET $offset",
    $params
);
$types = inquiry_types();

admin_header('Inquiries', $user, 'inquiries');
?>
<div class="admin-bar">
  <h1>Inquiries</h1>
  <p class="admin-bar__meta"><?= $counts[''] ?> received, <?= isset($counts['new']) ? $counts['new'] : 0 ?> new</p>
</div>

<nav class="admin-tabs" aria-label="Filter by status">
  <a href="<?= e(admin_url('index.php')) ?>"<?= $status === '' ? ' aria-current="true"' : '' ?>>All (<?= $counts[''] ?>)</a>
<?php foreach ($statuses as $key => $label): ?>
  <a href="<?= e(admin_url('index.php', ['status' => $key])) ?>"<?= $status === $key ? ' aria-current="true"' : '' ?>><?= e($label) ?> (<?= isset($counts[$key]) ? $counts[$key] : 0 ?>)</a>
<?php endforeach; ?>
</nav>

<?php if (!$rows): ?>
<div class="admin-empty">
  <h2>No inquiries here yet</h2>
  <p>When a visitor sends the form on the website, the inquiry appears in this list.</p>
</div>
<?php else: ?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr><th scope="col">Ref.</th><th scope="col">Received</th><th scope="col">From</th><th scope="col">System</th><th scope="col">About</th><th scope="col">Status</th></tr>
    </thead>
    <tbody>
<?php foreach ($rows as $r): ?>
      <tr>
        <td data-label="Ref."><a href="<?= e(admin_url('inquiry.php', ['id' => $r['id']])) ?>"><?= sprintf('%06d', $r['id']) ?></a></td>
        <td data-label="Received"><?= e(date('M j, Y g:i A', strtotime($r['created_at']))) ?></td>
        <td data-label="From"><strong><?= e($r['full_name']) ?></strong><?= $r['organization'] ? '<br>' . e($r['organization']) : '' ?></td>
        <td data-label="System"><?= e($r['system_name'] ?: 'Not sure yet') ?></td>
        <td data-label="About"><?= e(isset($types[$r['inquiry_type']]) ? $types[$r['inquiry_type']] : $r['inquiry_type']) ?></td>
        <td data-label="Status"><span class="status status--<?= e($r['status']) ?>"><?= e(isset($statuses[$r['status']]) ? $statuses[$r['status']] : $r['status']) ?></span></td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php if ($pages > 1): ?>
<nav class="admin-pager" aria-label="Pages">
<?php if ($page > 1): ?><a class="btn btn--line btn--sm" href="<?= e(admin_url('index.php', array_filter(['status' => $status, 'page' => $page - 1]))) ?>">Newer</a><?php endif; ?>
  <span>Page <?= $page ?> of <?= $pages ?></span>
<?php if ($page < $pages): ?><a class="btn btn--line btn--sm" href="<?= e(admin_url('index.php', array_filter(['status' => $status, 'page' => $page + 1]))) ?>">Older</a><?php endif; ?>
</nav>
<?php endif; ?>
<?php endif; ?>
<?php admin_footer(); ?>
