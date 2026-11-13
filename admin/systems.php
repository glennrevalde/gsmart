<?php
/** Catalog administration: publish, hide, feature, reorder and edit systems. */
require __DIR__ . '/_admin.php';
$user = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $system = db_one('SELECT id, name, is_published, is_featured FROM systems WHERE id = ?', [$id]);
    if ($system) {
        if ($action === 'toggle_publish') {
            db_run('UPDATE systems SET is_published = ?, updated_at = ? WHERE id = ?', [$system['is_published'] ? 0 : 1, now(), $id]);
            flash('notice', $system['name'] . ($system['is_published'] ? ' is now hidden from the public site.' : ' is now published.'));
        } elseif ($action === 'toggle_feature') {
            db_run('UPDATE systems SET is_featured = ?, updated_at = ? WHERE id = ?', [$system['is_featured'] ? 0 : 1, now(), $id]);
            flash('notice', $system['name'] . ($system['is_featured'] ? ' was removed from the home page grid.' : ' was added to the home page grid.'));
        }
    }
    redirect(admin_url('systems.php'));
}

$rows = db_all(
    'SELECT s.id, s.slug, s.name, s.category, s.is_published, s.is_featured, s.sort_order, c.name AS sector_name
       FROM systems s JOIN sectors c ON c.id = s.sector_id
      ORDER BY s.sort_order, s.name'
);
$published = count(array_filter($rows, function ($r) { return $r['is_published']; }));

admin_header('Systems', $user, 'systems');
?>
<div class="admin-bar">
  <div>
    <h1>Systems</h1>
    <p class="admin-bar__meta"><?= count($rows) ?> in the catalog, <?= $published ?> published</p>
  </div>
  <a class="btn btn--primary btn--sm" href="<?= e(admin_url('system-edit.php')) ?>">Add a system</a>
</div>
<p class="admin-lead">Only published systems appear on the public site, in the inquiry form and in the sitemap. Hide anything GSMaRT has not confirmed it offers.</p>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr><th scope="col">Order</th><th scope="col">System</th><th scope="col">Industry group</th><th scope="col">Public site</th><th scope="col">Home page grid</th><th scope="col"><span class="visually-hidden">Actions</span></th></tr>
    </thead>
    <tbody>
<?php foreach ($rows as $r): ?>
      <tr>
        <td data-label="Order"><?= (int) $r['sort_order'] ?></td>
        <td data-label="System"><strong><?= e($r['name']) ?></strong><br><span class="admin-dim"><?= e($r['category']) ?></span></td>
        <td data-label="Industry group"><?= e($r['sector_name']) ?></td>
        <td data-label="Public site">
          <form method="post" action="<?= e(admin_url('systems.php')) ?>" class="admin-inline">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <span class="status status--<?= $r['is_published'] ? 'on' : 'off' ?>"><?= $r['is_published'] ? 'Published' : 'Hidden' ?></span>
            <button class="admin-link" type="submit" name="action" value="toggle_publish"><?= $r['is_published'] ? 'Hide' : 'Publish' ?><span class="visually-hidden"> <?= e($r['name']) ?></span></button>
          </form>
        </td>
        <td data-label="Home page grid">
          <form method="post" action="<?= e(admin_url('systems.php')) ?>" class="admin-inline">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <span><?= $r['is_featured'] ? 'Shown' : 'Not shown' ?></span>
            <button class="admin-link" type="submit" name="action" value="toggle_feature"><?= $r['is_featured'] ? 'Remove' : 'Show' ?><span class="visually-hidden"> <?= e($r['name']) ?> on the home page</span></button>
          </form>
        </td>
        <td data-label="Actions" class="admin-actions">
          <a href="<?= e(admin_url('system-edit.php', ['id' => $r['id']])) ?>">Edit<span class="visually-hidden"> <?= e($r['name']) ?></span></a>
<?php if ($r['is_published']): ?>
          <a href="<?= e(url('system', $r['slug'])) ?>">View<span class="visually-hidden"> <?= e($r['name']) ?> on the site</span></a>
<?php endif; ?>
        </td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php admin_footer(); ?>
