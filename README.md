# GSMaRT corporate website

A complete company website for GSMaRT: home page, systems catalog with search and
industry filter, a detail page for every system, Industries, Services, About and
Contact pages, a working inquiry form, and a small admin area for inquiries and
the catalog.

Built with HTML, CSS, JavaScript, PHP and MySQL 5.5. No framework, no build
step, no external CDN: fonts and scripts are served from this folder.

| | |
|---|---|
| PHP | 5.6 or later (written to run on 5.6 through 8.x; tested on 8.3) with the `pdo_mysql` and `mbstring` extensions |
| Database | MySQL 5.5 or later (or MariaDB) |
| Web server | Apache with `mod_rewrite` for clean addresses. Works without it using plain `.php` addresses. Nginx rules are below. |


## 1. Run it locally

### XAMPP / WAMP / Laragon

1. Copy the `gsmart-website` folder into `htdocs` (or `www`).
2. Open phpMyAdmin, go to **Import**, and import one of the files in `database/`:
   - `gsmart_website_demo.sql` gives you the catalog, five sample inquiries and a demo admin account.
   - `gsmart_website_clean.sql` gives you the catalog only.

   Both files create a database called `gsmart_website`.
3. Open `http://localhost/gsmart-website/`.

The site reads `config/config.sample.php` when `config/config.php` does not exist.
The sample matches a default XAMPP install (user `root`, no password), so the
steps above are enough to see the site. To use different database details, copy
`config/config.sample.php` to `config/config.php` and edit the copy.

### PHP's built-in server

```
php -S localhost:8000 router.php
```

`router.php` mirrors the Apache rules and is only for local testing.

### If a page says "The site cannot reach its database yet"

Set `'debug' => true` in the config file and reload. The page then shows the
database error. Turn `debug` off again afterwards.


## 2. Admin area

Address: `/admin/`

| Database file | How to sign in |
|---|---|
| Demo | Username `admin`, password `Gsmart#Demo2026`. Change it under **Account** straight away. |
| Clean | No account exists. The first visit to `/admin/` opens a one-time page to create the administrator. |

What it does:

- **Inquiries**: every form submission, with status (New, In progress, Closed), internal notes, reply-by-e-mail link and delete.
- **Systems**: publish or hide a system, choose which six appear on the home page, add, edit and delete systems, including modules, workflow steps, roles, implementation notes and availability.
- **Account**: change your password.

A system that is hidden disappears from the catalog, the inquiry form, the
Industries page and the sitemap, and its address returns "Page not found".


## 3. Folder structure

```
gsmart-website/
  index.php            Home
  systems.php          Our Systems (catalog)
  system.php           System detail template, one for all systems
  industries.php       Industries
  services.php         Services
  about.php            About GSMaRT
  contact.php          Contact / Request a Demo (also receives the form)
  privacy.php          Privacy notice (legal placeholder)
  terms.php            Terms of use (legal placeholder)
  404.php              Page not found
  sitemap.php          Served as /sitemap.xml
  robots.php           Served as /robots.txt
  .htaccess            Apache rules: clean addresses, private folders, caching
  router.php           Local testing with PHP's built-in server

  config/
    config.sample.php  Environment settings (copy to config.php)
    company.php        Business details, services, process, inquiry types
  content/
    previews.php       Sample screens drawn on cards and detail pages
  includes/
    bootstrap.php      Loaded by every page: config, session, helpers, addresses
    db.php             Database connection and query helpers (PDO, prepared statements)
    catalog.php        Catalog queries
    preview.php        Draws the interface previews and device frames
    layout.php         Header, footer, page bar, system card
    inquiry.php        Inquiry form, validation, saving, e-mail notification
    setup-needed.php   Shown when the database is unreachable
  admin/               Sign-in, inquiries, systems, account
  assets/
    css/site.css       All public styles (sections are numbered at the top)
    css/admin.css      Admin styles
    js/site.js         All behaviour (functions are listed at the top)
    fonts/             Archivo and IBM Plex Sans (SIL Open Font License)
    img/               Favicon and social-share placeholder
  database/
    gsmart_website_clean.sql
    gsmart_website_demo.sql
  tools/
    catalog-source.php Starter catalog text
    build-sql.php      Rebuilds both SQL files from catalog-source.php
```

`config`, `includes`, `content`, `database` and `tools` are blocked from
browsers by `.htaccess` files. On a host where you can choose the web root, the
safest setup is still to keep them there and rely on those rules, or move the
project so that only what must be public is under the web root.


## 4. Changing content

| To change | Edit |
|---|---|
| A system's text, modules, workflow, roles, order, visibility | Admin > Systems |
| Add or remove a system | Admin > Systems |
| Contact e-mail, address, legal name | `config/company.php` |
| Services, engagement steps, "Built for real operations" list | `config/company.php` |
| Inquiry type options | `config/company.php` |
| Sample screens on cards and detail pages | `content/previews.php` |
| Screens on the home page devices | `'_hero'` at the top of `content/previews.php` |
| Wall of systems: column offsets, scroll speeds, card shapes | "The wall" and "System card" in section 5 of `assets/css/site.css` |
| Home page headline and section text | `index.php` |
| About page text | `about.php` |
| Privacy and terms text | `privacy.php`, `terms.php` |
| Logo | `brand_mark()` in `includes/layout.php` and `assets/img/favicon.svg` |
| Colours, type, spacing | the `:root` block at the top of `assets/css/site.css` |

Animation code lives only in `assets/js/site.js` and the "Devices and 3D stage"
part of `site.css`. None of the content changes above touch it.

### Adding a system

1. Admin > Systems > **Add a system**.
2. Fill in the name, industry group, industry label and card description.
3. Enter modules, workflow steps and roles one per line as `Name | Description`.
4. Tick **Published** and save.

The new system gets a preview built from its own modules and workflow. To give
it custom screens, add an entry to `content/previews.php` using its address
(slug) as the key. The comment at the top of that file lists the block types.

### Rebuilding the SQL files

`tools/catalog-source.php` holds the starter text. After editing it:

```
php tools/build-sql.php
```

This rewrites both files in `database/`. Importing them replaces the tables, so
do this for a fresh install, not on a live site with real inquiries.


## 5. Inquiry form

- Every accepted inquiry is saved in the `inquiries` table. That is the delivery
  the visitor is told about, and the success message is shown only after the
  save succeeds.
- To also get an e-mail for each inquiry, set `inquiry.notify_email` (and
  `inquiry.from_email`, an address on your own domain) in `config/config.php`.
  This uses PHP `mail()`, so the server must be able to send mail. On XAMPP it
  usually cannot until you configure it. If the e-mail fails the inquiry is
  still saved and the admin page shows "Notification e-mail: Not sent".
- Spam controls: CSRF token, a hidden field, a signed timing token (a form sent
  within 4 seconds is refused), length limits, and 5 inquiries per visitor
  address per hour. The last two numbers are in the config file.
- A card or detail page passes its system to the form
  (`/contact?system=laundry-management&type=demo`). The visitor can change it.
- No calendar or booking service is connected. Demos are arranged by reply.


## 6. Review mode and the content checklist

`'review_mode' => true` in the config file (the default):

- shows a notice bar on every page,
- marks missing business details as `[... to be supplied]`,
- tells search engines not to index the site.

Go through this list, then set `review_mode` to `false`.

- [ ] **Systems**: confirm which of the 16 starter systems GSMaRT offers. Hide the rest in Admin > Systems.
- [ ] **System text**: check every module, workflow step, role and note against the real product.
- [ ] **Availability**: fill in platforms, deployment, integrations and pricing per system, only where verified. Blank fields are left off the page.
- [ ] **Interface previews**: the screens are illustrations with sample data and are labelled as such. Replace them with real screenshots or approved sample data when available.
- [ ] **Contact details**: the e-mail is set. The site shows no phone number and no office hours. Add the office address in `config/company.php`, or leave it out.
- [ ] **Legal name**: `legal_name` in `config/company.php` is set to "G-SMaRT Technologies Inc." Correct it if the registered name differs.
- [ ] **Services and process**: remove any service GSMaRT does not provide and confirm the six engagement steps (`config/company.php`).
- [ ] **About page**: add verified company background in `about.php`.
- [ ] **Privacy notice and terms**: have them reviewed and approved. Both pages are placeholders.
- [ ] **Logo, favicon and social image**: replace the placeholder mark and `assets/img/og-placeholder.png` (1200 x 630).
- [ ] **Demo data**: delete the sample inquiries and change or delete the demo admin account.

The site contains no testimonials, client names, statistics, partner badges or
prices. Add them only with documentation.


## 7. Going live

1. Upload the folder (or its contents, to serve the site from the domain root).
2. Create the database and import `database/gsmart_website_clean.sql`. If your
   host has already created the database, remove the `CREATE DATABASE` and `USE`
   lines at the top of the file first.
3. Copy `config/config.sample.php` to `config/config.php` and set:
   - the database name, user and password,
   - `site_url` to the public address, for example `https://www.example.com`,
   - `app_key` to a long random string,
   - `inquiry.notify_email` and `inquiry.from_email` if you want e-mail,
   - `debug` to `false`.
4. Visit `/admin/` and create the administrator.
5. Serve the site over HTTPS. The session cookie is then marked secure automatically.
6. Complete the checklist in section 6 and set `review_mode` to `false`.
7. Submit `/sitemap.xml` to search engines.

Instead of the config file, the database details and keys can be given as
server environment variables. They are listed at the top of `config.sample.php`.

### Clean addresses

With Apache, `mod_rewrite` and `AllowOverride All`, addresses look like
`/systems/laundry-management`. The site detects this by itself. If rewriting is
not available it prints working `system.php?slug=laundry-management` addresses
instead, so no link breaks either way.

If the site is reached through an Apache `Alias`, add a `RewriteBase` line with
the alias path after `RewriteEngine On` in `.htaccess`.

### Nginx

```nginx
root /var/www/gsmart-website;
index index.php;

location ~ ^/(config|includes|content|database|tools)(/|$) { return 403; }
location ~ /\.                 { return 403; }
location ~ \.(md|sql|log)$     { return 403; }

location = /sitemap.xml { rewrite ^ /sitemap.php last; }
location = /robots.txt  { rewrite ^ /robots.php last; }
location ~ ^/systems/([a-z0-9-]+)/?$ { rewrite ^ /system.php?slug=$1 last; }

location / { try_files $uri $uri/ @page; }
location @page {
    rewrite ^/([a-z0-9-]+)/?$ /$1.php last;
    rewrite ^ /404.php last;
}
location ~ \.php$ {
    try_files $uri /404.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    fastcgi_pass unix:/run/php/php-fpm.sock;
}
```

Then set `'pretty_urls' => true` in the config file.


## 8. Design notes

- **Palette**: navy `#10263B`, blue `#1C5CA8`, graphite `#223447`, gray `#5D6D7B`, warm white `#FAFBFC`, as specified.
- **Type**: Archivo for headings, IBM Plex Sans for text and interface previews. Both are self-hosted.
- **3D**: built with CSS 3D transforms on real HTML interface previews, so text stays sharp and no 3D library is downloaded. The home page scene is a hinged laptop, a tablet, a phone and a window at different depths. It turns a few degrees with the pointer, tilts with scroll, and stops updating when it is off screen or the tab is in the background. Catalog cards have two layered windows that separate on hover or keyboard focus, with a light that follows the cursor.
- **Wall of systems** (home page and Our Systems): a dark navy section with a faint grid. The system cards have pictures of different heights and sit in staggered columns: three on a desktop, two on a tablet, one on a phone. The columns move at slightly different speeds as the page scrolls. On the home page the heading and the **View All Systems** button sit at the top of the first column. Each card keeps its layered 3D preview, which tilts and separates under the pointer.
- **Search and industry filter** on Our Systems are in the page bar. Filtering re-sorts the remaining cards into the columns. Without JavaScript the cards are a plain grid and the filters still work.
- **Fallbacks**: on touch devices the scenes are static and cards do not tilt. With "reduce motion" switched on, pointer movement, parallax, column drift, reveals and transitions are off and the content is unchanged. On phones the detail-page screen is shown flat and larger.
- **Layout**: the home page keeps the hero that the brief specifies. Every inner page opens with a compact title bar.


## 9. Testing checklist

What was checked before delivery (PHP 8.3, MariaDB 10.11, Chromium):

- Every route returns the right status under Apache in a subfolder, with and without `mod_rewrite`, and at the root on the PHP built-in server.
- A crawl of every internal link, script, stylesheet and form action finds no dead link.
- Private folders, dotfiles and `.sql`/`.md` files return 403.
- No horizontal overflow and one `h1` per page at 320, 375, 390, 430, 768, 1024, 1440 and 1920 px wide, and at 844 x 390 landscape.
- Catalog: live search, industry filter, result count, clear, empty state, address stays in sync, and the same filters work with JavaScript off.
- Wall of systems: three, two and one column at desktop, tablet and phone widths; columns drift on scroll and stay still with reduced motion; filtering and resizing re-sort the cards; hover tilt still works after sorting; plain grid with JavaScript off.
- Detail page: screen tabs by click and arrow keys, system preselected in the form.
- Inquiry form: inline errors, server errors with JavaScript off, too-fast submission refused with no success message, successful save, text containing HTML and SQL stored and displayed safely.
- Admin: first-run setup, sign-in, wrong password, inquiry status and notes, publish and hide (hidden system returns 404 and leaves the sitemap), add, edit and delete a system, sign-out.
- Pointer and scroll motion on the home scene, card tilt and reset, reduced-motion behaviour, mobile menu open, close, Escape and focus return.

Not tested here, so check on your own server:

- A real MySQL 5.5 server. The SQL avoids everything newer than 5.5 (no JSON type, no `CURRENT_TIMESTAMP` on `DATETIME`, indexed text columns within the 767-byte limit) and was run on MariaDB 10.11.
- PHP versions below 8.3. The code avoids syntax and functions newer than PHP 5.6.
- Outgoing e-mail, which depends on the server's mail setup.
- The Nginx rules in section 7. They are a starting point written from the Apache rules.
- Safari and Firefox, and real phones.
- Lighthouse scores. None are claimed.

Repeat after any change:

- [ ] Home, catalog, one detail page and contact at phone, tablet and desktop widths
- [ ] Send one inquiry and find it in the admin area
- [ ] Hide a system and confirm it is gone from the public site
- [ ] Tab through the header, a card, the screen tabs and the form with the keyboard
#   g s m a r t  
 