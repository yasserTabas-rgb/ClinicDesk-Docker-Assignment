<?php
Auth::requireRole('admin');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Admin Dashboard</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <!-- Users Stats -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $userCounts['admin']   ?? 0 ?></h3>
                            <p>Admins</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-shield"></i></div>
                        <a href="<?= BASE_URL ?>?page=users&role=admin" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $userCounts['doctor']  ?? 0 ?></h3>
                            <p>Doctors</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-md"></i></div>
                        <a href="<?= BASE_URL ?>?page=doctors" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $userCounts['patient'] ?? 0 ?></h3>
                            <p>Patients</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="<?= BASE_URL ?>?page=users&role=patient" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $todayCount ?></h3>
                            <p>Appointments Today</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        <a href="<?= BASE_URL ?>?page=appointments" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- This Week Stats -->
            <div class="row">
                <?php
                $weekColors = [
                    'pending'   => 'warning',
                    'confirmed' => 'info',
                    'completed' => 'success',
                    'cancelled' => 'danger',
                ];
                foreach ($weekColors as $status => $color):
                ?>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-<?= $color ?>">
                            <i class="fas fa-calendar-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text"><?= ucfirst($status) ?> (This Week)</span>
                            <span class="info-box-number"><?= $weekStats[$status] ?? 0 ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Recent Appointments -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Appointments</h3>
                    <div class="card-tools">
                        <a href="<?= BASE_URL ?>?page=appointments" class="btn btn-sm btn-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentAppts as $a): ?>
                        <tr>
                            <td><?= e($a['patient_name']) ?></td>
                            <td><?= e($a['doctor_name']) ?></td>
                            <td><?= formatDate($a['appt_date']) ?></td>
                            <td>
                                <span class="badge badge-<?= ['pending'=>'warning','confirmed'=>'info','completed'=>'success','cancelled'=>'danger'][$a['status']] ?? 'secondary' ?>">
                                    <?= ucfirst($a['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>