<?php
/**
 * Router for PHP's built-in server, for local testing without Apache:
 *
 *   php -S localhost:8000 router.php
 *
 * It mirrors the rules in .htaccess. It is not used in production.
 */
if (PHP_SAPI !== 'cli-server') {
    http_response_code(404);
    exit;
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = '/' . ltrim(rawurldecode($path), '/');
$file = __DIR__ . $path;

// Private folders, dotfiles and source documents are never served.
if (preg_match('#^/(config|includes|content|database|tools)(/|$)#', $path)
    || preg_match('#(^|/)\.#', $path) || preg_match('#\.(md|sql|log)$#', $path) || $path === '/router.php') {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

$_SERVER['GSMART_REWRITE'] = 1;

$run = function ($script, array $get = []) {
    $_GET = $get + $_GET;
    $_SERVER['SCRIPT_NAME'] = '/' . $script;
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/' . $script;
    $_SERVER['PHP_SELF'] = '/' . $script;
    chdir(dirname(__DIR__ . '/' . $script));
    require __DIR__ . '/' . $script;
    return true;
};

if ($path === '/sitemap.xml') { return $run('sitemap.php'); }
if ($path === '/robots.txt')  { return $run('robots.php'); }
if (preg_match('#^/systems/([a-z0-9-]+)/?$#', $path, $m)) { return $run('system.php', ['slug' => $m[1]]); }

if (is_file($file)) {
    return false;                         // real file: let the server send it
}
if (is_dir($file) && is_file(rtrim($file, '/') . '/index.php')) {
    return $run(ltrim(rtrim($path, '/') . '/index.php', '/'));
}
if (preg_match('#^/([a-z0-9-]+)/?$#', $path, $m) && is_file(__DIR__ . '/' . $m[1] . '.php')) {
    return $run($m[1] . '.php');
}
return $run('404.php');
