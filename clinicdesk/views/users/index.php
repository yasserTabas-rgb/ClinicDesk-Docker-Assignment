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
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?page=dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <!-- Filter + Add Button -->
            <div class="card">
                <div class="card-header">
                    <form method="GET" action="" class="form-inline">
                        <input type="hidden" name="page" value="users">
                        <select name="role" class="form-control form-control-sm mr-2">
                            <option value="">All Roles</option>
                            <option value="admin"   <?= ($_GET['role'] ?? '') === 'admin'   ? 'selected' : '' ?>>Admin</option>
                            <option value="doctor"  <?= ($_GET['role'] ?? '') === 'doctor'  ? 'selected' : '' ?>>Doctor</option>
                            <option value="patient" <?= ($_GET['role'] ?? '') === 'patient' ? 'selected' : '' ?>>Patient</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-secondary mr-2">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </form>
                    <div class="card-tools">
                        <a href="<?= BASE_URL ?>?page=users&action=create" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add User
                        </a>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($users)): ?>
                            <tr><td colspan="8" class="text-center">No users found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= e($u['id']) ?></td>
                                <td><?= e($u['name']) ?></td>
                                <td><?= e($u['email']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'doctor' ? 'info' : 'success') ?>">
                                        <?= e($u['role']) ?>
                                    </span>
                                </td>
                                <td><?= e($u['phone'] ?? '—') ?></td>
                                <td>
                                    <span class="badge badge-<?= $u['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td><?= formatDate($u['created_at']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>?page=users&action=edit&id=<?= $u['id'] ?>"
                                       class="btn btn-xs btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- Toggle Active -->
                                    <?php if ((int)$u['id'] !== (int)Auth::currentUser()['id']): ?>
                                    <form method="POST" action="<?= BASE_URL ?>?page=users&action=toggle" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <button type="submit"
                                                class="btn btn-xs btn-<?= $u['is_active'] ? 'secondary' : 'success' ?>"
                                                onclick="return confirm('Change user status?')">
                                            <i class="fas fa-<?= $u['is_active'] ? 'ban' : 'check' ?>"></i>
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
                                <a class="page-link" href="?page=users&role=<?= e($_GET['role'] ?? '') ?>&p=<?= $pager->getCurrentPage() - 1 ?>">«</a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $pager->totalPages(); $i++): ?>
                            <li class="page-item <?= $i === $pager->getCurrentPage() ? 'active' : '' ?>">
                                <a class="page-link" href="?page=users&role=<?= e($_GET['role'] ?? '') ?>&p=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($pager->hasNext()): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=users&role=<?= e($_GET['role'] ?? '') ?>&p=<?= $pager->getCurrentPage() + 1 ?>">»</a>
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