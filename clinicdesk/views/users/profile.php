<?php
Auth::requireRole('admin','doctor','patient');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">My Profile</h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <?php if (!empty($user['avatar'])): ?>
                            <img class="profile-user-img img-fluid img-circle"
                                 src="<?= BASE_URL ?>public/uploads/avatars/<?= e($user['avatar']) ?>"
                                 alt="Avatar">
                        <?php else: ?>
                            <i class="fas fa-user-circle fa-5x text-secondary"></i>
                        <?php endif; ?>
                    </div>
                    <h3 class="profile-username text-center mt-2"><?= e($user['name']) ?></h3>
                    <p class="text-muted text-center">
                        <span class="badge badge-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'doctor' ? 'info' : 'success') ?>">
                            <?= e($user['role']) ?>
                        </span>
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Email</b> <span class="float-right"><?= e($user['email']) ?></span>
                        </li>
                        <li class="list-group-item">
                            <b>Phone</b> <span class="float-right"><?= e($user['phone'] ?? '—') ?></span>
                        </li>
                        <li class="list-group-item">
                            <b>Member Since</b>
                            <span class="float-right"><?= formatDate($user['created_at']) ?></span>
                        </li>
                    </ul>

                    <a href="<?= BASE_URL ?>?page=users&action=edit&id=<?= $user['id'] ?>"
                       class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>