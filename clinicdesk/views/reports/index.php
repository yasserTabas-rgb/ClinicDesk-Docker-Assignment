<?php
Auth::requireRole('admin');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';

$statusColors = [
    'pending'   => 'warning',
    'confirmed' => 'info',
    'completed' => 'success',
    'cancelled' => 'danger',
];
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Appointment Reports</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>?page=dashboard">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <!-- Filter Form -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter mr-1"></i> Report Filters
                    </h3>
                </div>

                <form method="GET" action="">
                    <input type="hidden" name="page" value="reports">

                    <div class="card-body">

                        <!-- Errors -->
                        <?php foreach ($errors as $err): ?>
                            <div class="alert alert-danger"><?= e($err) ?></div>
                        <?php endforeach; ?>

                        <div class="row">
                            <!-- Start Date -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date *</label>
                                    <input type="date" name="start_date"
                                           class="form-control"
                                           value="<?= e($startDate) ?>" required>
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>End Date *</label>
                                    <input type="date" name="end_date"
                                           class="form-control"
                                           value="<?= e($endDate) ?>" required>
                                </div>
                            </div>

                            <!-- Doctor -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Doctor</label>
                                    <select name="doctor_id" class="form-control">
                                        <option value="0">All Doctors</option>
                                        <?php foreach ($doctors as $d): ?>
                                            <option value="<?= $d['id'] ?>"
                                                <?= $doctorId === (int)$d['id'] ? 'selected' : '' ?>>
                                                <?= e($d['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <?php foreach (['pending','confirmed','completed','cancelled'] as $s): ?>
                                            <option value="<?= $s ?>"
                                                <?= $status === $s ? 'selected' : '' ?>>
                                                <?= ucfirst($s) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Generate Report
                        </button>
                        <a href="<?= BASE_URL ?>?page=reports" class="btn btn-secondary ml-2">
                            <i class="fas fa-redo"></i> Reset
                        </a>

                        <!-- CSV Export -->
                        <?php if (!empty($appointments)): ?>
                        <a href="<?= BASE_URL ?>?page=reports&start_date=<?= e($startDate) ?>&end_date=<?= e($endDate) ?>&doctor_id=<?= $doctorId ?>&status=<?= e($status) ?>&export=csv"
                           class="btn btn-success ml-2">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Results -->
            <?php if ($startDate && $endDate && empty($errors)): ?>

                <!-- Summary Cards -->
                <?php if (!empty($summary)): ?>
                <div class="row mb-3">
                    <div class="col-md-2">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary">
                                <i class="fas fa-list"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total</span>
                                <span class="info-box-number"><?= count($appointments) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php foreach ($statusColors as $st => $color): ?>
                        <?php if (isset($summary[$st])): ?>
                        <div class="col-md-2">
                            <div class="info-box">
                                <span class="info-box-icon bg-<?= $color ?>">
                                    <i class="fas fa-calendar-check"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?= ucfirst($st) ?></span>
                                    <span class="info-box-number"><?= $summary[$st] ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Results Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Results: <?= e($startDate) ?> → <?= e($endDate) ?>
                            <span class="badge badge-primary ml-2">
                                <?= count($appointments) ?> records
                            </span>
                        </h3>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-bordered text-nowrap">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Specialization</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($appointments)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">No records found for selected filters.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($appointments as $a): ?>
                                <tr>
                                    <td><?= e($a['id']) ?></td>
                                    <td><?= e($a['patient_name']) ?></td>
                                    <td>Dr. <?= e($a['doctor_name']) ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= e($a['specialization_name']) ?>
                                        </span>
                                    </td>
                                    <td><?= formatDate($a['appt_date']) ?></td>
                                    <td><?= formatTime($a['appt_time']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $statusColors[$a['status']] ?? 'secondary' ?>">
                                            <?= ucfirst($a['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= e($a['reason'] ?? '—') ?></td>
                                </tr>
                                <?php endforeach; ?>

                                <!-- Summary Row -->
                                <tr class="table-secondary font-weight-bold">
                                    <td colspan="6" class="text-right">
                                        Total: <?= count($appointments) ?> records
                                    </td>
                                    <td colspan="2">
                                        <?php foreach ($summary as $st => $count): ?>
                                            <span class="badge badge-<?= $statusColors[$st] ?? 'secondary' ?> mr-1">
                                                <?= ucfirst($st) ?>: <?= $count ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>