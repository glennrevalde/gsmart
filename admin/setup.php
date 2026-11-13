<?php
/**
 * First-run page: creates the first administrator.
 * It only works while the admin_users table is empty.
 */
require __DIR__ . '/_admin.php';

if (admin_count() > 0) {
    redirect(admin_url('login.php'));
}

$errors = [];
$username = '';
$display = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $username = trim(isset($_POST['username']) ? (string) $_POST['username'] : '');
    $display  = mb_substr(trim(isset($_POST['display_name']) ? (string) $_POST['display_name'] : ''), 0, 120);
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
    $confirm  = isset($_POST['confirm']) ? (string) $_POST['confirm'] : '';

    if (!preg_match('/^[A-Za-z0-9._-]{3,60}$/', $username)) {
        $errors['username'] = 'Use 3 to 60 letters, numbers, dots, dashes or underscores.';
    }
    $problem = password_problem($password);
    if ($problem) {
        $errors['password'] = $problem;
    } elseif ($password !== $confirm) {
        $errors['confirm'] = 'The two passwords are not the same.';
    }
    if (!$errors && admin_count() === 0) {
        db_run(
            'INSERT INTO admin_users (username, display_name, password_hash, created_at) VALUES (?, ?, ?, ?)',
            [$username, $display !== '' ? $display : $username, password_hash($password, PASSWORD_DEFAULT), now()]
        );
        flash('notice', 'Administrator created. Sign in to continue.');
        redirect(admin_url('login.php'));
    }
}

admin_header('Create the first administrator', null);
?>
<div class="admin-card admin-card--narrow">
  <h1>Create the first administrator</h1>
  <p class="admin-lead">No administrator exists yet. This page closes itself once one has been created.</p>
  <form method="post" action="<?= e(admin_url('setup.php')) ?>" class="admin-form">
    <?= csrf_field() ?>
    <div class="field">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" value="<?= e($username) ?>" autocomplete="username" maxlength="60" required>
<?php if (isset($errors['username'])): ?><p class="field__error"><?= e($errors['username']) ?></p><?php endif; ?>
    </div>
    <div class="field">
      <label for="display_name">Name shown in the admin area <span class="field__opt">optional</span></label>
      <input type="text" id="display_name" name="display_name" value="<?= e($display) ?>" maxlength="120">
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" autocomplete="new-password" required>
      <p class="field__hint">At least 10 characters, with a letter and a number.</p>
<?php if (isset($errors['password'])): ?><p class="field__error"><?= e($errors['password']) ?></p><?php endif; ?>
    </div>
    <div class="field">
      <label for="confirm">Repeat the password</label>
      <input type="password" id="confirm" name="confirm" autocomplete="new-password" required>
<?php if (isset($errors['confirm'])): ?><p class="field__error"><?= e($errors['confirm']) ?></p><?php endif; ?>
    </div>
    <button class="btn btn--primary btn--block" type="submit">Create administrator</button>
  </form>
</div>
<?php admin_footer(); ?>
