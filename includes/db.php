<?php
/**
 * Database access (PDO, MySQL 5.5+). All queries use prepared statements.
 */

/** @return PDO|null  null when the connection cannot be made */
function db()
{
    static $pdo = null, $tried = false;
    if ($tried) {
        return $pdo;
    }
    $tried = true;
    $charset = preg_replace('/[^a-z0-9_]/i', '', (string) config('db.charset', 'utf8mb4')) ?: 'utf8mb4';
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        config('db.host', '127.0.0.1'),
        (int) config('db.port', 3306),
        config('db.name', 'gsmart_website'),
        $charset
    );
    try {
        $pdo = new PDO($dsn, config('db.user', 'root'), (string) config('db.pass', ''), [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $pdo->exec('SET NAMES ' . $charset);
    } catch (Exception $ex) {
        $pdo = null;
        $GLOBALS['gsmart_db_error'] = $ex->getMessage();
        error_log('GSMaRT website: database connection failed: ' . $ex->getMessage());
    }
    return $pdo;
}

function db_error()
{
    return isset($GLOBALS['gsmart_db_error']) ? $GLOBALS['gsmart_db_error'] : null;
}

/** Run a query and return all rows. */
function db_all($sql, array $params = [])
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/** Run a query and return the first row, or null. */
function db_one($sql, array $params = [])
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

/** Run an INSERT, UPDATE or DELETE and return the affected row count. */
function db_run($sql, array $params = [])
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/**
 * Stop with a plain "setup needed" page when the database is unreachable.
 * Public pages call this before reading the catalog.
 */
function require_database()
{
    if (db() !== null) {
        try {
            db()->query('SELECT 1 FROM systems LIMIT 1');
            return;
        } catch (Exception $ex) {
            $GLOBALS['gsmart_db_error'] = $ex->getMessage();
        }
    }
    if (!headers_sent()) {
        header('HTTP/1.1 503 Service Unavailable');
        header('Retry-After: 300');
    }
    $detail = config('debug') ? db_error() : null;
    require GSMART_ROOT . '/includes/setup-needed.php';
    exit;
}
