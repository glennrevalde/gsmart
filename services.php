<?php
require __DIR__ . '/includes/bootstrap.php';
require_database();

$services = company('services', []);
$process  = company('process', []);

page_header([
    'title'       => 'Services | ' . company('brand'),
    'description' => 'What GSMaRT does around its software: custom development, setup and configuration, deployment, training, maintenance and technical support.',
    'current'     => 'services',
    'canonical'   => url('services'),
]);

page_bar(
    'Services',
    'The work that turns a system into something your staff use every day.',
    [['Home', url('home')], ['Services', null]]
);
?>

<section class="section section--flush" aria-labelledby="svc-list">
  <div class="wrap rail">
    <div class="rail__side">
      <h2 id="svc-list">What we do</h2>
      <p class="section__lead">Most engagements use several of these. Which ones you need depends on whether an existing system fits and how your team is set up.</p>
    </div>
    <dl class="ledger ledger--wide">
<?php foreach ($services as $svc): ?>
      <div class="ledger__row">
        <dt><?= e($svc['name']) ?></dt>
        <dd><?= e($svc['text']) ?></dd>
      </div>
<?php endforeach; ?>
    </dl>
  </div>
</section>

<section class="section section--tint" aria-labelledby="proc-title">
  <div class="wrap">
    <div class="section__head">
      <h2 id="proc-title">How an engagement works</h2>
      <p class="section__lead">From the first conversation to support after go-live.</p>
    </div>
    <ol class="steps" data-reveal>
<?php foreach ($process as $step): ?>
      <li>
        <h3><?= e($step['name']) ?></h3>
        <p><?= e($step['text']) ?></p>
      </li>
<?php endforeach; ?>
    </ol>
<?php if (is_review()): ?>
    <p class="review-note">Proposed process and service list, shown pending confirmation by GSMaRT.</p>
<?php endif; ?>
  </div>
</section>

<section class="section" aria-labelledby="svc-cta">
  <div class="wrap cta-row">
    <div>
      <h2 id="svc-cta">Start with the first step</h2>
      <p class="section__lead">Tell us about the operation. A short description of what you do and how you record it today is enough to begin.</p>
    </div>
    <a class="btn btn--primary" href="<?= e(url('contact')) ?>">Talk to GSMaRT</a>
  </div>
</section>

<?php page_footer(); ?>
