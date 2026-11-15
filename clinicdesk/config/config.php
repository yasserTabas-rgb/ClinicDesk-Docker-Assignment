<?php
// Application Settings
define('APP_NAME', 'ClinicDesk');
define('BASE_URL', 'http://localhost/clinicdesk/');

// Pagination
define('ITEMS_PER_PAGE', 10);

// Upload Limits (in bytes)
define('MAX_AVATAR_SIZE',   1 * 1024 * 1024);  // 1 MB
define('MAX_PHOTO_SIZE',    1 * 1024 * 1024);  // 1 MB
define('MAX_PDF_SIZE',      3 * 1024 * 1024);  // 3 MB

// Upload Paths (relative to project root)
define('UPLOAD_AVATARS',      'public/uploads/avatars/');
define('UPLOAD_DOCTOR_PHOTOS','public/uploads/doctor_photos/');
define('UPLOAD_PRESCRIPTIONS','public/uploads/prescriptions/');

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);