<?php
/** XML sitemap. Served as /sitemap.xml when clean addresses are on. */
require __DIR__ . '/includes/bootstrap.php';
require_database();

$urls = [url('home')];
foreach (['systems', 'industries', 'services', 'about', 'contact', 'privacy', 'terms'] as $page) {
    $urls[] = url($page);
}
$rows = db_all('SELECT slug, updated_at FROM systems WHERE is_published = 1 ORDER BY sort_order, name');

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo '  <url><loc>' . e(absolute_url($u)) . "</loc></url>\n";
}
foreach ($rows as $row) {
    echo '  <url><loc>' . e(absolute_url(url('system', $row['slug']))) . '</loc><lastmod>'
        . e(substr($row['updated_at'], 0, 10)) . "</lastmod></url>\n";
}
echo "</urlset>\n";
