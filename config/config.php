<?php
// ============================================================
//  DATABASE SETTINGS  -  the ONLY place you need to edit.
//  XAMPP / WAMP defaults are already filled in.
//  For live hosting (InfinityFree etc.) copy config.local.example.php
//  to config.local.php and put your hosting details there.
// ============================================================
$local = __DIR__ . '/config.local.php';
if (file_exists($local)) {
    require $local;
}
defined('DB_HOST') || define('DB_HOST', 'localhost');
defined('DB_PORT') || define('DB_PORT', 3306);
defined('DB_NAME') || define('DB_NAME', 'studycircle');
defined('DB_USER') || define('DB_USER', 'root');
defined('DB_PASS') || define('DB_PASS', '');

define('APP_NAME', 'NSBM StudyCircle');
define('MAX_UPLOAD_MB', 10);
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');

// Works in any sub-folder (htdocs/studycircle) or at the site root.
$docRoot = rtrim(str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
$project = str_replace('\\', '/', (string) realpath(dirname(__DIR__)));
define('BASE_URL', ($docRoot !== '' && strpos($project, $docRoot) === 0) ? substr($project, strlen($docRoot)) : '');
