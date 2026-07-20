<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

try {
    $pdo = connectDatabase();
    $existing = currentUser($pdo);
    if ($existing) {
        redirect((int)$existing['admin_flag'] === 1 ? appUrl('admin/index.php') : appUrl('customer/dashboard.php'));
    }
} catch (Throwable $error) {
    error_log($error->getMessage());
    $pdo = null;
}

$errors = [];
$old = ['full_name' => '', 'username' => '', 'phone' => ''];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail();
    $old = [
        'full_name' => trim((string)($_POST['full_name'] ?? '')),
        'username' => trim((string)($_POST['username'] ?? '')),
        'phone' => normalizePhone((string)($_POST['phone'] ?? '')),
    ];
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if ($old['full_name'] === '' || mb_strlen($old['full_name']) < 2 || mb_strlen($old['full_name']) > 100) {
        $errors['full_name'] = 'Enter your full name.';
    }
    if (!validUsername($old['username'])) {
        $errors['username'] = 'Use 4–30 letters, numbers or underscores.';
    }
    if (!preg_match('/^\d{7,15}$/', $old['phone'])) {
        $errors['phone'] = 'Phone number must contain 7–15 digits.';
    }
    if ($passwordError = validatePassword($password)) {
        $errors['password'] = $passwordError;
    }
    if ($password !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (!$pdo) {
        $message = 'Database is not connected. Complete the setup first.';
    } elseif ($errors === []) {
        try {
            $stmt = $pdo->prepare('INSERT INTO users
                (public_id, full_name, username, phone, password_hash, admin_flag, active, must_change_password)
                VALUES (:public_id, :full_name, :username, :phone, :password_hash, 0, 1, 0)');
            $stmt->execute([
                'public_id' => generatePublicUserId(),
                'full_name' => $old['full_name'],
                'username' => $old['username'],
                'phone' => $old['phone'],
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $newId = (int)$pdo->lastInsertId();
            logAudit($pdo, $newId, 'customer_registered', 'user', $newId);
            setFlash('success', 'Customer account created. Please sign in.');
            redirect(appUrl('customer/login.php'));
        } catch (PDOException $error) {
            error_log($error->getMessage());
            $message = $error->getCode() === '23000'
                ? 'That username or phone number is already registered.'
                : 'The account could not be created. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration | Midnight Bento Garage</title>
    <link rel="icon" type="image/svg+xml" href="<?= e(appUrl('assets/images/logo.svg')) ?>">
    <link rel="stylesheet" href="<?= e(appUrl('assets/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e(appUrl('assets/css/auth.css')) ?>">
</head>
<body class="auth-body">
<main class="auth-shell">
    <section class="auth-visual glass-card">
        <a class="brand" href="<?= e(appUrl('customer/login.php')) ?>">
            <img src="<?= e(appUrl('assets/images/logo.svg')) ?>" alt="" width="54" height="54">
            <span><strong>MIDNIGHT</strong><small>BENTO GARAGE</small></span>
        </a>
        <div>
            <p class="eyebrow">CUSTOMER ACCOUNT</p>
            <h1>Create your secure garage ID.</h1>
            <p>Your random Customer ID connects your account and appointment without exposing database IDs.</p>
        </div>
        <img class="auth-car" src="<?= e(appUrl('assets/images/hero-car.webp')) ?>" alt="White sports car in a bright workshop">
    </section>

    <section class="auth-card strong-card">
        <div class="auth-heading">
            <p class="section-kicker">New customer</p>
            <h2>Create Account</h2>
            <p>Customer registration creates a standard customer account.</p>
        </div>

        <?php if ($message !== ''): ?><div class="auth-message error"><?= e($message) ?></div><?php endif; ?>

        <form method="post" novalidate>
            <?= csrfField() ?>
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" id="fullName" name="full_name" type="text" maxlength="100" value="<?= e($old['full_name']) ?>" autocomplete="name" required>
                <small class="field-error"><?= e($errors['full_name'] ?? '') ?></small>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" id="username" name="username" type="text" maxlength="30" value="<?= e($old['username']) ?>" autocomplete="username" required>
                <small class="field-error"><?= e($errors['username'] ?? '') ?></small>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" id="phone" name="phone" type="tel" maxlength="20" value="<?= e($old['phone']) ?>" autocomplete="tel" required>
                <small class="field-error"><?= e($errors['phone'] ?? '') ?></small>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" type="password" minlength="5" maxlength="12" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@_])[A-Za-z0-9@_]{5,12}" title="5–12 characters with an uppercase letter, lowercase letter, number, and @ or _." autocomplete="new-password" required>
                <small class="password-rule">Use 5–12 characters: uppercase, lowercase, number, and @ or _ only.</small>
                <small class="field-error"><?= e($errors['password'] ?? '') ?></small>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" id="confirmPassword" name="confirm_password" type="password" minlength="5" maxlength="12" autocomplete="new-password" required>
                <small class="field-error"><?= e($errors['confirm_password'] ?? '') ?></small>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Create Customer Account</button>
        </form>
        <p class="auth-switch">Already registered? <a href="<?= e(appUrl('customer/login.php')) ?>">Customer Login</a></p>
    </section>
</main>
</body>
</html>
