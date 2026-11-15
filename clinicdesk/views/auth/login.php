<?php
if (!defined('BASE_URL')) exit;
$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">

<div class="login-box">

    <!-- Logo -->
    <div class="login-logo">
        <a href="#"><b><?= APP_NAME ?></b></a>
        <p class="text-muted" style="font-size:0.9rem;">Clinic Management Dashboard</p>
    </div>

    <div class="card">
        <div class="card-body login-card-body">

            <!-- Flash alerts -->
            <?php if (!empty($_SESSION['flash'])): ?>
                <?php
                $flash = $_SESSION['flash'];
                unset($_SESSION['flash']);
                ?>
                <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <p class="login-box-msg">Sign in to start your session</p>

            <form method="POST" action="<?= BASE_URL ?>?page=auth&action=login">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                <!-- Email -->
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control"
                           placeholder="Email" required autofocus
                           value="<?= isset($oldEmail) ? e($oldEmail) : '' ?>">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control"
                           placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt mr-1"></i> Sign In
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>public/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>