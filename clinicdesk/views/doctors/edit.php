<?php
Auth::requireRole('admin','doctor');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';

$activeDays = explode(',', $doctor['available_days']);
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Edit Doctor: <?= e($doctor['name']) ?></h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Doctor Profile</h3>
                </div>

                <form method="POST" action="<?= BASE_URL ?>?page=doctors&action=update"
                      enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <input type="hidden" name="doctor_id" value="<?= $doctor['id'] ?>">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Specialization</label>
                                    <select name="specialization_id" class="form-control">
                                        <?php foreach ($specs as $s): ?>
                                            <option value="<?= $s['id'] ?>"
                                                <?= $s['id'] == $doctor['specialization_id'] ? 'selected' : '' ?>>
                                                <?= e($s['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Consultation Fee ($)</label>
                                    <input type="number" name="consultation_fee"
                                           class="form-control" step="0.01"
                                           value="<?= e($doctor['consultation_fee']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Bio</label>
                            <textarea name="bio" class="form-control"
                                      rows="3"><?= e($doctor['bio'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Available Days</label><br>
                            <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day): ?>
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input"
                                           id="day_<?= $day ?>" name="available_days[]"
                                           value="<?= $day ?>"
                                           <?= in_array($day, $activeDays) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="day_<?= $day ?>"><?= $day ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="form-group">
                            <label>Profile Photo <small class="text-muted">(JPEG/PNG, max 1MB)</small></label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input"
                                           name="photo" accept="image/jpeg,image/png">
                                    <label class="custom-file-label">Choose file</label>
                                </div>
                            </div>
                            <?php if (!empty($doctor['avatar'])): ?>
                                <small class="text-muted mt-1 d-block">
                                    Current: <?= e($doctor['avatar']) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="<?= BASE_URL ?>?page=doctors" class="btn btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>