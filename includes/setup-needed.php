<?php /** Shown (with HTTP 503) when the database cannot be reached. $detail is set only in debug mode. */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>GSMaRT | Site setup needed</title>
<style>
  body { margin: 0; font: 16px/1.6 system-ui, -apple-system, "Segoe UI", sans-serif; color: #223447; background: #FAFBFC; }
  main { max-width: 40rem; margin: 12vh auto; padding: 0 1.5rem; }
  h1 { font-size: 1.6rem; line-height: 1.25; color: #10263B; margin: 0 0 .75rem; }
  ol { padding-left: 1.2rem; }
  li { margin: .4rem 0; }
  code { background: #EDF1F5; padding: .1rem .35rem; border-radius: 3px; font-size: .92em; }
  .detail { margin-top: 1.5rem; padding: .9rem 1rem; border-left: 3px solid #1C5CA8; background: #fff; font-size: .9rem; word-break: break-word; }
</style>
</head>
<body>
<main>
  <h1>The site cannot reach its database yet.</h1>
  <p>The pages are in place, but the catalog is read from MySQL and the connection is not working.</p>
  <ol>
    <li>Import <code>database/gsmart_website_clean.sql</code> (or the demo file) into MySQL.</li>
    <li>Copy <code>config/config.sample.php</code> to <code>config/config.php</code> and set the database name, user and password.</li>
    <li>Reload this page.</li>
  </ol>
<?php if (!empty($detail)): ?>
  <p class="detail"><strong>Database message:</strong> <?= htmlspecialchars($detail, ENT_QUOTES, 'UTF-8') ?></p>
<?php else: ?>
  <p>To see the database error here, set <code>'debug' =&gt; true</code> in the config file while you are setting up.</p>
<?php endif; ?>
</main>
</body>
</html>
