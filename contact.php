<?php
require __DIR__ . '/includes/bootstrap.php';
require_database();
start_session();

$wantsJson = (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
$result    = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = inquiry_handle($_POST);

    if ($wantsJson) {
        http_response_code($result['status']);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode([
            'ok'       => $result['ok'],
            'message'  => $result['message'],
            'errors'   => $result['errors'],
            // Fresh tokens so the form can be corrected and sent again.
            'csrf'     => csrf_token(),
            'shown_at' => inquiry_time_token(),
        ]);
        exit;
    }
    if ($result['ok']) {
        flash('inquiry_sent', $result['message']);
        redirect(url('contact', null, ['sent' => 1]) . '#inquiry');
    }
    http_response_code($result['status']);
}

$sent = flash('inquiry_sent');

// A system and inquiry type can be passed in from a card or detail page.
$systemSlug = isset($_GET['system']) && is_string($_GET['system']) && catalog_system($_GET['system']) ? $_GET['system'] : '';
$type       = isset($_GET['type']) && is_string($_GET['type']) && array_key_exists($_GET['type'], inquiry_types()) ? $_GET['type'] : 'demo';

header('Cache-Control: no-store');
page_header([
    'title'       => 'Contact and demo requests | ' . company('brand'),
    'description' => 'Ask GSMaRT about a system or request a demo. Tell us what your business needs.',
    'current'     => 'contact',
    'canonical'   => url('contact'),
]);

page_bar(
    'Tell us what your business needs.',
    'Ask about a system, request a demo, or describe something you need built.',
    [['Home', url('home')], ['Contact', null]]
);
?>

<section class="section section--flush" id="inquiry" aria-label="Inquiry form">
  <div class="wrap contact-layout">
    <div class="form-panel form-panel--plain">
<?php if ($sent): ?>
      <div class="form-success" role="status" tabindex="-1" data-autofocus>
        <h2>Inquiry received</h2>
        <p><?= e($sent) ?></p>
        <p><a class="text-link" href="<?= e(url('contact')) ?>">Send another inquiry</a> or <a class="text-link" href="<?= e(url('systems')) ?>">go back to the catalog</a>.</p>
      </div>
<?php else: ?>
      <?php inquiry_form([
          'system' => $systemSlug,
          'type'   => $type,
          'data'   => $result ? $result['data'] : [],
          'errors' => $result ? $result['errors'] : [],
      ]); ?>
<?php endif; ?>
    </div>

    <aside class="contact-aside" aria-label="Other ways to reach us">
      <h2>Other ways to reach us</h2>
      <dl class="contact-list">
<?php
$methods = [
    'E-mail'  => [company('email'), 'E-mail address', 'mailto:'],
    'Office'  => [company('address'), 'Office address', null],
];
$shown = 0;
foreach ($methods as $label => $m):
    $html = detail_or_placeholder($m[0], $m[1]);
    if ($html === '') { continue; }
    $shown++;
    if ($m[0] && $m[2]) {
        $href = $m[2] . $m[0];
        $html = '<a href="' . e($href) . '">' . $html . '</a>';
    }
?>
        <div><dt><?= e($label) ?></dt><dd><?= $html ?></dd></div>
<?php endforeach; ?>
      </dl>
<?php if (!$shown): ?>
      <p>The form on this page is the quickest way to reach us.</p>
<?php endif; ?>

      <h2>What happens after you send it</h2>
      <ol class="mini-steps">
        <li>Your inquiry is saved with a reference number.</li>
        <li>Someone at GSMaRT reads it and replies to your e-mail address.</li>
        <li>If a demo makes sense, we arrange a time with you directly.</li>
      </ol>
<?php if (is_review()): ?>
      <p class="review-note">Demo scheduling is arranged by reply. No online calendar is connected.</p>
<?php endif; ?>
    </aside>
  </div>
</section>

<?php page_footer(); ?>
