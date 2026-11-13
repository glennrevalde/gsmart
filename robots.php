<?php
/** robots.txt. Served as /robots.txt when clean addresses are on. */
require __DIR__ . '/includes/bootstrap.php';

header('Content-Type: text/plain; charset=utf-8');
echo "User-agent: *\n";
if (is_review()) {
    // Review mode: keep the whole site out of search engines.
    echo "Disallow: /\n";
    exit;
}
echo 'Disallow: ' . base_path() . "/admin/\n";
echo 'Sitemap: ' . absolute_url(base_path() . (pretty_urls() ? '/sitemap.xml' : '/sitemap.php')) . "\n";
