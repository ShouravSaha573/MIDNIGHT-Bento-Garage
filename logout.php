<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}
verifyCsrfOrFail();
$adminFlag = (int)($_SESSION['admin_flag'] ?? 0);
logoutUser();
redirect(appUrl($adminFlag === 1 ? 'admin/login.php' : 'customer/login.php'));
