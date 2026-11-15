<?php
Auth::requireRole('doctor');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';
$statusColors = ['pending'=>'warning','confirmed'=>'info','completed'=>'success','cancelled'=>'danger'];
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Doctor Dashboard</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <!-- Stats -->
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $monthStats['total'] ?? 0 ?></h3>
                            <p>This Month</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        <a href="<?= BASE_URL ?>?page=appointments" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $monthStats['pending'] ?? 0 ?></h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                        <a href="<?= BASE_URL ?>?page=appointments&status=pending" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $monthStats['completed'] ?? 0 ?></h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                        <a href="<?= BASE_URL ?>?page=appointments&status=completed" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Today's Appointments -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-day mr-1"></i>
                        Today's Appointments
                        <span class="badge badge-primary ml-1"><?= count($todayAppts) ?></span>
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>Patient</th><th>Time</th><th>Status</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                        <?php if (empty($todayAppts)): ?>
                            <tr><td colspan="4" class="text-center text-muted">No appointments today.</td></tr>
                        <?php else: ?>
                            <?php foreach ($todayAppts as $a): ?>
                            <tr>
                                <td><?= e($a['patient_name']) ?></td>
                                <td><?= formatTime($a['appt_time']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $statusColors[$a['status']] ?>">
                                        <?= ucfirst($a['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>?page=appointments&action=detail&id=<?= $a['id'] ?>"
                                       class="btn btn-xs btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Upcoming -->
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-1"></i> Upcoming Appointments
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>Patient</th><th>Date</th><th>Time</th><th>Status</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                        <?php if (empty($upcomingAppts)): ?>
                            <tr><td colspan="5" class="text-center text-muted">No upcoming appointments.</td></tr>
                        <?php else: ?>
                            <?php foreach ($upcomingAppts as $a): ?>
                            <tr>
                                <td><?= e($a['patient_name']) ?></td>
                                <td><?= formatDate($a['appt_date']) ?></td>
                                <td><?= formatTime($a['appt_time']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $statusColors[$a['status']] ?>">
                                        <?= ucfirst($a['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>?page=appointments&action=detail&id=<?= $a['id'] ?>"
                                       class="btn btn-xs btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>