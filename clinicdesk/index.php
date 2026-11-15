<?php
session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/CSRF.php';
require_once __DIR__ . '/core/Paginator.php';

// --- Routing ---
$page   = $_GET['page']   ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Sanitize route inputs (only allow alphanumeric + underscore)
$page   = preg_replace('/[^a-zA-Z0-9_]/', '', $page);
$action = preg_replace('/[^a-zA-Z0-9_]/', '', $action);

$controllerMap = [
    'auth'         => 'AuthController',
    'dashboard'    => 'DashboardController',
    'users'        => 'UserController',
    'doctors'      => 'DoctorController',
    'appointments' => 'AppointmentController',
    'prescriptions'=> 'PrescriptionController',
    'reports'      => 'ReportController',
];

if (!isset($controllerMap[$page])) {
    require_once __DIR__ . '/views/errors/404.php';
    exit;
}

$controllerName = $controllerMap[$page];
$controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    require_once __DIR__ . '/views/errors/404.php';
    exit;
}

require_once $controllerFile;

$controller = new $controllerName();

if (!method_exists($controller, $action)) {
    require_once __DIR__ . '/views/errors/404.php';
    exit;
}

$controller->$action();