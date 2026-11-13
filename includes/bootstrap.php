<?php
/**
 * Loaded first by every page: configuration, session, helpers.
 * Written to run on PHP 5.6 through 8.x.
 */

if (defined('GSMART_ROOT')) {
    return;
}
define('GSMART_ROOT', dirname(__DIR__));

// ------------------------------------------------------------ configuration
$configFile = GSMART_ROOT . '/config/config.php';
if (!is_file($configFile)) {
    $configFile = GSMART_ROOT . '/config/config.sample.php';
}
$GLOBALS['gsmart_config']  = require $configFile;
$GLOBALS['gsmart_company'] = require GSMART_ROOT . '/config/company.php';

/** Read a configuration value with dot notation, e.g. config('db.host'). */
function config($key, $default = null)
{
    static $env = [
        'db.host' => 'GSMART_DB_HOST', 'db.port' => 'GSMART_DB_PORT', 'db.name' => 'GSMART_DB_NAME',
        'db.user' => 'GSMART_DB_USER', 'db.pass' => 'GSMART_DB_PASS',
        'site_url' => 'GSMART_SITE_URL', 'app_key' => 'GSMART_APP_KEY',
        'inquiry.notify_email' => 'GSMART_NOTIFY_EMAIL', 'inquiry.from_email' => 'GSMART_FROM_EMAIL',
    ];
    if (isset($env[$key])) {
        $value = getenv($env[$key]);
        if ($value !== false && $value !== '') {
            return $value;
        }
    }
    $node = $GLOBALS['gsmart_config'];
    foreach (explode('.', $key) as $part) {
        if (!is_array($node) || !array_key_exists($part, $node)) {
            return $default;
        }
        $node = $node[$part];
    }
    return $node;
}

/** Read a value from config/company.php. */
function company($key, $default = null)
{
    $c = $GLOBALS['gsmart_company'];
    return (isset($c[$key]) && $c[$key] !== '') ? $c[$key] : $default;
}

date_default_timezone_set(config('timezone', 'UTC') ?: 'UTC');
mb_internal_encoding('UTF-8');

if (config('debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

// ------------------------------------------------------------------ request
function is_https()
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    return isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
}

/** Folder the site is installed in, relative to the web root. '' or '/folder'. */
function base_path()
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $base = '';
    $scriptFile = isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : false;
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';
    $root = realpath(GSMART_ROOT);
    if ($scriptFile && $root && strpos($scriptFile, $root) === 0) {
        $inside = str_replace('\\', '/', substr($scriptFile, strlen($root)));   // "/admin/index.php"
        if ($inside !== '' && substr($scriptName, -strlen($inside)) === $inside) {
            $base = substr($scriptName, 0, -strlen($inside));
        }
    }
    $base = rtrim($base, '/');
    return $base;
}

function pretty_urls()
{
    static $pretty = null;
    if ($pretty === null) {
        $setting = config('pretty_urls', 'auto');
        if ($setting === true || $setting === false) {
            $pretty = $setting;
        } else {
            // .htaccess sets this flag only when mod_rewrite is really running.
            $pretty = !empty($_SERVER['GSMART_REWRITE']) || !empty($_SERVER['REDIRECT_GSMART_REWRITE']);
        }
    }
    return $pretty;
}

/**
 * Address of a page.
 *   url('home'), url('systems'), url('system', 'laundry-management'),
 *   url('contact', null, ['system' => 'laundry-management'])
 */
function url($page = 'home', $slug = null, array $query = [])
{
    $base = base_path();
    if ($page === 'home') {
        $path = '/';
    } elseif ($page === 'system') {
        if (pretty_urls()) {
            $path = '/systems/' . rawurlencode($slug);
        } else {
            $path = '/system.php';
            $query = ['slug' => $slug] + $query;
        }
    } else {
        $path = '/' . $page . (pretty_urls() ? '' : '.php');
    }
    $q = $query ? '?' . http_build_query($query) : '';
    return $base . $path . $q;
}

function asset($path)
{
    $file = GSMART_ROOT . '/assets/' . ltrim($path, '/');
    $version = is_file($file) ? '?v=' . filemtime($file) : '';
    return base_path() . '/assets/' . ltrim($path, '/') . $version;
}

/** Scheme and host, e.g. https://www.example.com (no folder, no trailing slash). */
function site_origin()
{
    $configured = rtrim((string) config('site_url', ''), '/');
    if ($configured !== '') {
        $parts = parse_url($configured);
        if (!empty($parts['scheme']) && !empty($parts['host'])) {
            return $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');
        }
    }
    $host = isset($_SERVER['HTTP_HOST']) ? preg_replace('/[^A-Za-z0-9.\-:\[\]]/', '', $_SERVER['HTTP_HOST']) : 'localhost';
    return (is_https() ? 'https' : 'http') . '://' . $host;
}

function absolute_url($relative)
{
    return site_origin() . $relative;
}

function redirect($to)
{
    header('Location: ' . $to, true, 303);
    exit;
}

// ------------------------------------------------------------------- output
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_review()
{
    return (bool) config('review_mode', true);
}

/**
 * A business detail that may not have been supplied yet.
 * Returns HTML: the value, a marked placeholder (review mode) or ''.
 */
function detail_or_placeholder($value, $label)
{
    if ($value !== null && $value !== '') {
        return e($value);
    }
    if (is_review()) {
        return '<span class="placeholder" title="Placeholder: supply this before publishing">[' . e($label) . ' to be supplied]</span>';
    }
    return '';
}

function nav_items()
{
    return [
        'systems'    => 'Our Systems',
        'industries' => 'Industries',
        'services'   => 'Services',
        'about'      => 'About GSMaRT',
        'contact'    => 'Contact',
    ];
}

// ------------------------------------------------------------------ session
function start_session()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name('gsmart_session');
    $path = base_path() . '/';
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0, 'path' => $path, 'secure' => is_https(),
            'httponly' => true, 'samesite' => 'Lax',
        ]);
    } else {
        session_set_cookie_params(0, $path . '; samesite=Lax', '', is_https(), true);
    }
    session_start();
}

function random_token($bytes = 32)
{
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes($bytes));
    }
    return bin2hex(openssl_random_pseudo_bytes($bytes));
}

function csrf_token()
{
    start_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = random_token();
    }
    return $_SESSION['csrf'];
}

function csrf_valid($token)
{
    start_session();
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** One-time messages carried across a redirect. */
function flash($key, $value = null)
{
    start_session();
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }
    if (isset($_SESSION['flash'][$key])) {
        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    return null;
}

function now()
{
    return date('Y-m-d H:i:s');
}

function client_ip()
{
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
}

require GSMART_ROOT . '/includes/db.php';
require GSMART_ROOT . '/includes/catalog.php';
require GSMART_ROOT . '/includes/preview.php';
require GSMART_ROOT . '/includes/layout.php';
require GSMART_ROOT . '/includes/inquiry.php';

// Baseline security headers for every response.
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
