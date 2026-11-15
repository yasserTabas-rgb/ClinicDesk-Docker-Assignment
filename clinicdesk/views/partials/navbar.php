<?php
$user = Auth::currentUser();
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= BASE_URL ?>" class="nav-link"><?= APP_NAME ?></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- User info -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle mr-1"></i>
                <?= e($user['name']) ?>
                <span class="badge badge-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'doctor' ? 'info' : 'success') ?> ml-1">
                    <?= e($user['role']) ?>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="<?= BASE_URL ?>?page=users&action=profile" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> My Profile
                </a>
                <div class="dropdown-divider"></div>
                <!-- Logout (POST form — never GET) -->
                <form method="POST" action="<?= BASE_URL ?>?page=auth&action=logout">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>