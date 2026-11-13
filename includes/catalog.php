<?php
/**
 * Catalog queries. The public site only ever sees published systems.
 */

function catalog_sectors()
{
    static $sectors = null;
    if ($sectors === null) {
        $sectors = db_all('SELECT id, slug, name, summary FROM sectors ORDER BY sort_order, name');
    }
    return $sectors;
}

/**
 * Published systems in display order, each with its sector and module names.
 * @return array[]
 */
function catalog_systems()
{
    static $systems = null;
    if ($systems !== null) {
        return $systems;
    }
    $systems = db_all(
        'SELECT s.id, s.slug, s.name, s.category, s.tagline, s.summary, s.search_terms, s.is_featured,
                c.slug AS sector_slug, c.name AS sector_name
           FROM systems s
           JOIN sectors c ON c.id = s.sector_id
          WHERE s.is_published = 1
          ORDER BY s.sort_order, s.name'
    );
    $byId = [];
    foreach ($systems as $i => $system) {
        $systems[$i]['modules'] = [];
        $byId[$system['id']] = $i;
    }
    if ($byId) {
        $rows = db_all(
            'SELECT m.system_id, m.name
               FROM system_modules m
               JOIN systems s ON s.id = m.system_id
              WHERE s.is_published = 1
              ORDER BY m.system_id, m.sort_order, m.id'
        );
        foreach ($rows as $row) {
            $systems[$byId[$row['system_id']]]['modules'][] = $row['name'];
        }
    }
    return $systems;
}

/** One published system with everything the detail page needs, or null. */
function catalog_system($slug)
{
    if (!is_string($slug) || !preg_match('/^[a-z0-9-]{1,120}$/', $slug)) {
        return null;
    }
    $system = db_one(
        'SELECT s.*, c.slug AS sector_slug, c.name AS sector_name
           FROM systems s
           JOIN sectors c ON c.id = s.sector_id
          WHERE s.slug = ? AND s.is_published = 1',
        [$slug]
    );
    if (!$system) {
        return null;
    }
    $id = $system['id'];
    $system['modules'] = db_all('SELECT name, description FROM system_modules WHERE system_id = ? ORDER BY sort_order, id', [$id]);
    $system['steps']   = db_all('SELECT label, detail FROM system_workflow_steps WHERE system_id = ? ORDER BY sort_order, id', [$id]);
    $system['roles']   = db_all('SELECT role_name, description FROM system_roles WHERE system_id = ? ORDER BY sort_order, id', [$id]);
    $system['notes']   = array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $system['implementation_notes']))));
    return $system;
}

/** Published systems grouped under their sector, skipping empty sectors. */
function catalog_by_sector()
{
    $groups = [];
    foreach (catalog_sectors() as $sector) {
        $groups[$sector['slug']] = $sector + ['systems' => []];
    }
    foreach (catalog_systems() as $system) {
        if (isset($groups[$system['sector_slug']])) {
            $groups[$system['sector_slug']]['systems'][] = $system;
        }
    }
    return array_filter($groups, function ($g) {
        return !empty($g['systems']);
    });
}

/** Server-side match for the catalog search, mirrored in assets/js/site.js. */
function catalog_matches(array $system, $query, $sector)
{
    if ($sector !== '' && $system['sector_slug'] !== $sector) {
        return false;
    }
    $query = trim(mb_strtolower($query));
    if ($query === '') {
        return true;
    }
    $haystack = mb_strtolower(implode(' ', [
        $system['name'], $system['category'], $system['sector_name'], $system['summary'],
        $system['search_terms'], implode(' ', $system['modules']),
    ]));
    foreach (preg_split('/\s+/', $query) as $word) {
        if ($word !== '' && mb_strpos($haystack, $word) === false) {
            return false;
        }
    }
    return true;
}
