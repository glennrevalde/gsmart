<?php
require __DIR__ . '/includes/bootstrap.php';
require_database();

page_header([
    'title'       => 'Terms of use | ' . company('brand'),
    'description' => 'Terms for using the GSMaRT website.',
    'canonical'   => url('terms'),
]);

page_bar('Terms of use', 'Terms for using this website.', [['Home', url('home')], ['Terms of use', null]]);
?>

<section class="section section--flush">
  <div class="wrap">
    <div class="prose legal">
<?php if (is_review()): ?>
      <p class="placeholder-block">[Legal placeholder. GSMaRT's approved terms of use will be published here. The points below are a starting outline only and are not legal advice.]</p>
<?php endif; ?>

      <h2>About the information on this site</h2>
      <p>This website describes software systems and services offered by GSMaRT. Descriptions are general. The functions, platforms and price of a system are confirmed in writing for each engagement.</p>

      <h2>Interface previews</h2>
      <p>The screens shown on this site are illustrations with sample data. They show the kind of screen a system has and are not screenshots of a client installation.</p>

      <h2>Inquiries</h2>
      <p>Sending an inquiry or a demo request does not create a contract. How the details you send are handled is described in the <a href="<?= e(url('privacy')) ?>">privacy notice</a>.</p>

      <h2>Ownership</h2>
      <p>The text and design of this website belong to <?= e(company('legal_name', company('brand'))) ?> unless stated otherwise.</p>

<?php if (is_review()): ?>
      <p class="placeholder-block">[To be supplied: governing law, limitation of liability, and the effective date of these terms.]</p>
<?php endif; ?>
    </div>
  </div>
</section>

<?php page_footer(); ?>
