<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
try { $pdo=connectDatabase(); requireAdmin($pdo); $date=trim((string)($_GET['date']??''));$exclude=filter_var($_GET['exclude_id']??null,FILTER_VALIDATE_INT)?:null;if(!validDate($date)||$date<date('Y-m-d')){http_response_code(422);echo json_encode(['success'=>false,'message'=>'Invalid appointment date.']);exit;}$mechanics=getMechanicsWithAvailability($pdo,$date,$exclude);echo json_encode(['success'=>true,'mechanics'=>$mechanics,'summary'=>getWorkshopSummary($mechanics)]); } catch(Throwable $error){error_log($error->getMessage());http_response_code(500);echo json_encode(['success'=>false,'message'=>'Capacity could not be loaded.']);}
