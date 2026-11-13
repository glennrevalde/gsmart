<?php
require __DIR__ . '/includes/bootstrap.php';
require_database();

$systems = catalog_systems();
$sectors = catalog_by_sector();

// Filters also work without JavaScript: ?q=billing&sector=retail-supply
$q      = isset($_GET['q']) && is_string($_GET['q']) ? mb_substr(trim($_GET['q']), 0, 80) : '';
$sector = isset($_GET['sector']) && is_string($_GET['sector']) && isset($sectors[$_GET['sector']]) ? $_GET['sector'] : '';

$visible = 0;
foreach ($systems as $i => $system) {
    $systems[$i]['match'] = catalog_matches($system, $q, $sector);
    $visible += $systems[$i]['match'] ? 1 : 0;
}
$total = count($systems);

page_header([
    'title'       => 'Our Systems | ' . company('brand'),
    'description' => 'Browse GSMaRT systems by industry: accounting, payroll, school management, water billing, retail, hospitality, services, logistics and construction.',
    'current'     => 'systems',
    'canonical'   => url('systems'),
]);

// Search and industry filter sit in the page bar, so the wall of systems starts right under it.
ob_start();
?>
<form class="catalog-tools" method="get" action="<?= e(url('systems')) ?>" role="search" aria-label="Filter systems" data-catalog-form>
  <div class="catalog-field catalog-field--search">
    <label for="catalog-q">Search</label>
    <div class="catalog-search__box">
      <svg viewBox="0 0 20 20" width="18" height="18" aria-hidden="true" focusable="false"><circle cx="8.5" cy="8.5" r="5.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M12.8 12.8 17 17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
      <input type="search" id="catalog-q" name="q" value="<?= e($q) ?>" placeholder="System or business need" autocomplete="off" maxlength="80" data-catalog-q>
    </div>
  </div>
  <div class="catalog-field">
    <label for="catalog-sector">Industry</label>
    <select id="catalog-sector" name="sector" data-catalog-sector>
      <option value="">All industries</option>
<?php foreach ($sectors as $s): ?>
      <option value="<?= e($s['slug']) ?>"<?= $sector === $s['slug'] ? ' selected' : '' ?>><?= e($s['name']) ?></option>
<?php endforeach; ?>
    </select>
  </div>
  <noscript><button class="btn btn--line btn--sm" type="submit">Apply</button></noscript>
</form>
<?php
$tools = ob_get_clean();
?>

<div class="catalog" data-catalog data-total="<?= $total ?>">
<?php
page_bar(
    'Our Systems',
    'Explore systems for different industries and operational needs. Select a solution to see its functions, workflows and available options.',
    [['Home', url('home')], ['Our Systems', null]],
    $tools
);
?>

<section class="wall-section wall-section--catalog" aria-label="System catalog">
  <div class="wrap">
    <div class="catalog-status">
      <p aria-live="polite" data-catalog-count>Showing <span data-count-n><?= $visible ?> of <?= $total ?></span> systems</p>
      <a class="text-link" href="<?= e(url('systems')) ?>" data-catalog-reset<?= ($q === '' && $sector === '') ? ' hidden' : '' ?>>Clear search and filter</a>
    </div>

    <?php /* Cards are laid out as a plain grid here. site.js rearranges them into staggered columns. */ ?>
    <div class="wall" data-wall data-catalog-grid>
<?php foreach ($systems as $n => $system): ?>
      <div class="wall__cell" data-cell<?= $system['match'] ? '' : ' hidden' ?>><?php system_card($system, 2, $n); ?></div>
<?php endforeach; ?>
    </div>

    <div class="empty-state" data-catalog-empty<?= $visible ? ' hidden' : '' ?>>
      <h2>No system matches <span data-catalog-term><?= $q !== '' ? '“' . e($q) . '”' : 'that filter' ?></span>.</h2>
      <p>Try a broader word such as “billing”, “inventory” or “booking”, or choose a different industry. If what you need is not in the catalog, it may be a case for custom development.</p>
      <div class="empty-state__actions">
        <a class="btn btn--outline" href="<?= e(url('systems')) ?>" data-catalog-reset>Show all systems</a>
        <a class="btn btn--ghost-light" href="<?= e(url('contact', null, ['type' => 'custom'])) ?>">Describe what you need</a>
      </div>
    </div>

    <?= preview_note() ?>
  </div>
</section>
</div>

<section class="section section--tint" aria-labelledby="custom-title">
  <div class="wrap cta-row">
    <div>
      <h2 id="custom-title">Need something that is not listed?</h2>
      <p class="section__lead">Tell us how the work is done today. If an existing system can be adjusted we will say so, and if it needs to be built we will explain what that involves.</p>
    </div>
    <a class="btn btn--primary" href="<?= e(url('contact', null, ['type' => 'custom'])) ?>">Discuss Custom Development</a>
  </div>
</section>

<?php page_footer(); ?>
