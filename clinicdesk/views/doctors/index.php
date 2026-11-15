<?php
Auth::requireRole('admin');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Doctors</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?page=dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Doctors</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <div class="card-group mb-3">
                <a href="<?= BASE_URL ?>?page=doctors&action=specializations"
                   class="btn btn-outline-info btn-sm">
                    <i class="fas fa-list"></i> Manage Specializations
                </a>
                <a href="<?= BASE_URL ?>?page=users&action=create"
                   class="btn btn-primary btn-sm ml-2">
                    <i class="fas fa-plus"></i> Add Doctor
                </a>
            </div>

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Specialization</th>
                                <th>Fee ($)</th>
                                <th>Available Days</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($doctors)): ?>
                            <tr><td colspan="7" class="text-center">No doctors found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($doctors as $d): ?>
                            <tr>
                                <td><?= e($d['id']) ?></td>
                                <td><?= e($d['name']) ?></td>
                                <td><span class="badge badge-info"><?= e($d['specialization_name']) ?></span></td>
                                <td><?= number_format($d['consultation_fee'], 2) ?></td>
                                <td><?= e($d['available_days']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $d['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $d['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>?page=doctors&action=edit&id=<?= $d['id'] ?>"
                                       class="btn btn-xs btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($pager->totalPages() > 1): ?>
                <div class="card-footer">
                    <ul class="pagination pagination-sm m-0 float-right">
                        <?php if ($pager->hasPrev()): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=doctors&p=<?= $pager->getCurrentPage()-1 ?>">«</a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $pager->totalPages(); $i++): ?>
                            <li class="page-item <?= $i === $pager->getCurrentPage() ? 'active' : '' ?>">
                                <a class="page-link" href="?page=doctors&p=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($pager->hasNext()): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=doctors&p=<?= $pager->getCurrentPage()+1 ?>">»</a>
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