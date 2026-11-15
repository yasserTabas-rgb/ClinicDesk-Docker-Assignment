<?php
Auth::requireRole('admin');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Specializations</h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <div class="row">
                <!-- Add Form -->
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header"><h3 class="card-title">Add New</h3></div>
                        <form method="POST" action="<?= BASE_URL ?>?page=doctors&action=addSpecialization">
                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control"
                                           placeholder="e.g. Cardiology" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- List -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">All Specializations</h3></div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr><th>#</th><th>Name</th><th>Action</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($specs as $s): ?>
                                <tr>
                                    <td><?= e($s['id']) ?></td>
                                    <td><?= e($s['name']) ?></td>
                                    <td>
                                        <form method="POST"
                                              action="<?= BASE_URL ?>?page=doctors&action=deleteSpecialization"
                                              class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                            <input type="hidden" name="spec_id" value="<?= $s['id'] ?>">
                                            <button type="submit" class="btn btn-xs btn-danger"
                                                    onclick="return confirm('Delete this specialization?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>