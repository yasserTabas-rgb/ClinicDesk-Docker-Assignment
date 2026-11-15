<?php
Auth::requireRole('doctor');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Prescriptions</h1>
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
                                <th>Patient</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Diagnosis</th>
                                <th>PDF</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($prescriptions)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-muted">No prescriptions found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($prescriptions as $p): ?>
                            <tr>
                                <td><?= e($p['patient_name']) ?></td>
                                <td><?= formatDate($p['appt_date']) ?></td>
                                <td><?= formatTime($p['appt_time']) ?></td>
                                <td>
                                    <?= e(strlen($p['diagnosis']) > 50
                                        ? substr($p['diagnosis'], 0, 50) . '...'
                                        : $p['diagnosis']) ?>
                                </td>
                                <td>
                                    <?php if (!empty($p['file_path'])): ?>
                                        <span class="badge badge-success">
                                            <i class="fas fa-file-pdf"></i> Attached
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">None</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= formatDate($p['created_at']) ?></td>
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