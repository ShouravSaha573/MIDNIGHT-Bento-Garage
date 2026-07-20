<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
try{$pdo=connectDatabase();$admin=requireAdmin($pdo);}catch(Throwable $error){error_log($error->getMessage());exit('Database connection failed.');}

$errors=[];$message='';$old=['mechanic_name'=>'','role_title'=>''];
if($_SERVER['REQUEST_METHOD']==='POST'){
    verifyCsrfOrFail();
    $old=['mechanic_name'=>trim((string)($_POST['mechanic_name']??'')),'role_title'=>trim((string)($_POST['role_title']??''))];
    if(mb_strlen($old['mechanic_name'])<2||mb_strlen($old['mechanic_name'])>100)$errors['mechanic_name']='Enter a mechanic name containing 2 to 100 characters.';
    if(mb_strlen($old['role_title'])>100)$errors['role_title']='The role title cannot exceed 100 characters.';
    if($errors===[]){
        try{
            $duplicate=$pdo->prepare('SELECT id FROM mechanics WHERE LOWER(name)=LOWER(:name) LIMIT 1');$duplicate->execute(['name'=>$old['mechanic_name']]);
            if($duplicate->fetch()){$errors['mechanic_name']='A mechanic with that name already exists.';}
            else{
                $pdo->beginTransaction();
                $insert=$pdo->prepare('INSERT INTO mechanics (name,role_title,is_active) VALUES (:name,:role_title,1)');
                $insert->execute(['name'=>$old['mechanic_name'],'role_title'=>$old['role_title']===''?null:$old['role_title']]);
                $mechanicId=(int)$pdo->lastInsertId();
                logAudit($pdo,(int)$admin['id'],'mechanic_created','mechanic',$mechanicId,['name'=>$old['mechanic_name'],'role_title'=>$old['role_title']]);
                $pdo->commit();setFlash('success','New mechanic added successfully.');redirect(appUrl('admin/add_mechanic.php'));
            }
        }catch(Throwable $error){if($pdo->inTransaction())$pdo->rollBack();error_log($error->getMessage());$message='The mechanic could not be added. Please try again.';}
    }
}
$flash=pullFlash();
$mechanics=$pdo->query('SELECT name,role_title,is_active,created_at FROM mechanics ORDER BY name,id')->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Add Mechanic | Midnight Bento Garage</title><link rel="icon" type="image/svg+xml" href="<?= e(appUrl('assets/images/logo.svg')) ?>"><link rel="stylesheet" href="<?= e(appUrl('assets/css/style.css')) ?>"><link rel="stylesheet" href="<?= e(appUrl('assets/css/admin.css?v=20260720-6')) ?>"><link rel="stylesheet" href="<?= e(appUrl('assets/css/auth.css')) ?>"></head>
<body class="admin-body"><button class="admin-menu-toggle" id="adminMenuToggle" type="button" aria-label="Open admin menu" aria-expanded="false">☰</button><aside class="admin-sidebar" id="adminSidebar"><a class="brand admin-brand" href="<?= e(appUrl('admin/index.php')) ?>"><img src="<?= e(appUrl('assets/images/logo.svg')) ?>" alt="" width="52" height="52"><span><strong>MIDNIGHT</strong><small>BENTO GARAGE</small></span></a><nav class="admin-nav"><a href="<?= e(appUrl('admin/index.php')) ?>"><span>⌂</span>Dashboard</a><a class="active" href="<?= e(appUrl('admin/add_mechanic.php')) ?>"><span>+</span>Add Mechanic</a><a href="<?= e(appUrl('admin/create_admin.php')) ?>"><span>+</span>Create Admin</a><a href="<?= e(appUrl('account/change_password.php')) ?>"><span>⚿</span>Password</a></nav><form class="sidebar-logout" action="<?= e(appUrl('logout.php')) ?>" method="post"><?= csrfField() ?><button class="btn btn-secondary btn-block" type="submit">Logout</button></form></aside>
<main class="admin-main"><header class="admin-topbar"><div><p class="section-kicker">Workshop team</p><h1>Add New Mechanic</h1><p>Create an active mechanic who becomes available for customer bookings.</p></div><a class="btn btn-secondary" href="<?= e(appUrl('admin/index.php')) ?>">Back to Dashboard</a></header>
<?php if($flash['message']!==''): ?><div class="admin-alert admin-alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?><?php if($message!==''): ?><div class="admin-alert admin-alert-error"><?= e($message) ?></div><?php endif; ?>
<section class="admin-create-grid"><article class="strong-card admin-create-card"><h2>Mechanic Details</h2><p class="security-note">The mechanic will be active immediately after creation.</p><form method="post"><?= csrfField() ?><div class="form-group"><label for="mechanicName">Mechanic Name</label><input class="form-control <?= isset($errors['mechanic_name'])?'is-invalid':'' ?>" id="mechanicName" name="mechanic_name" maxlength="100" value="<?= e($old['mechanic_name']) ?>" required><small class="field-error"><?= e($errors['mechanic_name']??'') ?></small></div><div class="form-group"><label for="mechanicRole">Role / Speciality (optional)</label><input class="form-control <?= isset($errors['role_title'])?'is-invalid':'' ?>" id="mechanicRole" name="role_title" maxlength="100" value="<?= e($old['role_title']) ?>" placeholder="e.g. Senior Mechanic"><small class="field-error"><?= e($errors['role_title']??'') ?></small></div><button class="btn btn-primary btn-block" type="submit">Add Mechanic</button></form></article>
<article class="strong-card admin-list-card"><h2>Current Mechanics</h2><div class="admin-account-list"><?php foreach($mechanics as $mechanic): ?><div><span class="mini-avatar"><?= e(initials($mechanic['name'])) ?></span><p><strong><?= e($mechanic['name']) ?></strong><small><?= e($mechanic['role_title']??'General Mechanic') ?></small></p><span class="role-badge <?= (int)$mechanic['is_active']===1?'customer':'admin' ?>"><?= (int)$mechanic['is_active']===1?'Active':'Inactive' ?></span></div><?php endforeach; ?></div></article></section></main><script src="<?= e(appUrl('assets/js/admin.js')) ?>"></script></body></html>
