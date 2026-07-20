<?php
declare(strict_types=1);

const DAILY_MECHANIC_LIMIT = 4;

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string)$_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function verifyCsrfOrFail(): void
{
    $submitted = (string)($_POST['csrf_token'] ?? '');
    $stored = (string)($_SESSION['csrf_token'] ?? '');
    if ($submitted === '' || $stored === '' || !hash_equals($stored, $submitted)) {
        http_response_code(403);
        exit('The security token is invalid. Please return to the previous page and try again.');
    }
}

function setFlash(string $type, string $message, array $fieldErrors = [], array $old = []): void
{
    $_SESSION['flash'] = compact('type', 'message', 'fieldErrors', 'old');
}

function pullFlash(): array
{
    $flash = $_SESSION['flash'] ?? ['type' => '', 'message' => '', 'fieldErrors' => [], 'old' => []];
    unset($_SESSION['flash']);
    return $flash;
}

function normalizePhone(string $phone): string
{
    return preg_replace('/\D+/', '', trim($phone)) ?? '';
}

function validDate(string $date): bool
{
    $parsed = DateTime::createFromFormat('Y-m-d', $date);
    return $parsed !== false && $parsed->format('Y-m-d') === $date;
}

function validUsername(string $username): bool
{
    return preg_match('/^[A-Za-z0-9_]{4,30}$/', $username) === 1;
}

function validatePassword(string $password, int $minimumLength = 5): ?string
{
    if (strlen($password) < 5 || strlen($password) > 12) {
        return 'Password must be 5 to 12 characters long.';
    }
    if (!preg_match('/^[A-Za-z0-9@_]+$/', $password)) {
        return 'Only letters, numbers, @, and _ are allowed in the password.';
    }
    if (!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[@_]/', $password)) {
        return 'Password must include an uppercase letter, lowercase letter, number, and @ or _.';
    }
    return null;
}

function generatePublicUserId(): string
{
    return 'MBG-' . strtoupper(bin2hex(random_bytes(16)));
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $first = $parts[0][0] ?? '';
    $last = count($parts) > 1 ? ($parts[count($parts) - 1][0] ?? '') : '';
    return strtoupper($first . $last);
}

function getMechanicsWithAvailability(PDO $pdo, string $date, ?int $excludeAppointmentId = null): array
{
    $excludeSql = $excludeAppointmentId ? ' AND a.id <> :exclude_id ' : '';
    $sql = "SELECT m.id, m.name, m.role_title, COUNT(a.id) AS booked_count
            FROM mechanics m
            LEFT JOIN appointments a
             ON a.mechanic_id = m.id
             AND a.appointment_date = :appointment_date
             AND a.status = 'scheduled'
             {$excludeSql}
            WHERE m.is_active = 1
            GROUP BY m.id, m.name, m.role_title
            ORDER BY m.id";
    $stmt = $pdo->prepare($sql);
    $params = ['appointment_date' => $date];
    if ($excludeAppointmentId) {
        $params['exclude_id'] = $excludeAppointmentId;
    }
    $stmt->execute($params);
    $result = [];
    foreach ($stmt->fetchAll() as $row) {
        $booked = min((int)$row['booked_count'], DAILY_MECHANIC_LIMIT);
        $free = max(DAILY_MECHANIC_LIMIT - $booked, 0);
        $result[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'role_title' => $row['role_title'] ?: 'Senior Mechanic',
            'booked' => $booked,
            'free' => $free,
            'is_full' => $free === 0,
            'status' => $free === 0 ? 'full' : ($free === 1 ? 'almost-full' : 'available'),
        ];
    }
    return $result;
}

function getWorkshopSummary(array $mechanics): array
{
    $total = count($mechanics) * DAILY_MECHANIC_LIMIT;
    $booked = array_sum(array_column($mechanics, 'booked'));
    $free = array_sum(array_column($mechanics, 'free'));
    $full = count(array_filter($mechanics, fn(array $m): bool => $m['is_full']));
    return [
        'total' => $total,
        'booked' => $booked,
        'free' => $free,
        'full' => $full,
        'booked_percent' => $total > 0 ? (int)round(($booked / $total) * 100) : 0,
    ];
}

function logAudit(PDO $pdo, ?int $actorUserId, string $action, string $targetType, ?int $targetId, array $details = []): void
{
    $stmt = $pdo->prepare('INSERT INTO audit_logs (actor_user_id, action, target_type, target_id, details_json)
                           VALUES (:actor_user_id, :action, :target_type, :target_id, :details_json)');
    $stmt->execute([
        'actor_user_id' => $actorUserId,
        'action' => $action,
        'target_type' => $targetType,
        'target_id' => $targetId,
        'details_json' => json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ]);
}
