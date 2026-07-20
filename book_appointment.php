<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD']!=='POST') redirect(appUrl('index.php'));
verifyCsrfOrFail();
try { $pdo=connectDatabase(); $user=requireCustomer($pdo); } catch(Throwable $error) { error_log($error->getMessage()); exit('Database connection failed.'); }
$clean=['address'=>trim((string)($_POST['address']??'')),'car_license'=>strtoupper(trim((string)($_POST['car_license']??''))),'car_engine'=>strtoupper(trim((string)($_POST['car_engine']??''))),'appointment_date'=>trim((string)($_POST['appointment_date']??'')),'mechanic_id'=>filter_var($_POST['mechanic_id']??null,FILTER_VALIDATE_INT)?:0];
$errors=[];
if ($clean['address']===''||mb_strlen($clean['address'])<5||mb_strlen($clean['address'])>255) $errors['address']='Enter a complete address.';
if (!preg_match('/^[A-Z0-9 -]{2,50}$/',$clean['car_license'])) $errors['car_license']='Enter a valid car registration number.';
if (!preg_match('/^[A-Z0-9-]{2,50}$/',$clean['car_engine'])) $errors['car_engine']='Engine number may contain letters, numbers and hyphens.';
$minimumAppointmentDate=date('Y-m-d',strtotime('+1 day'));
if (!validDate($clean['appointment_date'])||$clean['appointment_date']<$minimumAppointmentDate) $errors['appointment_date']='Appointments must be requested at least one day in advance.';
if ($clean['mechanic_id']<1) $errors['mechanic_id']='Select an available mechanic.';
if ($errors!==[]) { setFlash('error','Please correct the highlighted fields.',$errors,$clean); redirect(appUrl('index.php#booking')); }
try {
    $pdo->beginTransaction();
    $today=date('Y-m-d');$now=date('H:i:s');
    $existing=$pdo->prepare("SELECT id FROM appointments WHERE user_id=:user_id AND status='scheduled' AND (appointment_date>:future_date OR (appointment_date=:current_date AND (appointment_time IS NULL OR appointment_time>:current_time))) FOR UPDATE");$existing->execute(['user_id'=>$user['id'],'future_date'=>$today,'current_date'=>$today,'current_time'=>$now]);
    if ($existing->fetch()) { $pdo->rollBack(); setFlash('warning','You already have an upcoming appointment.'); redirect(appUrl('customer/dashboard.php')); }
    $mechanic=$pdo->prepare('SELECT id,name FROM mechanics WHERE id=:id AND is_active=1 FOR UPDATE');$mechanic->execute(['id'=>$clean['mechanic_id']]);$mechanicRow=$mechanic->fetch();
    if (!$mechanicRow) { $pdo->rollBack(); setFlash('error','The selected mechanic is not available.'); redirect(appUrl('index.php#booking')); }
    $count=$pdo->prepare("SELECT COUNT(*) FROM appointments WHERE mechanic_id=:mechanic_id AND appointment_date=:appointment_date AND status='scheduled'");$count->execute(['mechanic_id'=>$clean['mechanic_id'],'appointment_date'=>$clean['appointment_date']]);
    if ((int)$count->fetchColumn()>=DAILY_MECHANIC_LIMIT) { $pdo->rollBack(); setFlash('error','This mechanic is fully booked. Choose another mechanic or date.'); redirect(appUrl('index.php#booking')); }
    $insert=$pdo->prepare('INSERT INTO appointments (user_id,client_name,address,phone,car_license,car_engine,appointment_date,mechanic_id) VALUES (:user_id,:client_name,:address,:phone,:car_license,:car_engine,:appointment_date,:mechanic_id)');
    $insert->execute(['user_id'=>$user['id'],'client_name'=>$user['full_name'],'address'=>$clean['address'],'phone'=>$user['phone'],'car_license'=>$clean['car_license'],'car_engine'=>$clean['car_engine'],'appointment_date'=>$clean['appointment_date'],'mechanic_id'=>$clean['mechanic_id']]);
    $appointmentId=(int)$pdo->lastInsertId();logAudit($pdo,(int)$user['id'],'appointment_created','appointment',$appointmentId,['mechanic_id'=>$clean['mechanic_id'],'date'=>$clean['appointment_date']]);$pdo->commit();
    setFlash('success','Appointment booked successfully with '.$mechanicRow['name'].'.');redirect(appUrl('customer/dashboard.php'));
} catch(PDOException $error) { if($pdo->inTransaction())$pdo->rollBack();error_log($error->getMessage());setFlash('error','Something went wrong. Please try again.');redirect(appUrl('index.php#booking')); }
