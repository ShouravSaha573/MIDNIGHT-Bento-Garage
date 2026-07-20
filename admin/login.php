<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
$flash = pullFlash();
$message = '';
$username = '';
try {
    $pdo = connectDatabase();
    $existing = currentUser($pdo);
    if ($existing) {
        redirect((int)$existing['admin_flag'] === 1 ? appUrl('admin/index.php') : appUrl('customer/dashboard.php'));
    }
} catch (Throwable $error) {
    error_log($error->getMessage());
    $pdo = null;
    $message = 'Database is not connected. Complete the SQL setup first.';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if ($pdo && loginUser($pdo, $username, $password, 1)) {
        $user = currentUser($pdo);
        redirect((int)$user['must_change_password'] === 1 ? appUrl('account/change_password.php') : appUrl('admin/index.php'));
    }
    $message = 'Invalid credentials or the account is temporarily locked.';
}
?>
<!DOCTYPE html><html lang="en" class="login-page"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | Midnight Bento Garage</title>
<link rel="icon" type="image/svg+xml" href="<?= e(appUrl('assets/images/logo.svg')) ?>">
<link rel="stylesheet" href="<?= e(appUrl('assets/css/style.css')) ?>"><link rel="stylesheet" href="<?= e(appUrl('assets/css/auth.css?v=20260720-4')) ?>"></head>
<body class="auth-body login-auth-body admin-auth-body"><main class="auth-shell">
<section class="auth-visual admin-auth-visual"><a class="brand" href="<?= e(appUrl('admin/login.php')) ?>"><img src="<?= e(appUrl('assets/images/logo.svg')) ?>" alt="" width="54" height="54"><span><strong>MIDNIGHT</strong><small>BENTO GARAGE</small></span></a><div><p class="eyebrow">RESTRICTED ADMIN AREA</p><h1>Secure workshop management.</h1></div><img class="auth-car" src="<?= e(appUrl('assets/images/hero-car.webp')) ?>" alt="White performance car"></section>
<section class="auth-card strong-card"><div class="auth-heading"><p class="section-kicker">Authorized staff only</p><h2>Admin Login</h2></div>
<?php if ($flash['message'] !== ''): ?><div class="auth-message <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<?php if ($message !== ''): ?><div class="auth-message error"><?= e($message) ?></div><?php endif; ?>
<form method="post"><?= csrfField() ?><div class="form-group"><label for="username">Admin Username</label><input class="form-control" id="username" name="username" type="text" value="<?= e($username) ?>" autocomplete="username" required></div><div class="form-group"><label for="password">Password</label><input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required></div><button class="btn btn-primary btn-block" type="submit">Sign In as Admin</button></form>
<p class="auth-switch"><a href="<?= e(appUrl('customer/login.php')) ?>">Back to Customer Login</a></p></section>
</main></body></html>
