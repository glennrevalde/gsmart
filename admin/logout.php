<?php
require __DIR__ . '/_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    unset($_SESSION['admin_id'], $_SESSION['admin_seen']);
    session_regenerate_id(true);
    flash('notice', 'You are signed out.');
}
redirect(admin_url('login.php'));
