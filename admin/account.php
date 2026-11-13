<?php
require __DIR__ . '/_admin.php';
$user = require_admin();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $current = isset($_POST['current']) ? (string) $_POST['current'] : '';
    $new     = isset($_POST['password']) ? (string) $_POST['password'] : '';
    $confirm = isset($_POST['confirm']) ? (string) $_POST['confirm'] : '';
    $row = db_one('SELECT password_hash FROM admin_users WHERE id = ?', [$user['id']]);

    if (!password_verify($current, $row['password_hash'])) {
        $errors['current'] = 'That is not your current password.';
    }
    $problem = password_problem($new);
    if ($problem) {
        $errors['password'] = $problem;
    } elseif ($new !== $confirm) {
        $errors['confirm'] = 'The two new passwords are not the same.';
    }
    if (!$errors) {
        db_run('UPDATE admin_users SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), $user['id']]);
        session_regenerate_id(true);
        flash('notice', 'Password changed.');
        redirect(admin_url('account.php'));
    }
}

admin_header('Account', $user, 'account');
?>
<div class="admin-bar"><h1>Account</h1></div>
<div class="admin-card admin-card--narrow admin-card--left">
  <p class="admin-lead">Signed in as <strong><?= e($user['username']) ?></strong>.</p>
  <h2>Change password</h2>
  <form method="post" action="<?= e(admin_url('account.php')) ?>" class="admin-form">
    <?= csrf_field() ?>
    <div class="field">
      <label for="current">Current password</label>
      <input type="password" id="current" name="current" autocomplete="current-password" required>
<?php if (isset($errors['current'])): ?><p class="field__error"><?= e($errors['current']) ?></p><?php endif; ?>
    </div>
    <div class="field">
      <label for="password">New password</label>
      <input type="password" id="password" name="password" autocomplete="new-password" required>
      <p class="field__hint">At least 10 characters, with a letter and a number.</p>
<?php if (isset($errors['password'])): ?><p class="field__error"><?= e($errors['password']) ?></p><?php endif; ?>
    </div>
    <div class="field">
      <label for="confirm">Repeat the new password</label>
      <input type="password" id="confirm" name="confirm" autocomplete="new-password" required>
<?php if (isset($errors['confirm'])): ?><p class="field__error"><?= e($errors['confirm']) ?></p><?php endif; ?>
    </div>
    <button class="btn btn--primary" type="submit">Change password</button>
  </form>
</div>
<?php admin_footer(); ?>
