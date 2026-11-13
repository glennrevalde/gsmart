<?php
require __DIR__ . '/includes/bootstrap.php';
require_database();

page_header([
    'title'       => 'Privacy notice | ' . company('brand'),
    'description' => 'How the GSMaRT website handles the details you send through the inquiry form.',
    'canonical'   => url('privacy'),
]);

page_bar('Privacy notice', 'What this website collects and what it is used for.', [['Home', url('home')], ['Privacy notice', null]]);
?>

<section class="section section--flush">
  <div class="wrap">
    <div class="prose legal">
<?php if (is_review()): ?>
      <p class="placeholder-block">[Legal placeholder. The sections below describe what this website technically does. GSMaRT must review them with its adviser, add the company's registered details, data protection contact and retention period, and approve the text before publishing.]</p>
<?php endif; ?>

      <h2>What the inquiry form collects</h2>
      <p>When you send an inquiry we receive what you type into the form: your name, your company or organization if you give it, your e-mail address, your phone number if you give it, the system you are asking about, the type of inquiry and your message.</p>
      <p>Along with it the website records the date and time, the internet address the inquiry came from and the name of your browser. These are used to limit automated and repeated submissions.</p>

      <h2>What it is used for</h2>
      <p>Your details are used to reply to your inquiry and to arrange a demo if you ask for one. They are not used for a mailing list.</p>

      <h2>Where it is kept</h2>
      <p>Inquiries are stored in the website's database and can be read by authorized GSMaRT staff. A copy may be sent by e-mail to the GSMaRT staff who handle inquiries.</p>

      <h2>Cookies</h2>
      <p>The website sets one session cookie. It protects the inquiry form against forged submissions and keeps staff signed in to the administration area. The website does not set advertising or analytics cookies.</p>

      <h2>Your requests</h2>
      <p>To ask what we hold about you, or to have an inquiry corrected or removed, contact us:
        <?= detail_or_placeholder(company('email'), 'Data protection contact e-mail') ?: 'use the <a href="' . e(url('contact')) . '">inquiry form</a>' ?>.</p>

<?php if (is_review()): ?>
      <p class="placeholder-block">[To be supplied: registered company name and address, how long inquiries are retained, and the effective date of this notice.]</p>
<?php endif; ?>
    </div>
  </div>
</section>

<?php page_footer(); ?>
