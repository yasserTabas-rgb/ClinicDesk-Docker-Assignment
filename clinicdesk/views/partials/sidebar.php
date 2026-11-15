<?php
$user    = Auth::currentUser();
$role    = Auth::role();
$current = $_GET['page'] ?? 'dashboard';
?>
<!-- Main Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <!-- Brand Logo -->
    <a href="<?= BASE_URL ?>?page=dashboard" class="brand-link">
        <i class="fas fa-clinic-medical brand-image ml-2 mr-2" style="font-size:1.5rem;color:#fff;"></i>
        <span class="brand-text font-weight-light"><?= APP_NAME ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= BASE_URL ?>public/uploads/avatars/<?= e($user['avatar']) ?>"
                         class="img-circle elevation-2" alt="Avatar">
                <?php else: ?>
                    <i class="fas fa-user-circle fa-2x" style="color:#c2c7d0;margin-left:4px;"></i>
                <?php endif; ?>
            </div>
            <div class="info">
                <a href="<?= BASE_URL ?>?page=users&action=profile" class="d-block">
                    <?= e($user['name']) ?>
                </a>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview"
                role="menu" data-accordion="false">

                <!-- Dashboard — all roles -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>?page=dashboard"
                       class="nav-link <?= $current === 'dashboard' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if ($role === 'admin'): ?>
                <!-- ── ADMIN LINKS ── -->
                <li class="nav-header">MANAGEMENT</li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>?page=users"
                       class="nav-link <?= $current === 'users' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>?page=doctors"
                       class="nav-link <?= $current === 'doctors' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-md"></i>
                        <p>Doctors</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>?page=appointments"
                       class="nav-link <?= $current === 'appointments' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>Appointments</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>?page=reports"
                       class="nav-link <?= $current === 'reports' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reports</p>
                    </a>
                </li>

                <?php elseif ($role === 'doctor'): ?>
                <!-- ── DOCTOR LINKS ── -->
                <li class="nav-header">MY WORK</li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>?page=appointments"
                       class="nav-link <?= $current === 'appointments' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>My Schedule</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>?page=prescriptions"
                       class="nav-link <?= $current === 'prescriptions' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-file-medical"></i>
                        <p>Prescriptions</p>
                    </a>
                </li>

                <?php elseif ($role === 'patient'): ?>
                <!-- ── PATIENT LINKS ── -->
                <li class="nav-header">MY HEALTH</li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>?page=appointments&action=book"
                       class="nav-link">
                        <i class="nav-icon fas fa-plus-circle"></i>
                        <p>Book Appointment</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>?page=appointments"
                       class="nav-link <?= $current === 'appointments' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>My Appointments</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>?page=prescriptions"
                       class="nav-link <?= $current === 'prescriptions' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                        <p>My Prescriptions</p>
                    </a>
                </li>

                <?php endif; ?>

            </ul>
        </nav>
    </div>
</aside>