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

require_once __DIR__ . '/../../models/PrescriptionModel.php';
$prescModel   = new PrescriptionModel();
$prescription = $prescModel->findByAppointmentId((int)$appt['id']);
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Appointment Detail</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?page=dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?page=appointments">Appointments</a></li>
                        <li class="breadcrumb-item active">#<?= e($appt['id']) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <div class="row">
                <!-- Appointment Info -->
                <div class="col-md-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Appointment Info</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Patient</th>
                                    <td><?= e($appt['patient_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Doctor</th>
                                    <td>Dr. <?= e($appt['doctor_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Specialization</th>
                                    <td><?= e($appt['specialization_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td><?= formatDate($appt['appt_date']) ?></td>
                                </tr>
                                <tr>
                                    <th>Time</th>
                                    <td><?= formatTime($appt['appt_time']) ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-<?= $statusColors[$appt['status']] ?>">
                                            <?= ucfirst($appt['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Reason</th>
                                    <td><?= e($appt['reason'] ?? '—') ?></td>
                                </tr>
                                <tr>
                                    <th>Fee</th>
                                    <td>$<?= number_format($appt['consultation_fee'], 2) ?></td>
                                </tr>
                                <?php if (!empty($appt['doctor_notes'])): ?>
                                <tr>
                                    <th>Doctor Notes</th>
                                    <td><?= e($appt['doctor_notes']) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Actions + Prescription -->
                <div class="col-md-6">

                    <!-- Doctor/Admin: Update Status -->
                    <?php if (in_array($role, ['doctor','admin']) && $appt['status'] !== 'cancelled'): ?>
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Update Status</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= BASE_URL ?>?page=appointments&action=updateStatus">
                                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                <input type="hidden" name="appt_id" value="<?= $appt['id'] ?>">

                                <div class="form-group">
                                    <label>New Status</label>
                                    <select name="status" class="form-control">
                                        <?php if ($appt['status'] === 'pending'): ?>
                                            <option value="confirmed">Confirm</option>
                                            <option value="cancelled">Cancel</option>
                                        <?php elseif ($appt['status'] === 'confirmed'): ?>
                                            <option value="completed">Complete</option>
                                            <option value="cancelled">Cancel</option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Doctor Notes (optional)</label>
                                    <textarea name="doctor_notes" class="form-control"
                                              rows="3"><?= e($appt['doctor_notes'] ?? '') ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-check"></i> Update
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Prescription section -->
                    <?php if ($appt['status'] === 'completed'): ?>
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Prescription</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($prescription): ?>
                                <p><strong>Diagnosis:</strong> <?= e($prescription['diagnosis']) ?></p>
                                <p><strong>Medications:</strong> <?= e($prescription['medications']) ?></p>
                                <?php if (!empty($prescription['notes'])): ?>
                                    <p><strong>Notes:</strong> <?= e($prescription['notes']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($prescription['file_path'])): ?>
                                    <a href="<?= BASE_URL ?>?page=prescriptions&action=download&id=<?= $appt['id'] ?>"
                                       class="btn btn-success btn-sm">
                                        <i class="fas fa-download"></i> Download PDF
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($role === 'doctor'): ?>
                                    <a href="<?= BASE_URL ?>?page=prescriptions&action=add&appt_id=<?= $appt['id'] ?>"
                                       class="btn btn-primary btn-block">
                                        <i class="fas fa-plus"></i> Add Prescription
                                    </a>
                                <?php else: ?>
                                    <p class="text-muted">No prescription added yet.</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <a href="<?= BASE_URL ?>?page=appointments" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>