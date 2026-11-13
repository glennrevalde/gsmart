<?php
require __DIR__ . '/includes/bootstrap.php';
require_database();

$sectors = catalog_by_sector();

page_header([
    'title'       => 'Industries | ' . company('brand'),
    'description' => 'GSMaRT systems grouped by industry: finance and administration, education and utilities, retail, hospitality, local services, logistics and construction.',
    'current'     => 'industries',
    'canonical'   => url('industries'),
]);

page_bar(
    'Industries',
    'Systems grouped by the kind of business they are written for.',
    [['Home', url('home')], ['Industries', null]]
);
?>

<section class="section section--flush" aria-label="Systems by industry">
  <div class="wrap">
    <nav class="jump" aria-label="Industries on this page">
      <ul>
<?php foreach ($sectors as $sector): ?>
        <li><a href="#<?= e($sector['slug']) ?>"><?= e($sector['name']) ?></a></li>
<?php endforeach; ?>
      </ul>
    </nav>

<?php foreach ($sectors as $sector): ?>
    <section class="sector" id="<?= e($sector['slug']) ?>" aria-labelledby="h-<?= e($sector['slug']) ?>">
      <div class="sector__head">
        <h2 id="h-<?= e($sector['slug']) ?>"><?= e($sector['name']) ?></h2>
        <p><?= e($sector['summary']) ?></p>
        <a class="text-link" href="<?= e(url('systems', null, ['sector' => $sector['slug']])) ?>">View in the catalog</a>
      </div>
      <ul class="sector__list">
<?php foreach ($sector['systems'] as $s): ?>
        <li>
          <div class="sector__name">
            <h3><a href="<?= e(url('system', $s['slug'])) ?>"><?= e($s['name']) ?></a></h3>
            <p class="sector__cat"><?= e($s['category']) ?></p>
          </div>
          <p class="sector__sum"><?= e($s['summary']) ?></p>
          <p class="sector__mods"><?= e(implode(', ', array_slice($s['modules'], 0, 5))) ?></p>
        </li>
<?php endforeach; ?>
      </ul>
    </section>
<?php endforeach; ?>
  </div>
</section>

<section class="section section--tint" aria-labelledby="ind-cta">
  <div class="wrap cta-row">
    <div>
      <h2 id="ind-cta">Is your industry not here?</h2>
      <p class="section__lead">The catalog covers the systems that already exist. Other kinds of operations are handled as custom development.</p>
    </div>
    <a class="btn btn--primary" href="<?= e(url('contact', null, ['type' => 'custom'])) ?>">Talk to GSMaRT</a>
  </div>
</section>

<?php page_footer(); ?>
