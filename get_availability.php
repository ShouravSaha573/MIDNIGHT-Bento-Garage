<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
try {
    $pdo=connectDatabase();
    requireCustomer($pdo);
    $date=trim((string)($_GET['date']??''));
    if (!validDate($date)||$date<date('Y-m-d',strtotime('+1 day'))) { http_response_code(422); echo json_encode(['success'=>false,'message'=>'Appointments must be requested at least one day in advance.']); exit; }
    $mechanics=getMechanicsWithAvailability($pdo,$date);
    echo json_encode(['success'=>true,'date'=>$date,'mechanics'=>$mechanics,'summary'=>getWorkshopSummary($mechanics)]);
} catch(Throwable $error) { error_log($error->getMessage()); http_response_code(500); echo json_encode(['success'=>false,'message'=>'Availability could not be loaded.']); }
