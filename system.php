<?php
require __DIR__ . '/includes/bootstrap.php';
require_database();
start_session();   // the inquiry form needs the session before any output

$slug   = isset($_GET['slug']) && is_string($_GET['slug']) ? $_GET['slug'] : '';
$system = catalog_system($slug);
if (!$system) {
    require __DIR__ . '/404.php';
    exit;
}

$screens = preview_screens($system['slug'], $system);
$demoUrl = url('contact', null, ['system' => $system['slug'], 'type' => 'demo']);

// Other published systems in the same sector.
$related = array_values(array_filter(catalog_systems(), function ($s) use ($system) {
    return $s['sector_slug'] === $system['sector_slug'] && $s['slug'] !== $system['slug'];
}));

// Availability facts. Only verified values belong in these columns.
$availability = [
    'Platforms'    => $system['platforms'],
    'Deployment'   => $system['deployment'],
    'Integrations' => $system['integrations'],
    'Pricing'      => $system['pricing'],
];
$hasAvailability = (bool) array_filter($availability);

page_header([
    'title'       => $system['name'] . ' | ' . company('brand'),
    'description' => $system['summary'],
    'current'     => 'systems',
    'canonical'   => url('system', $system['slug']),
]);

page_bar(
    $system['name'],
    $system['tagline'],
    [
        ['Our Systems', url('systems')],
        [$system['sector_name'], url('systems', null, ['sector' => $system['sector_slug']])],
        [$system['name'], null],
    ],
    '<a class="btn btn--primary" href="#inquiry">Request a Demo</a>'
    . '<p class="page-bar__meta">Industry: ' . e($system['category']) . '</p>'
);
?>
<script type="application/ld+json"><?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Our Systems', 'item' => absolute_url(url('systems'))],
        ['@type' => 'ListItem', 'position' => 2, 'name' => $system['name'], 'item' => absolute_url(url('system', $system['slug']))],
    ],
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>

<section class="showcase" aria-labelledby="showcase-title">
  <div class="showcase__lines" aria-hidden="true"></div>
  <div class="wrap">
    <h2 class="visually-hidden" id="showcase-title">Screens</h2>
    <div class="showcase__in" data-showcase>
      <div class="showcase__tabs" role="tablist" aria-label="<?= e($system['name']) ?> screens">
<?php foreach ($screens as $i => $screen): ?>
        <button type="button" role="tab" id="tab-<?= $i ?>" aria-controls="screen-<?= $i ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"<?= $i === 0 ? '' : ' tabindex="-1"' ?>><?= e($screen['label']) ?></button>
<?php endforeach; ?>
      </div>
      <div class="stage showcase__stage" data-stage>
        <div class="stage__rig" data-stage-rig>
          <div class="device device--laptop showcase__laptop">
            <div class="laptop__lid">
              <div class="screen">
<?php foreach ($screens as $i => $screen): ?>
                <div class="showcase__screen" role="tabpanel" id="screen-<?= $i ?>" aria-labelledby="tab-<?= $i ?>" aria-label="Illustrative <?= e($screen['label']) ?> screen"<?= $i === 0 ? '' : ' hidden' ?>>
                  <?= render_screen($screen, 'full') ?>
                </div>
<?php endforeach; ?>
                <div class="sheen"></div>
              </div>
            </div>
            <div class="laptop__base"><div class="laptop__keys"></div><div class="laptop__pad"></div></div>
            <div class="device__shadow"></div>
          </div>
        </div>
      </div>
      <?= preview_note() ?>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="overview-title">
  <div class="wrap rail">
    <div class="rail__side">
      <h2 id="overview-title">What it covers</h2>
    </div>
    <div class="facts">
      <div class="facts__item">
        <h3>Who it is for</h3>
        <p><?= e($system['audience']) ?></p>
      </div>
      <div class="facts__item">
        <h3>What it manages</h3>
        <p><?= e($system['manages']) ?></p>
      </div>
    </div>
  </div>
</section>

<?php if ($system['steps']): ?>
<section class="section section--tint" aria-labelledby="flow-title">
  <div class="wrap">
    <div class="section__head">
      <h2 id="flow-title">How the work moves through it</h2>
      <p class="section__lead">The usual sequence, from the first record to the last.</p>
    </div>
    <ol class="flow" data-reveal>
<?php foreach ($system['steps'] as $step): ?>
      <li>
        <h3><?= e($step['label']) ?></h3>
        <p><?= e($step['detail']) ?></p>
      </li>
<?php endforeach; ?>
    </ol>
  </div>
</section>
<?php endif; ?>

<?php if ($system['modules']): ?>
<section class="section" aria-labelledby="modules-title">
  <div class="wrap rail">
    <div class="rail__side">
      <h2 id="modules-title">Core modules</h2>
      <p class="section__lead"><?= count($system['modules']) ?> modules make up the system. Each can be reviewed in a demo.</p>
    </div>
    <div class="matrix" role="table" aria-label="Core modules">
      <div class="matrix__row matrix__row--head" role="row">
        <span role="columnheader">Module</span><span role="columnheader">What it does</span>
      </div>
<?php foreach ($system['modules'] as $module): ?>
      <div class="matrix__row" role="row">
        <span class="matrix__name" role="cell"><?= e($module['name']) ?></span>
        <span role="cell"><?= e($module['description']) ?></span>
      </div>
<?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($system['roles']): ?>
<section class="section section--tint" aria-labelledby="roles-title">
  <div class="wrap rail">
    <div class="rail__side">
      <h2 id="roles-title">Who uses it</h2>
      <p class="section__lead">Each role sees the screens it needs for its part of the work.</p>
    </div>
    <ul class="roles">
<?php foreach ($system['roles'] as $role): ?>
      <li>
        <h3><?= e($role['role_name']) ?></h3>
        <p><?= e($role['description']) ?></p>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
</section>
<?php endif; ?>

<section class="section" aria-labelledby="impl-title">
  <div class="wrap rail">
    <div class="rail__side">
      <h2 id="impl-title">Implementation and availability</h2>
    </div>
    <div class="impl">
<?php if ($system['notes']): ?>
      <div>
        <h3>Implementation notes</h3>
        <ul class="ticks">
<?php foreach ($system['notes'] as $note): ?>
          <li><?= e($note) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
<?php endif; ?>
      <div>
        <h3>Availability</h3>
<?php if ($hasAvailability || is_review()): ?>
        <dl class="avail">
<?php foreach ($availability as $label => $value): if ($value || is_review()): ?>
          <div><dt><?= e($label) ?></dt><dd><?= detail_or_placeholder($value, $label) ?></dd></div>
<?php endif; endforeach; ?>
        </dl>
<?php endif; ?>
<?php if (!$hasAvailability): ?>
        <p>Supported platforms, how the system is installed, integrations and pricing are confirmed for each engagement. Ask about them in your inquiry and we will answer for your situation.</p>
<?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php if ($related): ?>
<section class="section section--related" aria-labelledby="related-title">
  <div class="wrap">
    <h2 id="related-title">Other systems in <?= e($system['sector_name']) ?></h2>
    <ul class="related">
<?php foreach ($related as $r): ?>
      <li>
        <a href="<?= e(url('system', $r['slug'])) ?>"><?= e($r['name']) ?></a>
        <p><?= e($r['tagline']) ?></p>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
</section>
<?php endif; ?>

<section class="section section--contact" id="inquiry" aria-labelledby="inquiry-title">
  <div class="wrap rail rail--form">
    <div class="rail__side">
      <h2 id="inquiry-title">Request a demo of <?= e($system['name']) ?></h2>
      <p class="section__lead">The system is already selected in the form. Change it if you would like to ask about a different one.</p>
    </div>
    <div class="form-panel">
      <?php inquiry_form(['system' => $system['slug'], 'type' => 'demo']); ?>
    </div>
  </div>
</section>

<?php page_footer(); ?>
