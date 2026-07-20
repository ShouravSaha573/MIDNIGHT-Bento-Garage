<?php
declare(strict_types=1);

const APP_NAME = 'Midnight Bento Garage';
const APP_BASE_URL = '/Midnight_Bento_Garage_Secure';
const SESSION_IDLE_TIMEOUT = 1800;
const SESSION_ABSOLUTE_TIMEOUT = 28800;
const SESSION_ROTATION_INTERVAL = 900;
const LOGIN_MAX_FAILURES = 5;
const LOGIN_LOCK_MINUTES = 15;

function appUrl(string $path = ''): string
{
    $clean = ltrim($path, '/');
    return rtrim(APP_BASE_URL, '/') . ($clean === '' ? '' : '/' . $clean);
}
