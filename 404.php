<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_database();

if (!headers_sent()) {
    http_response_code(404);
}

page_header([
    'title'  => 'Page not found | ' . company('brand'),
    'robots' => 'noindex',
]);

page_bar('Page not found', 'The address may have changed, or the system may no longer be listed.', [['Home', url('home')], ['Page not found', null]]);
?>

<section class="section section--flush">
  <div class="wrap">
    <div class="prose">
      <p>These pages are a good place to continue:</p>
      <ul class="plain-links">
        <li><a href="<?= e(url('systems')) ?>">Our Systems</a>, the full catalog with search</li>
        <li><a href="<?= e(url('industries')) ?>">Industries</a>, systems grouped by type of business</li>
        <li><a href="<?= e(url('contact')) ?>">Contact</a>, to ask us directly</li>
      </ul>
    </div>
  </div>
</section>

<?php page_footer(); ?>
