<?php
require __DIR__ . '/_admin.php';

if (admin_count() === 0) {
    redirect(admin_url('setup.php'));
}
if (admin_user()) {
    redirect(admin_url());
}

$error = null;
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $username = mb_substr(trim(isset($_POST['username']) ? (string) $_POST['username'] : ''), 0, 60);
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
    $user = db_one('SELECT * FROM admin_users WHERE username = ?', [$username]);

    if ($user && $user['locked_until'] && strtotime($user['locked_until']) > time()) {
        $error = 'Too many wrong passwords. This account is locked for a few minutes. Try again later.';
    } elseif ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $user['id'];
        $_SESSION['admin_seen'] = time();
        db_run('UPDATE admin_users SET failed_attempts = 0, locked_until = NULL, last_login_at = ? WHERE id = ?', [now(), $user['id']]);
        redirect(admin_url());
    } else {
        if ($user) {
            $attempts = (int) $user['failed_attempts'] + 1;
            $lock = $attempts >= ADMIN_MAX_ATTEMPTS ? date('Y-m-d H:i:s', time() + ADMIN_LOCK_MINUTES * 60) : null;
            db_run('UPDATE admin_users SET failed_attempts = ?, locked_until = ? WHERE id = ?', [$lock ? 0 : $attempts, $lock, $user['id']]);
        }
        usleep(400000);   // slow down guessing
        $error = 'That username and password do not match.';
    }
}

admin_header('Sign in', null);
?>
<div class="admin-card admin-card--narrow">
  <h1>Sign in</h1>
<?php if ($error): ?>
  <p class="admin-msg admin-msg--error" role="alert"><?= e($error) ?></p>
<?php endif; ?>
  <form method="post" action="<?= e(admin_url('login.php')) ?>" class="admin-form">
    <?= csrf_field() ?>
    <div class="field">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" value="<?= e($username) ?>" autocomplete="username" maxlength="60" required autofocus>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" autocomplete="current-password" required>
    </div>
    <button class="btn btn--primary btn--block" type="submit">Sign in</button>
  </form>
</div>
<?php admin_footer(); ?>
