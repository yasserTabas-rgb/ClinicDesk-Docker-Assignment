<?php
Auth::requireRole('admin','doctor','patient');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';

$role = Auth::role();

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
                    <h1 class="m-0">
                        <?= $role === 'doctor' ? 'My Schedule' : ($role === 'patient' ? 'My Appointments' : 'All Appointments') ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?page=dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Appointments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <!-- Book button for patients -->
            <?php if ($role === 'patient'): ?>
            <div class="mb-3">
                <a href="<?= BASE_URL ?>?page=appointments&action=book"
                   class="btn btn-primary">
                    <i class="fas fa-plus"></i> Book New Appointment
                </a>
            </div>
            <?php endif; ?>

            <!-- Filter Bar -->
            <div class="card card-outline card-secondary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filters</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="">
                        <input type="hidden" name="page" value="appointments">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="">All</option>
                                        <?php foreach (['pending','confirmed','completed','cancelled'] as $s): ?>
                                            <option value="<?= $s ?>"
                                                <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>>
                                                <?= ucfirst($s) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm"
                                           value="<?= e($_GET['start_date'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm"
                                           value="<?= e($_GET['end_date'] ?? '') ?>">
                                </div>
                            </div>
                            <?php if ($role === 'admin'): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Doctor</label>
                                    <select name="doctor_id" class="form-control form-control-sm">
                                        <option value="">All Doctors</option>
                                        <?php foreach ($doctors as $d): ?>
                                            <option value="<?= $d['id'] ?>"
                                                <?= ($_GET['doctor_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                                                <?= e($d['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i> Apply
                        </button>
                        <a href="<?= BASE_URL ?>?page=appointments" class="btn btn-sm btn-secondary ml-1">
                            Clear
                        </a>
                    </form>
                </div>
            </div>

            <!-- Appointments Table -->
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <?php if ($role !== 'patient'): ?>
                                    <th>Patient</th>
                                <?php endif; ?>
                                <?php if ($role !== 'doctor'): ?>
                                    <th>Doctor</th>
                                <?php endif; ?>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No appointments found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($appointments as $a): ?>
                            <tr>
                                <td><?= e($a['id']) ?></td>
                                <?php if ($role !== 'patient'): ?>
                                    <td><?= e($a['patient_name']) ?></td>
                                <?php endif; ?>
                                <?php if ($role !== 'doctor'): ?>
                                    <td>
                                        <?= e($a['doctor_name']) ?>
                                        <br>
                                        <small class="text-muted"><?= e($a['specialization_name']) ?></small>
                                    </td>
                                <?php endif; ?>
                                <td><?= formatDate($a['appt_date']) ?></td>
                                <td><?= formatTime($a['appt_time']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $statusColors[$a['status']] ?? 'secondary' ?>">
                                        <?= ucfirst($a['status']) ?>
                                    </span>
                                </td>
                                <td><?= e($a['reason'] ?? '—') ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>?page=appointments&action=detail&id=<?= $a['id'] ?>"
                                       class="btn btn-xs btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Patient: cancel pending -->
                                    <?php if ($role === 'patient' && $a['status'] === 'pending'): ?>
                                    <form method="POST"
                                          action="<?= BASE_URL ?>?page=appointments&action=cancel"
                                          class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                        <input type="hidden" name="appt_id" value="<?= $a['id'] ?>">
                                        <button type="submit" class="btn btn-xs btn-danger"
                                                onclick="return confirm('Cancel this appointment?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($pager->totalPages() > 1): ?>
                <div class="card-footer">
                    <ul class="pagination pagination-sm m-0 float-right">
                        <?php if ($pager->hasPrev()): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=appointments&p=<?= $pager->getCurrentPage()-1 ?>">«</a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $pager->totalPages(); $i++): ?>
                            <li class="page-item <?= $i === $pager->getCurrentPage() ? 'active' : '' ?>">
                                <a class="page-link" href="?page=appointments&p=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($pager->hasNext()): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=appointments&p=<?= $pager->getCurrentPage()+1 ?>">»</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>