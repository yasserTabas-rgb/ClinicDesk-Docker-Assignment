<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireRole('patient');
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">My Prescriptions</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Prescriptions</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
      <div class="card">
        <div class="card-header"><h3 class="card-title">Prescription History</h3></div>
        <div class="card-body p-0">
          <table class="table table-striped datatable">
            <thead>
              <tr><th>Date</th><th>Doctor</th><th>Specialization</th><th>Diagnosis</th><th>Download</th></tr>
            </thead>
            <tbody>
              <?php foreach ($prescriptions as $p): ?>
              <tr>
                <td><?= formatDate($p['appt_date']) ?></td>
                <td>Dr. <?= e($p['doctor_name']) ?></td>
                <td><?= e($p['specialization_name']) ?></td>
                <td><?= e(mb_strimwidth($p['diagnosis'], 0, 60, '…')) ?></td>
                <td>
                  <?php if ($p['file_path']): ?>
                  <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=download&id=<?= (int)$p['id'] ?>"
                     class="btn btn-xs btn-success"><i class="fas fa-download"></i> PDF</a>
                  <?php else: ?>
                  <span class="text-muted">—</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($prescriptions)): ?>
              <tr><td colspan="5" class="text-center p-4 text-muted">No prescriptions yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
