<?php
Auth::requireRole('patient');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">My Prescriptions</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Specialization</th>
                                <th>Date</th>
                                <th>Diagnosis</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($prescriptions)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-prescription-bottle fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No prescriptions yet.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($prescriptions as $p): ?>
                            <tr>
                                <td>Dr. <?= e($p['doctor_name']) ?></td>
                                <td>
                                    <span class="badge badge-info">
                                        <?= e($p['specialization_name']) ?>
                                    </span>
                                </td>
                                <td><?= formatDate($p['appt_date']) ?></td>
                                <td>
                                    <?= e(strlen($p['diagnosis']) > 60
                                        ? substr($p['diagnosis'], 0, 60) . '...'
                                        : $p['diagnosis']) ?>
                                </td>
                                <td>
                                    <?php if (!empty($p['file_path'])): ?>
                                        <a href="<?= BASE_URL ?>?page=prescriptions&action=download&id=<?= $p['appointment_id'] ?>"
                                           class="btn btn-xs btn-success">
                                            <i class="fas fa-download"></i> PDF
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
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