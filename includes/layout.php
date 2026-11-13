<?php
/**
 * Shared page furniture: document head, header, footer and the reusable
 * components (page bar, system card, inquiry form).
 */

function brand_mark()
{
    // Placeholder mark: three stacked application windows. Replace with the
    // official GSMaRT logo when it is supplied (see README, content checklist).
    return '<svg class="brand__mark" viewBox="0 0 32 32" width="30" height="30" aria-hidden="true" focusable="false">'
        . '<rect x="9" y="3" width="20" height="14" rx="2" fill="#B9C6D3"/>'
        . '<rect x="6" y="8" width="20" height="14" rx="2" fill="#1C5CA8"/>'
        . '<rect x="3" y="13" width="20" height="15" rx="2" fill="#10263B"/>'
        . '<rect x="6" y="17" width="9" height="2" rx="1" fill="#FAFBFC"/>'
        . '<rect x="6" y="21" width="14" height="2" rx="1" fill="#5D6D7B"/>'
        . '</svg>';
}

/**
 * Open the document.
 * $page keys: title, description, current (nav key), canonical (relative url),
 *             robots, body_class
 */
function page_header(array $page)
{
    $brand   = company('brand', 'GSMaRT');
    $title   = isset($page['title']) ? $page['title'] : $brand;
    $desc    = isset($page['description']) ? $page['description'] : company('meta_description', '');
    $current = isset($page['current']) ? $page['current'] : '';
    $canon   = isset($page['canonical']) ? absolute_url($page['canonical']) : null;
    $robots  = is_review() ? 'noindex, nofollow' : (isset($page['robots']) ? $page['robots'] : 'index, follow');
    $ogImage = absolute_url(base_path() . '/assets/img/og-placeholder.png');
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($desc) ?>">
<meta name="robots" content="<?= e($robots) ?>">
<?php if ($canon): ?>
<link rel="canonical" href="<?= e($canon) ?>">
<meta property="og:url" content="<?= e($canon) ?>">
<?php endif; ?>
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e($brand) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($desc) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>"><!-- Placeholder image: replace assets/img/og-placeholder.png -->
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#10263B">
<link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
<link rel="preload" href="<?= e(base_path() . '/assets/fonts/archivo-variable.woff2') ?>" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?= e(base_path() . '/assets/fonts/ibm-plex-sans-400.woff2') ?>" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>">
<script>document.documentElement.className += ' js';</script>
<script src="<?= e(asset('js/site.js')) ?>" defer></script>
</head>
<body class="<?= e(isset($page['body_class']) ? $page['body_class'] : '') ?>">
<a class="skip-link" href="#main">Skip to main content</a>
<?php if (is_review()): ?>
<div class="review-bar" role="note">
  <div class="wrap">Review copy. Systems, contact details and policies shown here are awaiting confirmation and the site is hidden from search engines.</div>
</div>
<?php endif; ?>
<header class="site-header" data-header>
  <div class="wrap site-header__in">
    <a class="brand" href="<?= e(url('home')) ?>" aria-label="<?= e($brand) ?> home">
      <?= brand_mark() ?><span class="brand__name"><?= e($brand) ?></span>
    </a>
    <nav class="site-nav" aria-label="Main">
      <ul>
<?php foreach (nav_items() as $key => $label): ?>
        <li><a href="<?= e(url($key)) ?>"<?= $current === $key ? ' aria-current="page"' : '' ?>><?= e($label) ?></a></li>
<?php endforeach; ?>
      </ul>
    </nav>
    <a class="btn btn--primary btn--sm site-header__cta" href="<?= e(url('contact', null, ['type' => 'demo'])) ?>">Request a Demo</a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-drawer" data-drawer-open>
      <span class="nav-toggle__bars" aria-hidden="true"><i></i><i></i><i></i></span><span>Menu</span>
    </button>
  </div>
</header>

<div class="drawer" id="site-drawer" data-drawer hidden>
  <div class="drawer__scrim" data-drawer-close></div>
  <div class="drawer__panel" role="dialog" aria-modal="true" aria-label="Site menu">
    <div class="drawer__head">
      <span class="brand__name"><?= e($brand) ?></span>
      <button class="drawer__close" type="button" data-drawer-close>Close</button>
    </div>
    <nav aria-label="Mobile">
      <ul class="drawer__links">
        <li><a href="<?= e(url('home')) ?>"<?= $current === 'home' ? ' aria-current="page"' : '' ?>>Home</a></li>
<?php foreach (nav_items() as $key => $label): ?>
        <li><a href="<?= e(url($key)) ?>"<?= $current === $key ? ' aria-current="page"' : '' ?>><?= e($label) ?></a></li>
<?php endforeach; ?>
      </ul>
    </nav>
    <a class="btn btn--primary btn--block" href="<?= e(url('contact', null, ['type' => 'demo'])) ?>">Request a Demo</a>
  </div>
</div>

<main id="main" tabindex="-1">
<?php
}

function page_footer()
{
    $brand = company('brand', 'GSMaRT');
    $legal = company('legal_name', $brand);
    $email = company('email');
    ?>
</main>

<footer class="site-footer">
  <div class="wrap site-footer__grid">
    <div class="site-footer__brand">
      <a class="brand brand--light" href="<?= e(url('home')) ?>"><?= brand_mark() ?><span class="brand__name"><?= e($brand) ?></span></a>
      <p><?= e(company('descriptor', '')) ?>. We build software for billing, inventory, workforce, reservations and reporting.</p>
    </div>
    <nav aria-label="Footer">
      <h2 class="site-footer__h">Site</h2>
      <ul>
        <li><a href="<?= e(url('home')) ?>">Home</a></li>
<?php foreach (nav_items() as $key => $label): ?>
        <li><a href="<?= e(url($key)) ?>"><?= e($label) ?></a></li>
<?php endforeach; ?>
      </ul>
    </nav>
    <div>
      <h2 class="site-footer__h">Contact</h2>
      <ul class="site-footer__contact">
<?php if ($email): ?>
        <li><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></li>
<?php elseif (is_review()): ?>
        <li><?= detail_or_placeholder(null, 'E-mail address') ?></li>
<?php endif; ?>
<?php $address = detail_or_placeholder(company('address'), 'Office address'); if ($address !== ''): ?>
        <li><?= $address ?></li>
<?php endif; ?>
        <li><a href="<?= e(url('contact')) ?>">Send an inquiry</a></li>
      </ul>
    </div>
    <div>
      <h2 class="site-footer__h">Legal</h2>
      <ul>
        <li><a href="<?= e(url('privacy')) ?>">Privacy notice</a></li>
        <li><a href="<?= e(url('terms')) ?>">Terms of use</a></li>
      </ul>
    </div>
  </div>
  <div class="wrap site-footer__base">
    <p>&copy; <?= date('Y') ?> <?= e(rtrim($legal, '.')) ?>. All rights reserved.</p>
  </div>
</footer>
</body>
</html>
<?php
}

/**
 * Compact title bar used at the top of every inner page.
 * $crumbs: list of [label, url|null]
 */
function page_bar($title, $lead = '', array $crumbs = [], $actionsHtml = '')
{
    ?>
<div class="page-bar">
  <div class="wrap page-bar__in">
    <div class="page-bar__text">
<?php if ($crumbs): ?>
      <nav class="crumbs" aria-label="Breadcrumb">
        <ol>
<?php foreach ($crumbs as $crumb): ?>
          <li><?php if (!empty($crumb[1])): ?><a href="<?= e($crumb[1]) ?>"><?= e($crumb[0]) ?></a><?php else: ?><span aria-current="page"><?= e($crumb[0]) ?></span><?php endif; ?></li>
<?php endforeach; ?>
        </ol>
      </nav>
<?php endif; ?>
      <h1><?= e($title) ?></h1>
<?php if ($lead !== ''): ?>
      <p class="page-bar__lead"><?= e($lead) ?></p>
<?php endif; ?>
    </div>
<?php if ($actionsHtml !== ''): ?>
    <div class="page-bar__actions"><?= $actionsHtml ?></div>
<?php endif; ?>
  </div>
</div>
<?php
}

/**
 * One system card for the staggered wall. $system comes from catalog_systems().
 * $index decides the height of the card's picture, so neighbours differ.
 */
function system_card(array $system, $headingLevel = 3, $index = 0)
{
    $shapes  = ['wide', 'tall', 'std', 'square', 'std', 'tall', 'wide', 'square'];
    $shape   = $shapes[$index % count($shapes)];
    $screens = preview_screens($system['slug'], $system);
    $front   = $screens[0];
    $back    = isset($screens[1]) ? $screens[1] : $screens[0];
    $detail  = url('system', $system['slug']);
    $demo    = url('contact', null, ['system' => $system['slug'], 'type' => 'demo']);
    $search  = mb_strtolower(implode(' ', [
        $system['name'], $system['category'], $system['sector_name'], $system['summary'],
        $system['search_terms'], implode(' ', $system['modules']),
    ]));
    $h = 'h' . (int) $headingLevel;
    ?>
<article class="sys-card sys-card--<?= e($shape) ?>" data-card data-sector="<?= e($system['sector_slug']) ?>" data-search="<?= e($search) ?>">
  <a class="sys-card__visual" href="<?= e($detail) ?>" tabindex="-1" aria-hidden="true" data-tilt>
    <span class="sys-card__stage">
      <span class="sys-card__win sys-card__win--back"><?= render_screen($back, 'compact') ?></span>
      <span class="sys-card__win sys-card__win--front"><?= render_screen($front, 'compact') ?></span>
      <span class="sys-card__light"></span>
    </span>
  </a>
  <div class="sys-card__body">
    <div class="sys-card__head">
      <<?= $h ?> class="sys-card__title"><a href="<?= e($detail) ?>"><?= e($system['name']) ?></a></<?= $h ?>>
      <p class="sys-card__cat"><span class="visually-hidden">Industry: </span><?= e($system['category']) ?></p>
    </div>
    <p class="sys-card__sum"><?= e($system['summary']) ?></p>
    <ul class="sys-card__mods" aria-label="Main modules">
<?php foreach (array_slice($system['modules'], 0, 4) as $module): ?>
      <li><?= e($module) ?></li>
<?php endforeach; ?>
    </ul>
    <div class="sys-card__actions">
      <a class="btn btn--outline btn--sm" href="<?= e($detail) ?>">View Details<span class="visually-hidden">: <?= e($system['name']) ?></span></a>
      <a class="btn btn--ghost-light btn--sm" href="<?= e($demo) ?>">Request a Demo<span class="visually-hidden">: <?= e($system['name']) ?></span></a>
    </div>
  </div>
</article>
<?php
}

/** Note shown wherever sample interface previews appear. */
function preview_note()
{
    return '<p class="preview-note">Interface previews are illustrations with sample data, not screenshots of a client installation.</p>';
}
