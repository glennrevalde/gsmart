<?php
/**
 * Rebuilds the SQL files in /database from tools/catalog-source.php.
 *
 *   php tools/build-sql.php
 *
 * Output:
 *   database/gsmart_website_clean.sql   structure + catalog content, no inquiries, no admin user
 *   database/gsmart_website_demo.sql    the same, plus sample inquiries and a demo admin account
 *
 * All SQL is written for MySQL 5.5: InnoDB, utf8mb4, DATETIME columns filled
 * by PHP (5.5 allows CURRENT_TIMESTAMP on one TIMESTAMP column only), no JSON
 * type, indexed VARCHARs kept to 191 characters.
 */

if (PHP_SAPI !== 'cli') {
    header('HTTP/1.1 403 Forbidden');
    exit('Run from the command line.');
}

$root   = dirname(__DIR__);
$source = require __DIR__ . '/catalog-source.php';
$dbName = 'gsmart_website';
$now    = '2026-01-01 08:00:00';

function q($value)
{
    if ($value === null) {
        return 'NULL';
    }
    if (is_int($value)) {
        return (string) $value;
    }
    return "'" . str_replace(
        ["\\", "'", "\r", "\n"],
        ["\\\\", "''", '', '\n'],
        (string) $value
    ) . "'";
}

function insert_rows($table, array $columns, array $rows)
{
    if (!$rows) {
        return '';
    }
    $lines = [];
    foreach ($rows as $row) {
        $lines[] = '(' . implode(', ', array_map('q', $row)) . ')';
    }
    return 'INSERT INTO `' . $table . '` (`' . implode('`, `', $columns) . "`) VALUES\n"
        . implode(",\n", $lines) . ";\n\n";
}

// ---------------------------------------------------------------- structure
$header = <<<SQL
-- ---------------------------------------------------------------------------
-- GSMaRT corporate website
-- %s
-- Compatible with MySQL 5.5 and later.
--
-- The next two statements create and select the database. Remove them if you
-- are importing into a database that your host has already created.
-- ---------------------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `{$dbName}`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `inquiries`;
DROP TABLE IF EXISTS `system_roles`;
DROP TABLE IF EXISTS `system_workflow_steps`;
DROP TABLE IF EXISTS `system_modules`;
DROP TABLE IF EXISTS `systems`;
DROP TABLE IF EXISTS `sectors`;
DROP TABLE IF EXISTS `admin_users`;

CREATE TABLE `sectors` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(80) NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `summary` VARCHAR(255) NOT NULL DEFAULT '',
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sectors_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `systems` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sector_id` INT UNSIGNED NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `category` VARCHAR(80) NOT NULL,
  `tagline` VARCHAR(255) NOT NULL DEFAULT '',
  `summary` TEXT NOT NULL,
  `audience` TEXT NOT NULL,
  `manages` TEXT NOT NULL,
  `search_terms` VARCHAR(255) NOT NULL DEFAULT '',
  `implementation_notes` TEXT NULL,
  `platforms` VARCHAR(255) NULL,
  `deployment` VARCHAR(255) NULL,
  `integrations` VARCHAR(255) NULL,
  `pricing` VARCHAR(255) NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_systems_slug` (`slug`),
  KEY `ix_systems_listing` (`is_published`, `sort_order`),
  KEY `ix_systems_sector` (`sector_id`),
  CONSTRAINT `fk_systems_sector` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_modules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `system_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `description` VARCHAR(500) NOT NULL DEFAULT '',
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ix_modules_system` (`system_id`, `sort_order`),
  CONSTRAINT `fk_modules_system` FOREIGN KEY (`system_id`) REFERENCES `systems` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_workflow_steps` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `system_id` INT UNSIGNED NOT NULL,
  `label` VARCHAR(120) NOT NULL,
  `detail` VARCHAR(500) NOT NULL DEFAULT '',
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ix_steps_system` (`system_id`, `sort_order`),
  CONSTRAINT `fk_steps_system` FOREIGN KEY (`system_id`) REFERENCES `systems` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `system_id` INT UNSIGNED NOT NULL,
  `role_name` VARCHAR(120) NOT NULL,
  `description` VARCHAR(500) NOT NULL DEFAULT '',
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ix_roles_system` (`system_id`, `sort_order`),
  CONSTRAINT `fk_roles_system` FOREIGN KEY (`system_id`) REFERENCES `systems` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inquiries` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(120) NOT NULL,
  `organization` VARCHAR(150) NULL,
  `email` VARCHAR(191) NOT NULL,
  `phone` VARCHAR(40) NULL,
  `system_id` INT UNSIGNED NULL,
  `system_name` VARCHAR(150) NULL,
  `inquiry_type` VARCHAR(40) NOT NULL,
  `message` TEXT NOT NULL,
  `consent` TINYINT(1) NOT NULL DEFAULT 0,
  `status` VARCHAR(20) NOT NULL DEFAULT 'new',
  `admin_notes` TEXT NULL,
  `email_sent` TINYINT(1) NOT NULL DEFAULT 0,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_inquiries_status` (`status`, `created_at`),
  KEY `ix_inquiries_throttle` (`ip_address`, `created_at`),
  KEY `ix_inquiries_system` (`system_id`),
  CONSTRAINT `fk_inquiries_system` FOREIGN KEY (`system_id`) REFERENCES `systems` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admin_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(60) NOT NULL,
  `display_name` VARCHAR(120) NOT NULL DEFAULT '',
  `password_hash` VARCHAR(255) NOT NULL,
  `failed_attempts` INT NOT NULL DEFAULT 0,
  `locked_until` DATETIME NULL,
  `last_login_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admin_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SQL;

// ------------------------------------------------------------------ content
$sectorRows = [];
$sectorIds  = [];
foreach ($source['sectors'] as $i => $sector) {
    $id = $i + 1;
    $sectorIds[$sector['slug']] = $id;
    $sectorRows[] = [$id, $sector['slug'], $sector['name'], $sector['summary'], $id * 10];
}

// Systems shown in the short grid on the home page.
$featured = [
    'accounting-finance', 'human-resource-payroll', 'water-billing-collection',
    'retail-pos-inventory', 'hotel-property-management', 'construction-management',
];

$systemRows = $moduleRows = $stepRows = $roleRows = [];
$systemIds  = [];
$slugs      = [];
foreach ($source['systems'] as $i => $system) {
    $id = $i + 1;
    if (!isset($sectorIds[$system['sector']])) {
        fwrite(STDERR, "Unknown sector for {$system['slug']}\n");
        exit(1);
    }
    if (isset($slugs[$system['slug']]) || !preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $system['slug'])) {
        fwrite(STDERR, "Bad or duplicate slug: {$system['slug']}\n");
        exit(1);
    }
    $slugs[$system['slug']] = true;
    $systemIds[$system['slug']] = $id;

    $systemRows[] = [
        $id, $sectorIds[$system['sector']], $system['slug'], $system['name'], $system['category'],
        $system['tagline'], $system['summary'], $system['audience'], $system['manages'],
        $system['terms'], implode("\n", $system['notes']),
        null, null, null, null,
        1, in_array($system['slug'], $featured, true) ? 1 : 0, $id * 10, $now, $now,
    ];
    foreach ($system['modules'] as $n => $m) {
        $moduleRows[] = [$id, $m[0], $m[1], ($n + 1) * 10];
    }
    foreach ($system['steps'] as $n => $s) {
        $stepRows[] = [$id, $s[0], $s[1], ($n + 1) * 10];
    }
    foreach ($system['roles'] as $n => $r) {
        $roleRows[] = [$id, $r[0], $r[1], ($n + 1) * 10];
    }
}

$content  = "-- Sectors (catalog filter groups)\n";
$content .= insert_rows('sectors', ['id', 'slug', 'name', 'summary', 'sort_order'], $sectorRows);
$content .= "-- Systems. STARTER CONTENT: confirm with GSMaRT, set is_published = 0 for anything not offered.\n";
$content .= insert_rows('systems', [
    'id', 'sector_id', 'slug', 'name', 'category', 'tagline', 'summary', 'audience', 'manages',
    'search_terms', 'implementation_notes', 'platforms', 'deployment', 'integrations', 'pricing',
    'is_published', 'is_featured', 'sort_order', 'created_at', 'updated_at',
], $systemRows);
$content .= insert_rows('system_modules', ['system_id', 'name', 'description', 'sort_order'], $moduleRows);
$content .= insert_rows('system_workflow_steps', ['system_id', 'label', 'detail', 'sort_order'], $stepRows);
$content .= insert_rows('system_roles', ['system_id', 'role_name', 'description', 'sort_order'], $roleRows);

$footer = "SET FOREIGN_KEY_CHECKS = 1;\n";

// --------------------------------------------------------------------- demo
$demoPassword = 'Gsmart#Demo2026';
$demo  = "-- Demo admin account. Username: admin   Password: {$demoPassword}\n";
$demo .= "-- Change this password (Admin > Account) or delete the row before going live.\n";
$demo .= insert_rows(
    'admin_users',
    ['username', 'display_name', 'password_hash', 'created_at'],
    [['admin', 'Demo Administrator', password_hash($demoPassword, PASSWORD_BCRYPT), $now]]
);

$sampleInquiries = [
    ['Sample Inquirer One', 'Sample Water Cooperative', 'inquirer.one@example.com', '0000 000 0001',
     'water-billing-collection', 'demo',
     "SAMPLE DATA. We read about 1,200 meters a month and bill from a spreadsheet. We would like to see how readings are entered and how arrears are carried to the next bill.",
     'new', '2026-01-12 09:14:00'],
    ['Sample Inquirer Two', null, 'inquirer.two@example.com', null,
     'laundry-management', 'question',
     "SAMPLE DATA. Can the claim stub be printed on a small thermal printer? We have two branches.",
     'new', '2026-01-11 16:40:00'],
    ['Sample Inquirer Three', 'Sample Builders', 'inquirer.three@example.com', '0000 000 0003',
     'construction-management', 'demo',
     "SAMPLE DATA. We run four to six projects at a time. Interested in material requests and progress against budget.",
     'in_progress', '2026-01-09 10:05:00'],
    ['Sample Inquirer Four', 'Sample Academy', 'inquirer.four@example.com', null,
     'school-management', 'custom',
     "SAMPLE DATA. We need an installment plan per student on top of the standard fee schedule. Is that something you can build?",
     'in_progress', '2026-01-07 13:22:00'],
    ['Sample Inquirer Five', null, 'inquirer.five@example.com', '0000 000 0005',
     null, 'other',
     "SAMPLE DATA. Do you provide training for staff after installation?",
     'closed', '2026-01-05 08:51:00'],
];
$nameBySlug = [];
foreach ($source['systems'] as $system) {
    $nameBySlug[$system['slug']] = $system['name'];
}
$inquiryRows = [];
foreach ($sampleInquiries as $r) {
    $inquiryRows[] = [
        $r[0], $r[1], $r[2], $r[3],
        $r[4] ? $systemIds[$r[4]] : null, $r[4] ? $nameBySlug[$r[4]] : null,
        $r[5], $r[6], 1, $r[7], '203.0.113.10', 'Sample data', $r[8], $r[8],
    ];
}
$demo .= "-- Sample inquiries (clearly marked; safe to delete).\n";
$demo .= insert_rows('inquiries', [
    'full_name', 'organization', 'email', 'phone', 'system_id', 'system_name', 'inquiry_type',
    'message', 'consent', 'status', 'ip_address', 'user_agent', 'created_at', 'updated_at',
], $inquiryRows);

// -------------------------------------------------------------------- write
$clean = sprintf($header, 'CLEAN: structure and catalog content only (no inquiries, no admin user).') . $content . $footer;
$full  = sprintf($header, 'DEMO: structure, catalog content, sample inquiries and a demo admin account.') . $content . $demo . $footer;

file_put_contents($root . '/database/gsmart_website_clean.sql', $clean);
file_put_contents($root . '/database/gsmart_website_demo.sql', $full);

echo "Wrote database/gsmart_website_clean.sql (" . strlen($clean) . " bytes)\n";
echo "Wrote database/gsmart_website_demo.sql (" . strlen($full) . " bytes)\n";
echo count($systemRows) . " systems, " . count($moduleRows) . " modules, "
    . count($stepRows) . " workflow steps, " . count($roleRows) . " roles.\n";
