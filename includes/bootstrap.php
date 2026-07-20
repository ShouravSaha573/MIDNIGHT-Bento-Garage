<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self'; connect-src 'self'; font-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
if ($https) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');

session_name('MBGSESSID');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => rtrim(APP_BASE_URL, '/') . '/',
    'domain' => '',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$now = time();
$agentHash = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');

if (isset($_SESSION['agent_hash']) && !hash_equals((string)$_SESSION['agent_hash'], $agentHash)) {
    $loginPath = (int)($_SESSION['admin_flag'] ?? 0) === 1 ? 'admin/login.php' : 'customer/login.php';
    logoutUser();
    redirect(appUrl($loginPath . '?expired=1'));
}
$_SESSION['agent_hash'] = $agentHash;

if (isset($_SESSION['created_at']) && $now - (int)$_SESSION['created_at'] > SESSION_ABSOLUTE_TIMEOUT) {
    $loginPath = (int)($_SESSION['admin_flag'] ?? 0) === 1 ? 'admin/login.php' : 'customer/login.php';
    logoutUser();
    redirect(appUrl($loginPath . '?expired=1'));
}
if (isset($_SESSION['last_activity']) && $now - (int)$_SESSION['last_activity'] > SESSION_IDLE_TIMEOUT) {
    $loginPath = (int)($_SESSION['admin_flag'] ?? 0) === 1 ? 'admin/login.php' : 'customer/login.php';
    logoutUser();
    redirect(appUrl($loginPath . '?expired=1'));
}
if (!isset($_SESSION['created_at'])) {
    $_SESSION['created_at'] = $now;
}
if (!isset($_SESSION['last_rotation'])) {
    $_SESSION['last_rotation'] = $now;
}
if ($now - (int)$_SESSION['last_rotation'] > SESSION_ROTATION_INTERVAL) {
    session_regenerate_id(true);
    $_SESSION['last_rotation'] = $now;
}
$_SESSION['last_activity'] = $now;

csrfToken();
