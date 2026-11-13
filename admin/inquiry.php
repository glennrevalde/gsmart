<?php
/** One inquiry: read it, set its status, keep internal notes, or delete it. */
require __DIR__ . '/_admin.php';
$user = require_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$inquiry = db_one('SELECT * FROM inquiries WHERE id = ?', [$id]);
if (!$inquiry) {
    http_response_code(404);
    admin_header('Inquiry not found', $user, 'inquiries');
    echo '<div class="admin-empty"><h1>Inquiry not found</h1><p>It may have been deleted. <a href="' . e(admin_url()) . '">Back to inquiries</a></p></div>';
    admin_footer();
    exit;
}
$statuses = inquiry_statuses();
$types = inquiry_types();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($action === 'delete') {
        if (empty($_POST['confirm_delete'])) {
            flash('error', 'Tick the box to confirm before deleting.');
            redirect(admin_url('inquiry.php', ['id' => $id]));
        }
        db_run('DELETE FROM inquiries WHERE id = ?', [$id]);
        flash('notice', 'Inquiry ' . sprintf('%06d', $id) . ' was deleted.');
        redirect(admin_url());
    }
    $status = isset($_POST['status']) && isset($statuses[$_POST['status']]) ? $_POST['status'] : $inquiry['status'];
    $notes = mb_substr(trim(isset($_POST['admin_notes']) ? (string) $_POST['admin_notes'] : ''), 0, 5000);
    db_run('UPDATE inquiries SET status = ?, admin_notes = ?, updated_at = ? WHERE id = ?', [$status, $notes !== '' ? $notes : null, now(), $id]);
    flash('notice', 'Inquiry updated.');
    redirect(admin_url('inquiry.php', ['id' => $id]));
}

$ref = sprintf('%06d', $inquiry['id']);
$subject = rawurlencode('Re: your inquiry to ' . company('brand', 'GSMaRT') . ' (ref. ' . $ref . ')');

admin_header('Inquiry ' . $ref, $user, 'inquiries');
?>
<div class="admin-bar">
  <div>
    <p class="admin-crumb"><a href="<?= e(admin_url()) ?>">Inquiries</a> / <?= $ref ?></p>
    <h1><?= e($inquiry['full_name']) ?></h1>
  </div>
  <a class="btn btn--primary btn--sm" href="mailto:<?= e($inquiry['email']) ?>?subject=<?= $subject ?>">Reply by e-mail</a>
</div>

<div class="admin-split">
  <section class="admin-card" aria-label="Inquiry">
    <dl class="admin-dl">
      <div><dt>Received</dt><dd><?= e(date('F j, Y, g:i A', strtotime($inquiry['created_at']))) ?></dd></div>
      <div><dt>Organization</dt><dd><?= e($inquiry['organization'] ?: 'Not given') ?></dd></div>
      <div><dt>E-mail</dt><dd><a href="mailto:<?= e($inquiry['email']) ?>"><?= e($inquiry['email']) ?></a></dd></div>
      <div><dt>Phone</dt><dd><?= $inquiry['phone'] ? '<a href="tel:' . e(preg_replace('/[^0-9+]/', '', $inquiry['phone'])) . '">' . e($inquiry['phone']) . '</a>' : 'Not given' ?></dd></div>
      <div><dt>System</dt><dd><?= e($inquiry['system_name'] ?: 'Not sure yet') ?></dd></div>
      <div><dt>About</dt><dd><?= e(isset($types[$inquiry['inquiry_type']]) ? $types[$inquiry['inquiry_type']] : $inquiry['inquiry_type']) ?></dd></div>
      <div><dt>Notification e-mail</dt><dd><?= $inquiry['email_sent'] ? 'Sent' : 'Not sent' ?></dd></div>
    </dl>
    <h2>Message</h2>
    <div class="admin-message"><?= nl2br(e($inquiry['message'])) ?></div>
  </section>

  <aside>
    <form method="post" action="<?= e(admin_url('inquiry.php', ['id' => $id])) ?>" class="admin-card admin-form">
      <?= csrf_field() ?>
      <h2>Follow-up</h2>
      <div class="field">
        <label for="status">Status</label>
        <select id="status" name="status">
<?php foreach ($statuses as $key => $label): ?>
          <option value="<?= e($key) ?>"<?= $inquiry['status'] === $key ? ' selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label for="admin_notes">Internal notes <span class="field__opt">not shown to the visitor</span></label>
        <textarea id="admin_notes" name="admin_notes" rows="6" maxlength="5000"><?= e($inquiry['admin_notes']) ?></textarea>
      </div>
      <button class="btn btn--primary" type="submit" name="action" value="save">Save</button>
    </form>

    <form method="post" action="<?= e(admin_url('inquiry.php', ['id' => $id])) ?>" class="admin-card admin-form admin-danger">
      <?= csrf_field() ?>
      <h2>Delete this inquiry</h2>
      <div class="field field--check">
        <input type="checkbox" id="confirm_delete" name="confirm_delete" value="1">
        <label for="confirm_delete">I understand this removes the inquiry permanently.</label>
      </div>
      <button class="btn btn--line btn--sm" type="submit" name="action" value="delete">Delete inquiry</button>
    </form>
  </aside>
</div>
<?php admin_footer(); ?>
