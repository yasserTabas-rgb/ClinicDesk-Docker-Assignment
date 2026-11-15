<?php
Auth::requireRole('doctor');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Add Prescription</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>?page=appointments">Appointments</a>
                        </li>
                        <li class="breadcrumb-item active">Add Prescription</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <!-- Appointment Summary -->
            <div class="alert alert-info">
                <strong>Patient:</strong> <?= e($appt['patient_name']) ?> &nbsp;|&nbsp;
                <strong>Date:</strong> <?= formatDate($appt['appt_date']) ?> &nbsp;|&nbsp;
                <strong>Time:</strong> <?= formatTime($appt['appt_time']) ?>
            </div>

            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Prescription Details</h3>
                </div>

                <form method="POST"
                      action="<?= BASE_URL ?>?page=prescriptions&action=store"
                      enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token"      value="<?= CSRF::generateToken() ?>">
                    <input type="hidden" name="appointment_id"  value="<?= $appt['id'] ?>">

                    <div class="card-body">

                        <div class="form-group">
                            <label>Diagnosis *</label>
                            <textarea name="diagnosis" class="form-control"
                                      rows="3" required
                                      placeholder="Enter diagnosis..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Medications *</label>
                            <textarea name="medications" class="form-control"
                                      rows="4" required
                                      placeholder="List medications, dosage, frequency..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Additional Notes</label>
                            <textarea name="notes" class="form-control"
                                      rows="2"
                                      placeholder="Any additional instructions..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>
                                Attach PDF
                                <small class="text-muted">(optional, max 3MB)</small>
                            </label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input"
                                           name="prescription_file" accept=".pdf">
                                    <label class="custom-file-label">Choose PDF file</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Prescription
                        </button>
                        <a href="<?= BASE_URL ?>?page=appointments&action=detail&id=<?= $appt['id'] ?>"
                           class="btn btn-secondary ml-2">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>