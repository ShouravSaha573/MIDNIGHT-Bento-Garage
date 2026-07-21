<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
try {
    $pdo = connectDatabase();
    $user = requireAnyUser($pdo, true);
} catch (Throwable $error) {
    error_log($error->getMessage());
    exit('Database connection failed.');
}
$message = '';
$errors = [];
$isAdmin = (int)$user['admin_flag'] === 1;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail();
    $current = (string)($_POST['current_password'] ?? '');
    $new = (string)($_POST['new_password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    if (!password_verify($current, $user['password_hash'])) {
        $errors['current_password'] = 'Current password is incorrect.';
    }
    if ($error = validatePassword($new)) {
        $errors['new_password'] = $error;
    }
    if ($new === $current) {
        $errors['new_password'] = 'New password must be different from the current password.';
    }
    if ($new !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }
    if ($errors === []) {
        $stmt = $pdo->prepare('UPDATE users SET password_hash = :hash, must_change_password = 0, failed_login_count = 0, locked_until = NULL WHERE id = :id');
        $stmt->execute(['hash' => password_hash($new, PASSWORD_DEFAULT), 'id' => $user['id']]);
        logAudit($pdo, (int)$user['id'], 'password_changed', 'user', (int)$user['id']);
        session_regenerate_id(true);
        $_SESSION['must_change_password'] = 0;
        setFlash('success', 'Password changed successfully.');
        redirect((int)$user['admin_flag'] === 1 ? appUrl('admin/index.php') : appUrl('customer/dashboard.php'));
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Change Password | Midnight Bento Garage</title><link rel="stylesheet" href="<?= e(appUrl('assets/css/style.css')) ?>"><?php if($isAdmin): ?><link rel="stylesheet" href="<?= e(appUrl('assets/css/admin.css?v=20260720-7')) ?>"><?php endif; ?><link rel="stylesheet" href="<?= e(appUrl('assets/css/auth.css?v=20260720-6')) ?>"></head>
<?php if($isAdmin): ?><body class="admin-body"><button class="admin-menu-toggle" id="adminMenuToggle" type="button" aria-label="Open admin menu" aria-expanded="false">☰</button><aside class="admin-sidebar" id="adminSidebar"><a class="brand admin-brand" href="<?= e(appUrl('admin/index.php')) ?>"><img src="<?= e(appUrl('assets/images/logo.svg')) ?>" alt="" width="52" height="52"><span><strong>MIDNIGHT</strong><small>BENTO GARAGE</small></span></a><nav class="admin-nav"><a href="<?= e(appUrl('admin/index.php')) ?>"><span>⌂</span>Dashboard</a><a href="<?= e(appUrl('admin/index.php#appointments')) ?>"><span>▣</span>Appointments</a><a href="<?= e(appUrl('admin/history.php')) ?>"><span>↻</span>History</a><a href="<?= e(appUrl('admin/index.php#capacity')) ?>"><span>◎</span>Mechanics</a><a href="<?= e(appUrl('admin/add_mechanic.php')) ?>"><span>+</span>Add Mechanic</a><a href="<?= e(appUrl('admin/create_admin.php')) ?>"><span>+</span>Create Admin</a><a class="active" href="<?= e(appUrl('account/change_password.php')) ?>"><span>⚿</span>Password</a></nav><div class="admin-role-card"><small>Signed in administrator</small><strong><?= e($user['full_name']) ?></strong><span><?= e($user['public_id']) ?></span><b>Administrator</b></div><form class="sidebar-logout" action="<?= e(appUrl('logout.php')) ?>" method="post"><?= csrfField() ?><button class="btn btn-secondary btn-block" type="submit">Logout</button></form></aside><main class="admin-main"><section class="auth-card strong-card password-admin-card"><?php else: ?><body><header class="site-header glass-card account-header"><a class="brand" href="<?= e(appUrl('customer/dashboard.php')) ?>"><img src="<?= e(appUrl('assets/images/logo.svg')) ?>" alt="" width="48" height="48"><span><strong>MIDNIGHT</strong><small>BENTO GARAGE</small></span></a><nav class="account-nav"><a href="<?= e(appUrl('customer/dashboard.php')) ?>">My Account</a><a href="<?= e(appUrl('index.php')) ?>">Book Appointment</a><a href="<?= e(appUrl('customer/dashboard.php#history')) ?>">History</a><a class="active" href="<?= e(appUrl('account/change_password.php')) ?>">Change Password</a></nav><form class="logout-form" action="<?= e(appUrl('logout.php')) ?>" method="post"><?= csrfField() ?><button class="btn btn-secondary" type="submit">Logout</button></form></header><main class="single-auth-shell password-customer-shell"><section class="auth-card strong-card"><?php endif; ?><a class="brand" href="#"><img src="<?= e(appUrl('assets/images/logo.svg')) ?>" alt="" width="48" height="48"><span><strong>MIDNIGHT</strong><small>BENTO GARAGE</small></span></a><div class="auth-heading"><p class="section-kicker">Account security</p><h2>Change Password</h2><p><?= (int)$user['must_change_password'] === 1 ? 'The temporary password must be replaced before continuing.' : 'Enter your current password before setting a new one.' ?></p></div>
<?php if ($message !== ''): ?><div class="auth-message error"><?= e($message) ?></div><?php endif; ?>
<form method="post"><?= csrfField() ?><div class="form-group"><label for="currentPassword">Current Password</label><input class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>" id="currentPassword" name="current_password" type="password" autocomplete="current-password" required><small class="field-error"><?= e($errors['current_password'] ?? '') ?></small></div><div class="form-group"><label for="newPassword">New Password</label><input class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>" id="newPassword" name="new_password" type="password" minlength="5" maxlength="12" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@_])[A-Za-z0-9@_]{5,12}" title="5–12 characters with an uppercase letter, lowercase letter, number, and @ or _." autocomplete="new-password" required><small class="password-rule">Use 5–12 characters: at least one uppercase letter, lowercase letter, number, and <strong>@</strong> or <strong>_</strong>. No other special characters are allowed.</small><small class="field-error"><?= e($errors['new_password'] ?? '') ?></small></div><div class="form-group"><label for="confirmPassword">Confirm New Password</label><input class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" id="confirmPassword" name="confirm_password" type="password" minlength="5" maxlength="12" autocomplete="new-password" required><small class="field-error"><?= e($errors['confirm_password'] ?? '') ?></small></div><button class="btn btn-primary btn-block" type="submit">Update Password</button></form></section><?php if($isAdmin): ?></main><script src="<?= e(appUrl('assets/js/admin.js')) ?>"></script><?php else: ?></main><?php endif; ?></body></html>
