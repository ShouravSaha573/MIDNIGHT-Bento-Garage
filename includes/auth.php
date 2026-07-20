<?php
declare(strict_types=1);

const DUMMY_PASSWORD_HASH = '$2y$12$imu9sX4UrN/vLfJsDh1fQ.UG3FelDDoOsi6mKjzVoYXQPE45EzAE2';

function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

function currentUser(PDO $pdo): ?array
{
    $id = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
    if ($id < 1) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT id, public_id, full_name, username, phone, password_hash, admin_flag, active, must_change_password
                           FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch();
    if (!$user || !(int)$user['active']) {
        logoutUser();
        return null;
    }
    $_SESSION['admin_flag'] = (int)$user['admin_flag'];
    $_SESSION['must_change_password'] = (int)$user['must_change_password'];
    return $user;
}

function requireAnyUser(PDO $pdo, bool $allowPasswordChange = false): array
{
    $user = currentUser($pdo);
    if (!$user) {
        redirect(appUrl('customer/login.php'));
    }
    if (!$allowPasswordChange && (int)$user['must_change_password'] === 1) {
        redirect(appUrl('account/change_password.php'));
    }
    return $user;
}

function requireCustomer(PDO $pdo): array
{
    $user = currentUser($pdo);
    if (!$user) {
        redirect(appUrl('customer/login.php'));
    }
    if ((int)$user['must_change_password'] === 1) {
        redirect(appUrl('account/change_password.php'));
    }
    if ((int)$user['admin_flag'] === 1) {
        redirect(appUrl('admin/index.php'));
    }
    return $user;
}

function requireAdmin(PDO $pdo): array
{
    $user = currentUser($pdo);
    if (!$user) {
        redirect(appUrl('admin/login.php'));
    }
    if ((int)$user['must_change_password'] === 1) {
        redirect(appUrl('account/change_password.php'));
    }
    if ((int)$user['admin_flag'] !== 1) {
        redirect(appUrl('customer/dashboard.php'));
    }
    return $user;
}

function loginUser(PDO $pdo, string $username, string $password, int $requiredAdminFlag): bool
{
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username AND admin_flag = :admin_flag LIMIT 1');
    $stmt->execute(['username' => trim($username), 'admin_flag' => $requiredAdminFlag]);
    $user = $stmt->fetch();

    $hash = $user['password_hash'] ?? DUMMY_PASSWORD_HASH;
    $passwordOk = password_verify($password, $hash);
    $roleOk = (bool)$user;
    $active = $user && (int)$user['active'] === 1;
    $locked = $user && !empty($user['locked_until']) && strtotime((string)$user['locked_until']) > time();

    if (!$user || !$passwordOk || !$roleOk || !$active || $locked) {
        if ($user && !$locked) {
            $failures = (int)$user['failed_login_count'];
            if (!empty($user['locked_until']) && strtotime((string)$user['locked_until']) <= time()) {
                $failures = 0;
            }
            $failures++;
            $lockUntil = $failures >= LOGIN_MAX_FAILURES
                ? date('Y-m-d H:i:s', time() + LOGIN_LOCK_MINUTES * 60)
                : null;
            $update = $pdo->prepare('UPDATE users SET failed_login_count = :failures, locked_until = :locked_until WHERE id = :id');
            $update->execute(['failures' => $failures, 'locked_until' => $lockUntil, 'id' => $user['id']]);
        }
        usleep(250000);
        return false;
    }

    if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
        $rehash = $pdo->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
        $rehash->execute(['hash' => password_hash($password, PASSWORD_DEFAULT), 'id' => $user['id']]);
    }

    $reset = $pdo->prepare('UPDATE users SET failed_login_count = 0, locked_until = NULL, last_login_at = NOW() WHERE id = :id');
    $reset->execute(['id' => $user['id']]);

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['admin_flag'] = (int)$user['admin_flag'];
    $_SESSION['must_change_password'] = (int)$user['must_change_password'];
    $_SESSION['created_at'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['last_rotation'] = time();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    logAudit($pdo, (int)$user['id'], 'login_success', 'user', (int)$user['id'], ['role' => $requiredAdminFlag ? 'admin' : 'customer']);
    return true;
}
