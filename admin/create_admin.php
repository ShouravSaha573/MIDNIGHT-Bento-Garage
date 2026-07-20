<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
try {
    $pdo = connectDatabase();
    $admin = requireAdmin($pdo);
} catch (Throwable $error) {
    error_log($error->getMessage());
    exit('Database connection failed.');
}
$errors=[];$message='';$old=['full_name'=>'','username'=>'','phone'=>''];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verifyCsrfOrFail();
    $old=['full_name'=>trim((string)($_POST['full_name']??'')),'username'=>trim((string)($_POST['username']??'')),'phone'=>normalizePhone((string)($_POST['phone']??''))];
    $currentPassword=(string)($_POST['current_admin_password']??'');
    $password=(string)($_POST['password']??'');$confirm=(string)($_POST['confirm_password']??'');
    if (!password_verify($currentPassword,$admin['password_hash'])) $errors['current_admin_password']='Your current admin password is incorrect.';
    if ($old['full_name']===''||mb_strlen($old['full_name'])<2) $errors['full_name']='Enter the administrator name.';
    if (!validUsername($old['username'])) $errors['username']='Use 4–30 letters, numbers or underscores.';
    if ($old['phone']!==''&&!preg_match('/^\d{7,15}$/',$old['phone'])) $errors['phone']='Enter 7–15 digits or leave it empty.';
    if ($err=validatePassword($password)) $errors['password']=$err;
    if ($password!==$confirm) $errors['confirm_password']='Passwords do not match.';
    if ($errors===[]) {
        try {
            $stmt=$pdo->prepare('INSERT INTO users (public_id,full_name,username,phone,password_hash,admin_flag,active,must_change_password,created_by)
                                 VALUES (:public_id,:full_name,:username,:phone,:password_hash,1,1,1,:created_by)');
            $stmt->execute(['public_id'=>generatePublicUserId(),'full_name'=>$old['full_name'],'username'=>$old['username'],'phone'=>$old['phone']===''?null:$old['phone'],'password_hash'=>password_hash($password,PASSWORD_DEFAULT),'created_by'=>$admin['id']]);
            $newId=(int)$pdo->lastInsertId();
            logAudit($pdo,(int)$admin['id'],'admin_created','user',$newId,['username'=>$old['username']]);
            setFlash('success','New administrator created. The new admin must change the temporary password at first login.');
            redirect(appUrl('admin/create_admin.php'));
        } catch (PDOException $error) {
            error_log($error->getMessage());
            $message=$error->getCode()==='23000'?'That username or phone is already in use.':'The administrator could not be created.';
        }
    }
}
$flash=pullFlash();
$list=$pdo->query('SELECT public_id,full_name,username,phone,last_login_at,created_at FROM users WHERE admin_flag=1 ORDER BY full_name,public_id')->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Create Admin | Midnight Bento Garage</title><link rel="stylesheet" href="<?= e(appUrl('assets/css/style.css')) ?>"><link rel="stylesheet" href="<?= e(appUrl('assets/css/admin.css?v=20260720-6')) ?>"><link rel="stylesheet" href="<?= e(appUrl('assets/css/auth.css')) ?>"></head>
<body class="admin-body"><button class="admin-menu-toggle" id="adminMenuToggle" type="button" aria-label="Open admin menu" aria-expanded="false">☰</button><aside class="admin-sidebar" id="adminSidebar"><a class="brand admin-brand" href="<?= e(appUrl('admin/index.php')) ?>"><img src="<?= e(appUrl('assets/images/logo.svg')) ?>" alt="" width="52" height="52"><span><strong>MIDNIGHT</strong><small>BENTO GARAGE</small></span></a><nav class="admin-nav"><a href="<?= e(appUrl('admin/index.php')) ?>"><span>⌂</span>Dashboard</a><a class="active" href="<?= e(appUrl('admin/create_admin.php')) ?>"><span>+</span>Create Admin</a><a href="<?= e(appUrl('account/change_password.php')) ?>"><span>⚿</span>Password</a></nav><form class="sidebar-logout" action="<?= e(appUrl('logout.php')) ?>" method="post"><?= csrfField() ?><button class="btn btn-secondary btn-block" type="submit">Logout</button></form></aside>
<main class="admin-main"><header class="admin-topbar"><div><p class="section-kicker">Restricted operation</p><h1>Create Another Admin</h1><p>Only an authenticated administrator can create another administrator account.</p></div></header>
<?php if ($flash['message']!==''): ?><div class="admin-alert admin-alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?><?php if ($message!==''): ?><div class="admin-alert admin-alert-error"><?= e($message) ?></div><?php endif; ?>
<section class="admin-create-grid"><article class="strong-card admin-create-card"><h2>New Administrator</h2><p class="security-note">Re-enter your own password to authorize this sensitive action.</p><form method="post"><?= csrfField() ?><div class="form-group"><label for="fullName">Full Name</label><input class="form-control <?= isset($errors['full_name'])?'is-invalid':'' ?>" id="fullName" name="full_name" value="<?= e($old['full_name']) ?>" required><small class="field-error"><?= e($errors['full_name']??'') ?></small></div><div class="form-group"><label for="username">Username</label><input class="form-control <?= isset($errors['username'])?'is-invalid':'' ?>" id="username" name="username" value="<?= e($old['username']) ?>" required><small class="field-error"><?= e($errors['username']??'') ?></small></div><div class="form-group"><label for="phone">Phone (optional)</label><input class="form-control <?= isset($errors['phone'])?'is-invalid':'' ?>" id="phone" name="phone" value="<?= e($old['phone']) ?>"><small class="field-error"><?= e($errors['phone']??'') ?></small></div><div class="form-group"><label for="password">Temporary Password</label><input class="form-control <?= isset($errors['password'])?'is-invalid':'' ?>" id="password" name="password" type="password" minlength="5" maxlength="12" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@_])[A-Za-z0-9@_]{5,12}" title="5–12 characters with an uppercase letter, lowercase letter, number, and @ or _." required><small class="password-rule">Use 5–12 characters: uppercase, lowercase, number, and @ or _ only.</small><small class="field-error"><?= e($errors['password']??'') ?></small></div><div class="form-group"><label for="confirmPassword">Confirm Password</label><input class="form-control <?= isset($errors['confirm_password'])?'is-invalid':'' ?>" id="confirmPassword" name="confirm_password" type="password" minlength="5" maxlength="12" required><small class="field-error"><?= e($errors['confirm_password']??'') ?></small></div><div class="form-group"><label for="currentPassword">Your Current Admin Password</label><input class="form-control <?= isset($errors['current_admin_password'])?'is-invalid':'' ?>" id="currentPassword" name="current_admin_password" type="password" required><small class="field-error"><?= e($errors['current_admin_password']??'') ?></small></div><button class="btn btn-primary btn-block" type="submit">Create Admin Account</button></form></article>
<article class="strong-card admin-list-card"><h2>Current Administrators</h2><div class="admin-account-list"><?php foreach($list as $item): ?><div><span class="mini-avatar"><?= e(initials($item['full_name'])) ?></span><p><strong><?= e($item['full_name']) ?></strong><small><?= e($item['username']) ?> · <?= e($item['public_id']) ?></small></p><span class="role-badge admin">Admin</span></div><?php endforeach; ?></div></article></section></main><script src="<?= e(appUrl('assets/js/admin.js')) ?>"></script></body></html>
