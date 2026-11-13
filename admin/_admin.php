<?php
/**
 * Shared by every admin page: sign-in checks and the admin page frame.
 */
require dirname(__DIR__) . '/includes/bootstrap.php';
require_database();
start_session();

header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

define('ADMIN_IDLE_SECONDS', 7200);    // signed out after two hours without activity
define('ADMIN_MAX_ATTEMPTS', 5);       // wrong passwords before a temporary lock
define('ADMIN_LOCK_MINUTES', 15);

function admin_url($file = 'index.php', array $query = [])
{
    return base_path() . '/admin/' . $file . ($query ? '?' . http_build_query($query) : '');
}

function admin_count()
{
    $row = db_one('SELECT COUNT(*) AS n FROM admin_users');
    return (int) $row['n'];
}

function admin_user()
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }
    if (isset($_SESSION['admin_seen']) && time() - $_SESSION['admin_seen'] > ADMIN_IDLE_SECONDS) {
        unset($_SESSION['admin_id'], $_SESSION['admin_seen']);
        flash('notice', 'You were signed out after a period without activity.');
        return null;
    }
    $_SESSION['admin_seen'] = time();
    return db_one('SELECT id, username, display_name FROM admin_users WHERE id = ?', [$_SESSION['admin_id']]);
}

/** Send visitors who are not signed in to the sign-in page. */
function require_admin()
{
    if (admin_count() === 0) {
        redirect(admin_url('setup.php'));
    }
    $user = admin_user();
    if (!$user) {
        redirect(admin_url('login.php'));
    }
    return $user;
}

/** Every admin form posts a CSRF token. Stop if it is missing or wrong. */
function require_csrf()
{
    if (!csrf_valid(isset($_POST['csrf']) ? $_POST['csrf'] : null)) {
        http_response_code(419);
        admin_header('Form expired', null);
        echo '<p class="admin-msg admin-msg--error">This form expired before it was sent. Go back, reload the page and try again.</p>';
        admin_footer();
        exit;
    }
}

function password_problem($password)
{
    if (strlen($password) < 10) {
        return 'Use at least 10 characters.';
    }
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        return 'Include at least one letter and one number.';
    }
    return null;
}

function inquiry_statuses()
{
    return ['new' => 'New', 'in_progress' => 'In progress', 'closed' => 'Closed'];
}

function admin_header($title, $user, $current = '')
{
    $brand = company('brand', 'GSMaRT');
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title) ?> | <?= e($brand) ?> admin</title>
<link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="admin">
<a class="skip-link" href="#main">Skip to main content</a>
<header class="admin-top">
  <div class="wrap admin-top__in">
    <a class="brand" href="<?= e(admin_url()) ?>"><?= brand_mark() ?><span class="brand__name"><?= e($brand) ?></span><span class="admin-top__tag">Admin</span></a>
<?php if ($user): ?>
    <nav class="admin-nav" aria-label="Admin">
      <a href="<?= e(admin_url('index.php')) ?>"<?= $current === 'inquiries' ? ' aria-current="page"' : '' ?>>Inquiries</a>
      <a href="<?= e(admin_url('systems.php')) ?>"<?= $current === 'systems' ? ' aria-current="page"' : '' ?>>Systems</a>
      <a href="<?= e(admin_url('account.php')) ?>"<?= $current === 'account' ? ' aria-current="page"' : '' ?>>Account</a>
      <a href="<?= e(url('home')) ?>">View site</a>
    </nav>
    <form method="post" action="<?= e(admin_url('logout.php')) ?>" class="admin-top__out">
      <?= csrf_field() ?>
      <button class="btn btn--line btn--sm" type="submit">Sign out</button>
    </form>
<?php endif; ?>
  </div>
</header>
<main id="main" class="admin-main" tabindex="-1">
  <div class="wrap">
<?php
    foreach (['notice' => 'ok', 'error' => 'error'] as $key => $tone) {
        $message = flash($key);
        if ($message) {
            echo '<p class="admin-msg admin-msg--' . $tone . '" role="status">' . e($message) . '</p>';
        }
    }
}

function admin_footer()
{
    ?>
  </div>
</main>
</body>
</html>
<?php
}
