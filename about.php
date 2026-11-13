<?php
require __DIR__ . '/includes/bootstrap.php';
require_database();

$count   = count(catalog_systems());
$sectors = catalog_by_sector();

page_header([
    'title'       => 'About GSMaRT | ' . company('brand'),
    'description' => 'GSMaRT focuses on practical software for organizations that need clearer records, more consistent processes and dependable operational tools.',
    'current'     => 'about',
    'canonical'   => url('about'),
]);

page_bar(
    'About GSMaRT',
    'A business systems and software solutions company.',
    [['Home', url('home')], ['About GSMaRT', null]]
);
?>

<section class="section section--flush" aria-labelledby="about-what">
  <div class="wrap rail">
    <div class="rail__side">
      <h2 id="about-what">What we do</h2>
    </div>
    <div class="prose prose--large">
      <p>GSMaRT focuses on practical software for organizations that need clearer records, more consistent processes and dependable operational tools.</p>
      <p>We build systems for the work a business repeats every day: taking an order, reading a meter, enrolling a student, checking in a guest, running a payroll. Each system is written around how that work is actually done, in the terms the people doing it already use.</p>
<?php if (is_review()): ?>
      <p class="placeholder-block">[Company background, year established, team and office location to be supplied by GSMaRT. This note appears only in review mode. Replace it with verified information in about.php.]</p>
<?php endif; ?>
    </div>
  </div>
</section>

<section class="section section--tint" aria-labelledby="about-how">
  <div class="wrap rail">
    <div class="rail__side">
      <h2 id="about-how">How we approach a system</h2>
    </div>
    <dl class="ledger ledger--wide">
      <div class="ledger__row">
        <dt>Start from the process</dt>
        <dd>Before a screen is designed we ask who records what, on which form, and what happens next. The system follows that sequence.</dd>
      </div>
      <div class="ledger__row">
        <dt>Record once</dt>
        <dd>A transaction entered at the counter should not be typed again for the report. Billing, stock and reports read from the same entry.</dd>
      </div>
      <div class="ledger__row">
        <dt>Keep it traceable</dt>
        <dd>Figures should lead back to the documents behind them, and changes should show who made them.</dd>
      </div>
      <div class="ledger__row">
        <dt>Fit the people using it</dt>
        <dd>A cashier, a clerk and an owner need different screens. Each role gets the part of the system it works with.</dd>
      </div>
    </dl>
  </div>
</section>

<section class="section" aria-labelledby="about-cat">
  <div class="wrap rail">
    <div class="rail__side">
      <h2 id="about-cat">The catalog</h2>
      <p class="section__lead"><?= $count ?> systems across <?= count($sectors) ?> industry groups.</p>
      <p><a class="text-link" href="<?= e(url('systems')) ?>">Browse all systems</a></p>
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

<section class="section section--tint" aria-labelledby="about-cta">
  <div class="wrap cta-row">
    <div>
      <h2 id="about-cta">Tell us what your business needs.</h2>
      <p class="section__lead">A short description of your operation is enough to start the conversation.</p>
    </div>
    <a class="btn btn--primary" href="<?= e(url('contact')) ?>">Talk to GSMaRT</a>
  </div>
</section>

<?php page_footer(); ?>
