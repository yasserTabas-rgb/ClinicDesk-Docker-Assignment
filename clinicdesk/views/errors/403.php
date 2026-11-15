<?php http_response_code(403); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 - Access Denied</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 80px; }
        h1   { color: #e74c3c; }
        a    { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <h1>403 — Access Denied</h1>
    <p>You do not have permission to access this page.</p>
    <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>">← Go Home</a>
</body>
</html>