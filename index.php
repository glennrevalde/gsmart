<?php
require __DIR__ . '/includes/bootstrap.php';
require_database();
start_session();   // the inquiry form needs the session before any output

$systems  = catalog_systems();
$featured = array_values(array_filter($systems, function ($s) {
    return !empty($s['is_featured']);
}));
if (count($featured) < 3) {
    $featured = array_slice($systems, 0, 6);
}
$featured = array_slice($featured, 0, 6);
$sectors  = catalog_by_sector();

// Screens used on the hero devices (see '_hero' in content/previews.php).
$previews = preview_data();
$hero     = $previews['_hero'];
$pick     = function ($ref) use ($previews) {
    return $previews[$ref[0]]['screens'][$ref[1]];
};

page_header([
    'title'       => company('home_title'),
    'description' => company('meta_description'),
    'current'     => 'home',
    'canonical'   => url('home'),
    'body_class'  => 'page-home',
]);
?>

<section class="hero" aria-labelledby="hero-title">
  <div class="hero__lines" aria-hidden="true"></div>
  <div class="wrap hero__in">
    <div class="hero__text">
      <h1 id="hero-title">Business systems built around the way you work.</h1>
      <p class="hero__lead">GSMaRT develops high-quality software for managing daily operations, connecting teams, and keeping important business information organized.</p>
      <div class="hero__actions">
        <a class="btn btn--primary" href="<?= e(url('systems')) ?>">Explore Our Systems</a>
        <a class="btn btn--line" href="<?= e(url('contact', null, ['type' => 'demo'])) ?>">Request a Demo</a>
      </div>
    </div>

    <div class="hero__visual" role="img" aria-label="Illustration of GSMaRT systems on a laptop, a tablet and a phone: an accounting overview, a hotel reservation calendar, a construction schedule and a meter reading list.">
      <div class="stage" data-stage>
        <div class="stage__rig" data-stage-rig>
          <?= device_window(render_screen($pick($hero['window']), 'compact'), 'stage__window') ?>
          <?= device_laptop(render_screen($pick($hero['laptop']), 'full'), 'stage__laptop') ?>
          <?= device_tablet(render_screen($pick($hero['tablet']), 'compact'), 'stage__tablet') ?>
          <?= device_phone(render_screen($hero['phone'], 'phone'), 'stage__phone') ?>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="wall-section" aria-labelledby="systems-title">
  <div class="wrap">
    <div class="wall" data-wall>
      <div class="wall__head" data-wall-head>
        <h2 id="systems-title">GSMaRT offers high-quality systems for:</h2>
        <p>Explore systems for different industries and operational needs. Select a solution to see its functions, workflows and available options.</p>
        <a class="btn btn--outline" href="<?= e(url('systems')) ?>">View All Systems<span class="visually-hidden"> (<?= count($systems) ?>)</span></a>
      </div>
<?php foreach ($featured as $n => $system): ?>
      <div class="wall__cell" data-cell><?php system_card($system, 3, $n); ?></div>
<?php endforeach; ?>
    </div>

<?php $rest = array_values(array_filter($systems, function ($s) use ($featured) {
    foreach ($featured as $f) { if ($f['slug'] === $s['slug']) { return false; } }
    return true;
})); ?>
<?php if ($rest): ?>
    <div class="more-systems">
      <p class="more-systems__label">Also in the catalog</p>
      <ul>
<?php foreach ($rest as $s): ?>
        <li><a href="<?= e(url('system', $s['slug'])) ?>"><?= e($s['name']) ?></a></li>
<?php endforeach; ?>
      </ul>
    </div>
<?php endif; ?>
    <?= preview_note() ?>
  </div>
</section>

<section class="section section--tint" aria-labelledby="ops-title">
  <div class="wrap rail">
    <div class="rail__side">
      <h2 id="ops-title">Built for real operations</h2>
      <p class="section__lead">GSMaRT provides software systems designed to help businesses manage their operations more clearly and efficiently. From billing and inventory to workforce, reservations and reporting, our systems organize the work that happens every day.</p>
    </div>
    <dl class="ledger">
<?php foreach (company('operations', []) as $op): ?>
      <div class="ledger__row">
        <dt><?= e($op['name']) ?></dt>
        <dd><?= e($op['text']) ?></dd>
      </div>
<?php endforeach; ?>
    </dl>
  </div>
</section>

<section class="section" aria-labelledby="ind-title">
  <div class="wrap rail">
    <div class="rail__side">
      <h2 id="ind-title">Industry coverage</h2>
      <p class="section__lead">Each system is written for a particular kind of business, with the terms, documents and steps that business already uses.</p>
      <p><a class="text-link" href="<?= e(url('industries')) ?>">See systems by industry</a></p>
    </div>
    <div class="index-table">
<?php foreach ($sectors as $sector): ?>
      <div class="index-table__row">
        <h3><a href="<?= e(url('industries')) ?>#<?= e($sector['slug']) ?>"><?= e($sector['name']) ?></a></h3>
        <ul>
<?php foreach ($sector['systems'] as $s): ?>
          <li><a href="<?= e(url('system', $s['slug'])) ?>"><?= e($s['name']) ?></a></li>
<?php endforeach; ?>
        </ul>
      </div>
<?php endforeach; ?>
    </div>
  </div>
</section>

<?php $services = company('services', []); if ($services): ?>
<section class="section section--tint" aria-labelledby="svc-title">
  <div class="wrap rail">
    <div class="rail__side">
      <h2 id="svc-title">Services</h2>
      <p class="section__lead">A system is only useful once it matches your process and your staff know how to use it. These are the services around the software.</p>
      <p><a class="text-link" href="<?= e(url('services')) ?>">Read about our services</a></p>
    </div>
    <ul class="svc-grid">
<?php foreach ($services as $svc): ?>
      <li>
        <h3><?= e($svc['name']) ?></h3>
        <p><?= e($svc['text']) ?></p>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
</section>
<?php endif; ?>

<section class="section" aria-labelledby="how-title">
  <div class="wrap">
    <div class="section__head">
      <h2 id="how-title">How an engagement works</h2>
      <p class="section__lead">The same six steps whether you take an existing system or have one built.</p>
    </div>
    <ol class="steps" data-reveal>
<?php foreach (company('process', []) as $step): ?>
      <li>
        <h3><?= e($step['name']) ?></h3>
        <p><?= e($step['text']) ?></p>
      </li>
<?php endforeach; ?>
    </ol>
<?php if (is_review()): ?>
    <p class="review-note">Proposed process, shown pending confirmation by GSMaRT.</p>
<?php endif; ?>
  </div>
</section>

<section class="section section--tint" aria-labelledby="about-title">
  <div class="wrap rail">
    <div class="rail__side">
      <h2 id="about-title">About GSMaRT</h2>
    </div>
    <div class="prose prose--large">
      <p>GSMaRT focuses on practical software for organizations that need clearer records, more consistent processes and dependable operational tools.</p>
      <p><a class="text-link" href="<?= e(url('about')) ?>">More about the company</a></p>
    </div>
  </div>
</section>

<section class="section section--contact" id="inquiry" aria-labelledby="contact-title">
  <div class="wrap rail rail--form">
    <div class="rail__side">
      <h2 id="contact-title">Tell us what your business needs.</h2>
      <p class="section__lead">Describe how the work is done today. We will reply with the system that fits closest, or tell you plainly if none does.</p>
    </div>
    <div class="form-panel">
      <?php inquiry_form(['type' => 'demo']); ?>
    </div>
  </div>
</section>

<?php page_footer(); ?>
