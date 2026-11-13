<?php
/** Add or edit one system, including its modules, workflow and roles. */
require __DIR__ . '/_admin.php';
$user = require_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$sectors = catalog_sectors();
$errors = [];

/** "Name | Description" lines to rows. */
function lines_to_pairs($text)
{
    $pairs = [];
    foreach (preg_split('/\R/', (string) $text) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $parts = explode('|', $line, 2);
        $pairs[] = [mb_substr(trim($parts[0]), 0, 120), mb_substr(isset($parts[1]) ? trim($parts[1]) : '', 0, 500)];
    }
    return $pairs;
}

function pairs_to_lines(array $rows, $a, $b)
{
    $lines = [];
    foreach ($rows as $row) {
        $lines[] = $row[$a] . ($row[$b] !== '' ? ' | ' . $row[$b] : '');
    }
    return implode("\n", $lines);
}

function slugify($text)
{
    $text = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $text), '-'));
    return mb_substr($text, 0, 120);
}

if ($id) {
    $system = db_one('SELECT * FROM systems WHERE id = ?', [$id]);
    if (!$system) {
        http_response_code(404);
        admin_header('System not found', $user, 'systems');
        echo '<div class="admin-empty"><h1>System not found</h1><p><a href="' . e(admin_url('systems.php')) . '">Back to systems</a></p></div>';
        admin_footer();
        exit;
    }
    $form = $system + [
        'modules_text' => pairs_to_lines(db_all('SELECT name, description FROM system_modules WHERE system_id = ? ORDER BY sort_order, id', [$id]), 'name', 'description'),
        'steps_text'   => pairs_to_lines(db_all('SELECT label, detail FROM system_workflow_steps WHERE system_id = ? ORDER BY sort_order, id', [$id]), 'label', 'detail'),
        'roles_text'   => pairs_to_lines(db_all('SELECT role_name, description FROM system_roles WHERE system_id = ? ORDER BY sort_order, id', [$id]), 'role_name', 'description'),
    ];
} else {
    $next = db_one('SELECT COALESCE(MAX(sort_order), 0) + 10 AS n FROM systems');
    $form = [
        'name' => '', 'slug' => '', 'sector_id' => $sectors ? $sectors[0]['id'] : 0, 'category' => '', 'tagline' => '',
        'summary' => '', 'audience' => '', 'manages' => '', 'search_terms' => '', 'implementation_notes' => '',
        'platforms' => '', 'deployment' => '', 'integrations' => '', 'pricing' => '',
        'is_published' => 0, 'is_featured' => 0, 'sort_order' => (int) $next['n'],
        'modules_text' => '', 'steps_text' => '', 'roles_text' => '',
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    if ($id && isset($_POST['action']) && $_POST['action'] === 'delete') {
        if (empty($_POST['confirm_delete'])) {
            flash('error', 'Tick the box to confirm before deleting.');
            redirect(admin_url('system-edit.php', ['id' => $id]));
        }
        db_run('DELETE FROM systems WHERE id = ?', [$id]);   // modules, steps and roles go with it
        flash('notice', $system['name'] . ' was deleted. Past inquiries keep the system name.');
        redirect(admin_url('systems.php'));
    }

    $text = function ($key, $max) {
        return mb_substr(trim(isset($_POST[$key]) ? (string) $_POST[$key] : ''), 0, $max);
    };
    $form = [
        'name' => $text('name', 150), 'slug' => $text('slug', 120), 'sector_id' => (int) (isset($_POST['sector_id']) ? $_POST['sector_id'] : 0),
        'category' => $text('category', 80), 'tagline' => $text('tagline', 255), 'summary' => $text('summary', 600),
        'audience' => $text('audience', 1000), 'manages' => $text('manages', 1000), 'search_terms' => $text('search_terms', 255),
        'implementation_notes' => $text('implementation_notes', 3000),
        'platforms' => $text('platforms', 255), 'deployment' => $text('deployment', 255),
        'integrations' => $text('integrations', 255), 'pricing' => $text('pricing', 255),
        'is_published' => empty($_POST['is_published']) ? 0 : 1, 'is_featured' => empty($_POST['is_featured']) ? 0 : 1,
        'sort_order' => (int) (isset($_POST['sort_order']) ? $_POST['sort_order'] : 0),
        'modules_text' => $text('modules_text', 6000), 'steps_text' => $text('steps_text', 6000), 'roles_text' => $text('roles_text', 6000),
    ];
    if ($form['slug'] === '') {
        $form['slug'] = slugify($form['name']);
    }

    if ($form['name'] === '') { $errors['name'] = 'Enter the system name.'; }
    if (!preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $form['slug'])) {
        $errors['slug'] = 'Use lowercase letters, numbers and single hyphens, for example laundry-management.';
    } elseif (db_one('SELECT id FROM systems WHERE slug = ? AND id <> ?', [$form['slug'], $id])) {
        $errors['slug'] = 'Another system already uses this address.';
    }
    if (!in_array($form['sector_id'], array_map('intval', array_column($sectors, 'id')), true)) { $errors['sector_id'] = 'Choose an industry group.'; }
    if ($form['category'] === '') { $errors['category'] = 'Enter the industry label shown on the card.'; }
    if ($form['summary'] === '') { $errors['summary'] = 'Write the one or two sentences shown on the card.'; }
    $modules = lines_to_pairs($form['modules_text']);
    $steps   = lines_to_pairs($form['steps_text']);
    $roles   = lines_to_pairs($form['roles_text']);
    if ($form['is_published'] && count($modules) < 3) { $errors['modules_text'] = 'List at least three modules before publishing.'; }

    if (!$errors) {
        $nullable = function ($v) { return $v === '' ? null : $v; };
        $pdo = db();
        $pdo->beginTransaction();
        try {
            $values = [
                $form['sector_id'], $form['slug'], $form['name'], $form['category'], $form['tagline'], $form['summary'],
                $form['audience'], $form['manages'], $form['search_terms'], $nullable($form['implementation_notes']),
                $nullable($form['platforms']), $nullable($form['deployment']), $nullable($form['integrations']), $nullable($form['pricing']),
                $form['is_published'], $form['is_featured'], $form['sort_order'], now(),
            ];
            if ($id) {
                db_run(
                    'UPDATE systems SET sector_id = ?, slug = ?, name = ?, category = ?, tagline = ?, summary = ?, audience = ?, manages = ?,
                            search_terms = ?, implementation_notes = ?, platforms = ?, deployment = ?, integrations = ?, pricing = ?,
                            is_published = ?, is_featured = ?, sort_order = ?, updated_at = ?
                      WHERE id = ?',
                    array_merge($values, [$id])
                );
                db_run('DELETE FROM system_modules WHERE system_id = ?', [$id]);
                db_run('DELETE FROM system_workflow_steps WHERE system_id = ?', [$id]);
                db_run('DELETE FROM system_roles WHERE system_id = ?', [$id]);
                $systemId = $id;
            } else {
                db_run(
                    'INSERT INTO systems (sector_id, slug, name, category, tagline, summary, audience, manages, search_terms,
                            implementation_notes, platforms, deployment, integrations, pricing, is_published, is_featured, sort_order, updated_at, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    array_merge($values, [now()])
                );
                $systemId = (int) $pdo->lastInsertId();
            }
            foreach ($modules as $n => $p) {
                db_run('INSERT INTO system_modules (system_id, name, description, sort_order) VALUES (?, ?, ?, ?)', [$systemId, $p[0], $p[1], ($n + 1) * 10]);
            }
            foreach ($steps as $n => $p) {
                db_run('INSERT INTO system_workflow_steps (system_id, label, detail, sort_order) VALUES (?, ?, ?, ?)', [$systemId, $p[0], $p[1], ($n + 1) * 10]);
            }
            foreach ($roles as $n => $p) {
                db_run('INSERT INTO system_roles (system_id, role_name, description, sort_order) VALUES (?, ?, ?, ?)', [$systemId, $p[0], $p[1], ($n + 1) * 10]);
            }
            $pdo->commit();
        } catch (Exception $ex) {
            $pdo->rollBack();
            error_log('GSMaRT website: system could not be saved: ' . $ex->getMessage());
            $errors['form'] = 'The system could not be saved because of a database problem. Nothing was changed.';
        }
        if (!$errors) {
            flash('notice', $form['name'] . ' was saved.');
            redirect(admin_url('system-edit.php', ['id' => $systemId]));
        }
    }
}

$field = function ($key, $label, $hint = '', $type = 'text', $rows = 0) use (&$form, &$errors) {
    $value = isset($form[$key]) ? (string) $form[$key] : '';
    echo '<div class="field' . ($rows ? ' field--wide' : '') . '"><label for="f-' . e($key) . '">' . $label . '</label>';
    if ($rows) {
        echo '<textarea id="f-' . e($key) . '" name="' . e($key) . '" rows="' . (int) $rows . '">' . e($value) . '</textarea>';
    } else {
        echo '<input type="' . e($type) . '" id="f-' . e($key) . '" name="' . e($key) . '" value="' . e($value) . '">';
    }
    if ($hint !== '') { echo '<p class="field__hint">' . $hint . '</p>'; }
    if (isset($errors[$key])) { echo '<p class="field__error">' . e($errors[$key]) . '</p>'; }
    echo '</div>';
};

$title = $id ? 'Edit ' . $system['name'] : 'Add a system';
admin_header($title, $user, 'systems');
?>
<div class="admin-bar">
  <div>
    <p class="admin-crumb"><a href="<?= e(admin_url('systems.php')) ?>">Systems</a> / <?= $id ? e($system['name']) : 'New' ?></p>
    <h1><?= e($title) ?></h1>
  </div>
<?php if ($id && $system['is_published']): ?>
  <a class="btn btn--line btn--sm" href="<?= e(url('system', $system['slug'])) ?>">View on the site</a>
<?php endif; ?>
</div>
<?php if ($errors): ?>
<p class="admin-msg admin-msg--error" role="alert"><?= e(isset($errors['form']) ? $errors['form'] : 'Some fields need attention. Nothing was saved yet.') ?></p>
<?php endif; ?>

<form method="post" action="<?= e(admin_url('system-edit.php', $id ? ['id' => $id] : [])) ?>" class="admin-form">
  <?= csrf_field() ?>

  <section class="admin-card">
    <h2>Card and listing</h2>
    <div class="form-grid">
      <?php $field('name', 'System name'); ?>
      <?php $field('slug', 'Address <span class="field__opt">leave blank to create it from the name</span>', 'Appears in the page address: /systems/<strong>this-part</strong>. Changing it breaks existing links to the page.'); ?>
      <div class="field">
        <label for="f-sector_id">Industry group</label>
        <select id="f-sector_id" name="sector_id">
<?php foreach ($sectors as $s): ?>
          <option value="<?= (int) $s['id'] ?>"<?= (int) $form['sector_id'] === (int) $s['id'] ? ' selected' : '' ?>><?= e($s['name']) ?></option>
<?php endforeach; ?>
        </select>
        <p class="field__hint">Used by the catalog filter and the Industries page.</p>
<?php if (isset($errors['sector_id'])): ?><p class="field__error"><?= e($errors['sector_id']) ?></p><?php endif; ?>
      </div>
      <?php $field('category', 'Industry label', 'Shown beside the name on the card, for example Utilities.'); ?>
      <?php $field('summary', 'Card description', 'One or two specific sentences.', 'text', 3); ?>
      <?php $field('search_terms', 'Extra search words <span class="field__opt">optional</span>', 'Business needs a visitor might type, separated by spaces.'); ?>
      <?php $field('sort_order', 'Order', 'Lower numbers come first.', 'number'); ?>
      <div class="field field--wide field--check">
        <input type="checkbox" id="f-is_published" name="is_published" value="1"<?= $form['is_published'] ? ' checked' : '' ?>>
        <label for="f-is_published">Published: show this system on the public site</label>
      </div>
      <div class="field field--wide field--check">
        <input type="checkbox" id="f-is_featured" name="is_featured" value="1"<?= $form['is_featured'] ? ' checked' : '' ?>>
        <label for="f-is_featured">Show in the home page grid (the first six are used)</label>
      </div>
    </div>
  </section>

  <section class="admin-card">
    <h2>Detail page</h2>
    <div class="form-grid">
      <?php $field('tagline', 'Benefit statement', 'One sentence under the system name.', 'text', 2); ?>
      <?php $field('audience', 'Who it is for', '', 'text', 3); ?>
      <?php $field('manages', 'What it manages', '', 'text', 3); ?>
      <?php $field('modules_text', 'Core modules', 'One per line as <strong>Name | What it does</strong>. The first four appear on the card.', 'text', 7); ?>
      <?php $field('steps_text', 'Workflow steps', 'In order, one per line as <strong>Step | What happens</strong>.', 'text', 7); ?>
      <?php $field('roles_text', 'Roles', 'One per line as <strong>Role | What they do</strong>.', 'text', 5); ?>
      <?php $field('implementation_notes', 'Implementation notes <span class="field__opt">optional</span>', 'One note per line.', 'text', 4); ?>
    </div>
  </section>

  <section class="admin-card">
    <h2>Availability</h2>
    <p class="admin-lead">Enter verified facts only. Leave a field blank and it is left off the public page.</p>
    <div class="form-grid">
      <?php $field('platforms', 'Platforms <span class="field__opt">optional</span>'); ?>
      <?php $field('deployment', 'Deployment <span class="field__opt">optional</span>'); ?>
      <?php $field('integrations', 'Integrations <span class="field__opt">optional</span>'); ?>
      <?php $field('pricing', 'Pricing <span class="field__opt">optional</span>'); ?>
    </div>
  </section>

  <div class="admin-save">
    <button class="btn btn--primary" type="submit" name="action" value="save">Save system</button>
    <a class="btn btn--ghost" href="<?= e(admin_url('systems.php')) ?>">Cancel</a>
  </div>
</form>

<?php if ($id): ?>
<form method="post" action="<?= e(admin_url('system-edit.php', ['id' => $id])) ?>" class="admin-card admin-form admin-danger">
  <?= csrf_field() ?>
  <h2>Delete this system</h2>
  <p class="admin-lead">To take a system off the site without losing its content, untick Published instead.</p>
  <div class="field field--check">
    <input type="checkbox" id="confirm_delete" name="confirm_delete" value="1">
    <label for="confirm_delete">I understand this permanently removes the system with its modules, workflow and roles.</label>
  </div>
  <button class="btn btn--line btn--sm" type="submit" name="action" value="delete">Delete system</button>
</form>
<?php endif; ?>
<?php admin_footer(); ?>
