<?php
Auth::requireRole('admin');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Create User</h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">New User Details</h3>
                </div>

                <form method="POST" action="<?= BASE_URL ?>?page=users&action=store">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Full Name *</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password *</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Role *</label>
                            <select name="role" id="roleSelect" class="form-control">
                                <option value="patient">Patient</option>
                                <option value="doctor">Doctor</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <!-- Doctor Fields — shown only when role=doctor -->
                        <div id="doctorFields" style="display:none;">
                            <hr>
                            <h5>Doctor Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Specialization *</label>
                                        <select name="specialization_id" class="form-control">
                                            <?php foreach ($specs as $s): ?>
                                                <option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Consultation Fee ($)</label>
                                        <input type="number" name="consultation_fee"
                                               class="form-control" step="0.01" value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Bio</label>
                                <textarea name="bio" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Available Days</label><br>
                                <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day): ?>
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input"
                                               id="day_<?= $day ?>" name="available_days[]"
                                               value="<?= $day ?>"
                                               <?= in_array($day, ['Sun','Mon','Tue','Wed','Thu']) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="day_<?= $day ?>"><?= $day ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create User
                        </button>
                        <a href="<?= BASE_URL ?>?page=users" class="btn btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
document.getElementById('roleSelect').addEventListener('change', function() {
    document.getElementById('doctorFields').style.display =
        this.value === 'doctor' ? 'block' : 'none';
});
</script>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>