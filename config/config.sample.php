<?php
/**
 * Environment configuration.
 *
 * Copy this file to config/config.php and edit the copy. config.php is the
 * file to keep out of version control. If config.php does not exist, the site
 * falls back to this sample, which matches a default local XAMPP/WAMP setup.
 *
 * Each value can also be supplied as a server environment variable, which
 * takes priority over this file:
 *   GSMART_DB_HOST, GSMART_DB_PORT, GSMART_DB_NAME, GSMART_DB_USER, GSMART_DB_PASS,
 *   GSMART_SITE_URL, GSMART_APP_KEY, GSMART_NOTIFY_EMAIL, GSMART_FROM_EMAIL
 */

return [

    // MySQL connection (MySQL 5.5 or later).
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'gsmart_website',
        'user'    => 'root',
        'pass'    => 'v1i1n1x1',
        'charset' => 'utf8mb4',
    ],

    // Public address of the site without a trailing slash, for example
    // https://www.example.com. Used for canonical links, the sitemap and
    // e-mail notifications. Leave blank to detect it from the request.
    'site_url' => '',

    // Clean addresses such as /systems/laundry-management.
    //   'auto'  use them when Apache mod_rewrite and the .htaccess file are active
    //   true    always use them (set this on Nginx after adding the rewrite rules in the README)
    //   false   always use plain addresses such as system.php?slug=laundry-management
    'pretty_urls' => 'auto',

    // A long random string, unique to this installation. It signs the inquiry
    // form's timing token. Change it before going live.
    'app_key' => 'change-this-to-a-long-random-string',

    // Review mode shows a notice bar and labels unconfirmed business details
    // as placeholders, and tells search engines not to index the site.
    // Set to false only when the content checklist in the README is complete.
    'review_mode' => true,

    'inquiry' => [
        // Every inquiry is saved to the database. To also receive an e-mail
        // for each one, set the address here. Leave blank to skip e-mail.
        'notify_email' => '',

        // The From address for notification e-mails. It should be an address
        // on the site's own domain or the message may be rejected.
        'from_email' => '',

        // Spam controls.
        'max_per_hour' => 5,   // inquiries accepted per visitor address per hour
        'min_seconds'  => 4,   // a form submitted faster than this is treated as automated
    ],

    'timezone' => 'Asia/Manila',

    // Show database and PHP error details in the browser. Keep false in production.
    'debug' => false,
];
