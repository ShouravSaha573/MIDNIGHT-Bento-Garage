<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
if($_SERVER['REQUEST_METHOD']!=='POST')redirect(appUrl('admin/index.php'));
verifyCsrfOrFail();
try{$pdo=connectDatabase();$admin=requireAdmin($pdo);}catch(Throwable $error){error_log($error->getMessage());exit('Database connection failed.');}
$id=filter_var($_POST['appointment_id']??null,FILTER_VALIDATE_INT)?:0;
if($id<1){setFlash('error','Invalid appointment.');redirect(appUrl('admin/index.php'));}
try{$pdo->beginTransaction();$appointment=$pdo->prepare('SELECT id,status FROM appointments WHERE id=:id FOR UPDATE');$appointment->execute(['id'=>$id]);$row=$appointment->fetch();if(!$row){$pdo->rollBack();setFlash('error','Appointment not found.');redirect(appUrl('admin/index.php'));}if($row['status']==='completed'){$pdo->rollBack();setFlash('warning','This appointment is already completed.');redirect(appUrl('admin/index.php'));}$update=$pdo->prepare("UPDATE appointments SET status='completed',completed_at=NOW() WHERE id=:id");$update->execute(['id'=>$id]);logAudit($pdo,(int)$admin['id'],'appointment_completed','appointment',$id);$pdo->commit();setFlash('success','Appointment marked as completed and moved to customer history.');redirect(appUrl('admin/index.php'));}catch(Throwable $error){if($pdo->inTransaction())$pdo->rollBack();error_log($error->getMessage());setFlash('error','The appointment could not be completed.');redirect(appUrl('admin/index.php'));}
