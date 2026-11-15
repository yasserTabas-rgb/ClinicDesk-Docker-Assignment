<?php
Auth::requireRole('patient');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';
$statusColors = ['pending'=>'warning','confirmed'=>'info','completed'=>'success','cancelled'=>'danger'];
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">My Dashboard</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <!-- Next Appointment Card -->
            <?php if ($nextAppt): ?>
            <div class="alert alert-info alert-dismissible">
                <h5><i class="fas fa-calendar-check mr-1"></i> Next Appointment</h5>
                <strong>Dr. <?= e($nextAppt['doctor_name']) ?></strong>
                (<?= e($nextAppt['specialization_name']) ?>) —
                <?= formatDate($nextAppt['appt_date']) ?> at <?= formatTime($nextAppt['appt_time']) ?>
                <span class="badge badge-<?= $statusColors[$nextAppt['status']] ?> ml-2">
                    <?= ucfirst($nextAppt['status']) ?>
                </span>
            </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $activeCount ?></h3>
                            <p>Active Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        <a href="<?= BASE_URL ?>?page=appointments" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $completedCount ?></h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                        <a href="<?= BASE_URL ?>?page=appointments&status=completed" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $prescCount ?></h3>
                            <p>Prescriptions</p>
                        </div>
                        <div class="icon"><i class="fas fa-prescription-bottle-alt"></i></div>
                        <a href="<?= BASE_URL ?>?page=prescriptions" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Active Appointments List -->
            <?php if (!empty($activeAppts)): ?>
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">Active Appointments</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th><th></th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($activeAppts as $a): ?>
                        <tr>
                            <td>
                                Dr. <?= e($a['doctor_name']) ?>
                                <br><small class="text-muted"><?= e($a['specialization_name']) ?></small>
                            </td>
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
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>?page=appointments&action=book" class="btn btn-primary">
                <i class="fas fa-plus"></i> Book New Appointment
            </a>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>