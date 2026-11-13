<?php
/**
 * Illustrative interface previews.
 *
 * Screens are described as data in content/previews.php and drawn here as
 * plain HTML, so they stay sharp at any size and inside 3D transforms. They
 * are illustrations of the kind of screen a system has, with sample figures.
 * They are not screenshots, and every place they appear says so.
 */

function preview_data()
{
    static $data = null;
    if ($data === null) {
        $data = require GSMART_ROOT . '/content/previews.php';
    }
    return $data;
}

/**
 * Screens for a system. A system without an entry in content/previews.php
 * (for example one added later in the admin area) gets a screen built from
 * its own module names and workflow steps.
 */
function preview_screens($slug, $system = null)
{
    $data = preview_data();
    if (!empty($data[$slug]['screens'])) {
        return $data[$slug]['screens'];
    }
    $name    = $system ? $system['name'] : 'System';
    $modules = [];
    if ($system && !empty($system['modules'])) {
        foreach ($system['modules'] as $m) {
            $modules[] = is_array($m) ? $m['name'] : $m;
        }
    }
    $rows = [];
    foreach (array_slice($modules, 0, 6) as $i => $m) {
        $rows[] = [$m, $i === 0 ? 'info:Open' : 'mute:Available'];
    }
    $steps = [];
    if ($system && !empty($system['steps'])) {
        foreach (array_slice($system['steps'], 0, 6) as $i => $s) {
            $steps[] = [$s['label'], '', $i === 0 ? 'done' : ($i === 1 ? 'now' : 'todo')];
        }
    }
    $blocks = [['table', ['cols' => ['Module', 'Status'], 'w' => '3fr 1fr', 'rows' => $rows]]];
    if ($steps) {
        $blocks = [['cols', ['w' => '3fr 2fr', 'items' => [$blocks, [['timeline', $steps]]]]]];
    }
    return [[
        'label' => 'Overview', 'app' => $name, 'nav' => array_slice($modules, 0, 6), 'active' => 0,
        'title' => $name, 'meta' => 'Overview', 'blocks' => $blocks,
    ]];
}

/** "ok:Paid" becomes a coloured status mark followed by its text. */
function ui_value($value)
{
    $value = (string) $value;
    if (preg_match('/^(ok|warn|bad|info|mute):(.*)$/s', $value, $m)) {
        return '<span class="ui-st ui-st--' . $m[1] . '">' . e($m[2]) . '</span>';
    }
    return e($value);
}

function ui_tone($value)
{
    return preg_match('/^(ok|warn|bad|info|mute):/', (string) $value, $m) ? $m[1] : 'mute';
}

function ui_text($value)
{
    return preg_replace('/^(ok|warn|bad|info|mute):/', '', (string) $value);
}

function ui_widths($w)
{
    return preg_replace('/[^0-9a-z.\s]/', '', (string) $w);
}

function render_blocks(array $blocks)
{
    $html = '';
    foreach ($blocks as $block) {
        $html .= render_block($block[0], $block[1]);
    }
    return $html;
}

function render_block($type, $d)
{
    $h = '';
    switch ($type) {

        case 'h':
            return '<div class="ui-h">' . e($d) . '</div>';

        case 'stats':
            $h .= '<div class="ui-stats">';
            foreach ($d as $s) {
                $h .= '<div class="ui-stat"><span>' . e($s[0]) . '</span><b>' . e($s[1]) . '</b></div>';
            }
            return $h . '</div>';

        case 'table':
            $num = isset($d['num']) ? $d['num'] : [];
            $w   = isset($d['w']) ? ui_widths($d['w']) : 'repeat(' . count($d['cols']) . ', 1fr)';
            $h  .= '<div class="ui-table" style="--w:' . $w . '">';
            $h  .= '<div class="ui-tr ui-tr--head">';
            foreach ($d['cols'] as $i => $c) {
                $h .= '<span' . (in_array($i, $num, true) ? ' class="n"' : '') . '>' . e($c) . '</span>';
            }
            $h .= '</div>';
            foreach ($d['rows'] as $row) {
                $total = isset($row['total']);
                unset($row['total']);
                $h .= '<div class="ui-tr' . ($total ? ' ui-tr--total' : '') . '">';
                foreach (array_values($row) as $i => $cell) {
                    $h .= '<span' . (in_array($i, $num, true) ? ' class="n"' : '') . '>' . ui_value($cell) . '</span>';
                }
                $h .= '</div>';
            }
            return $h . '</div>';

        case 'bars':
            $max = max($d['values']) ?: 1;
            $h  .= '<div class="ui-chart"><div class="ui-h">' . e($d['label']) . '</div><div class="ui-bars">';
            foreach ($d['values'] as $i => $v) {
                $pct = round($v / $max * 100);
                $hot = isset($d['mark']) && $d['mark'] === $i ? ' class="is-mark"' : '';
                $h  .= '<span' . $hot . '><i style="height:' . $pct . '%"></i><em>' . e($d['labels'][$i]) . '</em></span>';
            }
            return $h . '</div></div>';

        case 'grid':   // time-slot or date grid: . free, b booked, p pending, x blocked
            $n  = count($d['cols']);
            $h .= '<div class="ui-grid" style="--n:' . $n . '"><div class="ui-grid__r ui-grid__r--head"><span></span>';
            foreach ($d['cols'] as $c) {
                $h .= '<em>' . e($c) . '</em>';
            }
            $h .= '</div>';
            foreach ($d['rows'] as $row) {
                $h .= '<div class="ui-grid__r"><span>' . e($row[0]) . '</span>';
                foreach (str_split(str_pad($row[1], $n, '.')) as $cell) {
                    $cls = ['.' => '', 'b' => 'is-b', 'p' => 'is-p', 'x' => 'is-x'];
                    $h  .= '<i class="' . (isset($cls[$cell]) ? $cls[$cell] : '') . '"></i>';
                }
                $h .= '</div>';
            }
            if (!empty($d['legend'])) {
                $h .= '<div class="ui-legend">';
                foreach ($d['legend'] as $code => $text) {
                    $h .= '<span><i class="is-' . e($code) . '"></i>' . e($text) . '</span>';
                }
                $h .= '</div>';
            }
            return $h . '</div>';

        case 'board':
            $h .= '<div class="ui-board" style="--n:' . count($d) . '">';
            foreach ($d as $col) {
                $h .= '<div class="ui-col"><div class="ui-col__t">' . e($col[0]) . '<b>' . count($col[1]) . '</b></div>';
                foreach ($col[1] as $card) {
                    $h .= '<div class="ui-card"><b>' . e($card[0]) . '</b><span>' . e($card[1]) . '</span>'
                        . (isset($card[2]) ? '<em>' . ui_value($card[2]) . '</em>' : '') . '</div>';
                }
                $h .= '</div>';
            }
            return $h . '</div>';

        case 'items':   // product buttons on a selling screen
            $h .= '<div class="ui-items">';
            foreach ($d as $item) {
                $h .= '<div class="ui-item"><b>' . e($item[0]) . '</b><span>' . e($item[1]) . '</span></div>';
            }
            return $h . '</div>';

        case 'ticket':
            $h .= '<div class="ui-ticket"><div class="ui-ticket__t">' . e($d['title']) . '</div>';
            foreach ($d['lines'] as $line) {
                $h .= '<div class="ui-line"><span>' . e($line[0]) . '</span><span>' . e($line[1]) . '</span></div>';
            }
            if (isset($d['total'])) {
                $h .= '<div class="ui-line ui-line--total"><span>' . e(isset($d['total_label']) ? $d['total_label'] : 'Total')
                    . '</span><span>' . e($d['total']) . '</span></div>';
            }
            if (isset($d['after'])) {
                foreach ($d['after'] as $line) {
                    $h .= '<div class="ui-line"><span>' . e($line[0]) . '</span><span>' . e($line[1]) . '</span></div>';
                }
            }
            if (isset($d['button'])) {
                $h .= '<div class="ui-btn ui-btn--wide">' . e($d['button']) . '</div>';
            }
            return $h . '</div>';

        case 'tiles':   // rooms, bays, tables, businesses
            $h .= '<div class="ui-tiles" style="--n:' . (int) $d['n'] . '">';
            foreach ($d['items'] as $t) {
                $h .= '<div class="ui-tile ui-tile--' . ui_tone($t[1]) . '"><b>' . e($t[0]) . '</b><span>' . e(ui_text($t[1])) . '</span>'
                    . (isset($t[2]) ? '<em>' . e($t[2]) . '</em>' : '') . '</div>';
            }
            return $h . '</div>';

        case 'timeline':
            $h .= '<div class="ui-timeline">';
            foreach ($d as $step) {
                $h .= '<div class="ui-tl is-' . e($step[2]) . '"><i></i><b>' . e($step[0]) . '</b><span>' . e($step[1]) . '</span></div>';
            }
            return $h . '</div>';

        case 'gantt':
            $n  = count($d['cols']);
            $h .= '<div class="ui-gantt" style="--n:' . $n . '"><div class="ui-gantt__r ui-gantt__r--head"><span></span><div>';
            foreach ($d['cols'] as $c) {
                $h .= '<em>' . e($c) . '</em>';
            }
            $h .= '</div></div>';
            foreach ($d['rows'] as $r) {
                $h .= '<div class="ui-gantt__r"><span>' . e($r[0]) . '</span><div><i style="--s:' . (int) $r[1] . ';--l:' . (int) $r[2]
                    . '"><u style="width:' . (int) $r[3] . '%"></u></i></div></div>';
            }
            return $h . '</div>';

        case 'matrix':   // size by colour stock
            $n  = count($d['cols']);
            $h .= '<div class="ui-matrix" style="--n:' . $n . '"><div class="ui-mx ui-mx--head"><span></span>';
            foreach ($d['cols'] as $c) {
                $h .= '<em>' . e($c) . '</em>';
            }
            $h .= '</div>';
            foreach ($d['rows'] as $r) {
                $h .= '<div class="ui-mx"><span><i style="background:' . e($r[1]) . '"></i>' . e($r[0]) . '</span>';
                foreach ($r[2] as $qty) {
                    $cls = $qty === 0 ? 'is-out' : ($qty <= 3 ? 'is-low' : '');
                    $h  .= '<b class="' . $cls . '">' . (int) $qty . '</b>';
                }
                $h .= '</div>';
            }
            return $h . '</div>';

        case 'kv':
            $h .= '<div class="ui-kv">';
            foreach ($d as $pair) {
                $h .= '<div><span>' . e($pair[0]) . '</span><b>' . ui_value($pair[1]) . '</b></div>';
            }
            return $h . '</div>';

        case 'list':   // stacked rows for narrow screens
            $h .= '<div class="ui-list">';
            foreach ($d as $r) {
                $h .= '<div class="ui-li"><div><b>' . e($r[0]) . '</b><span>' . e($r[1]) . '</span></div><em>' . ui_value($r[2]) . '</em></div>';
            }
            return $h . '</div>';

        case 'cols':
            $h .= '<div class="ui-cols" style="--w:' . ui_widths($d['w']) . '">';
            foreach ($d['items'] as $group) {
                $h .= '<div class="ui-stack">' . render_blocks($group) . '</div>';
            }
            return $h . '</div>';
    }
    return '';
}

/**
 * Draw one screen.
 * $variant: 'full' (side menu), 'compact' (no side menu, used in cards), 'phone'.
 */
function render_screen(array $screen, $variant = 'full')
{
    $title  = isset($screen['title']) ? $screen['title'] : '';
    $meta   = isset($screen['meta']) ? $screen['meta'] : '';
    $action = isset($screen['action']) ? $screen['action'] : '';
    $nav    = isset($screen['nav']) ? $screen['nav'] : [];
    $active = isset($screen['active']) ? (int) $screen['active'] : 0;

    $h = '<div class="ui ui--' . e($variant) . '" aria-hidden="true">';
    if ($variant === 'full') {
        $h .= '<div class="ui-side"><div class="ui-app">' . e(isset($screen['app']) ? $screen['app'] : '') . '</div>';
        foreach ($nav as $i => $item) {
            $h .= '<div class="ui-nav' . ($i === $active ? ' is-active' : '') . '">' . e($item) . '</div>';
        }
        $h .= '</div>';
    }
    $h .= '<div class="ui-main"><div class="ui-top"><div class="ui-top__t"><b>' . e($title) . '</b>'
        . ($meta !== '' ? '<span>' . e($meta) . '</span>' : '') . '</div>'
        . ($action !== '' ? '<div class="ui-btn">' . e($action) . '</div>' : '') . '</div>';
    $h .= '<div class="ui-body">' . render_blocks($screen['blocks']) . '</div>';
    if ($variant === 'phone' && $nav) {
        $h .= '<div class="ui-tabs">';
        foreach (array_slice($nav, 0, 4) as $i => $item) {
            $h .= '<span' . ($i === $active ? ' class="is-active"' : '') . '>' . e($item) . '</span>';
        }
        $h .= '</div>';
    }
    return $h . '</div></div>';
}

// ------------------------------------------------------------------ devices

function device_laptop($inner, $class = '')
{
    return '<div class="device device--laptop ' . e($class) . '">'
        . '<div class="laptop__lid"><div class="screen">' . $inner . '<div class="sheen"></div></div></div>'
        . '<div class="laptop__base"><div class="laptop__keys"></div><div class="laptop__pad"></div></div>'
        . '<div class="device__shadow"></div></div>';
}

function device_tablet($inner, $class = '')
{
    return '<div class="device device--tablet ' . e($class) . '"><div class="screen">' . $inner
        . '<div class="sheen"></div></div></div>';
}

function device_phone($inner, $class = '')
{
    return '<div class="device device--phone ' . e($class) . '"><div class="screen">' . $inner
        . '<div class="sheen"></div></div></div>';
}

function device_window($inner, $class = '')
{
    return '<div class="device device--window ' . e($class) . '"><div class="window__bar"><i></i><i></i><i></i></div>'
        . '<div class="screen">' . $inner . '</div></div>';
}
