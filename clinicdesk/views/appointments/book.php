<?php
Auth::requireRole('patient');
require_once __DIR__ . '/../../views/partials/header.php';
require_once __DIR__ . '/../../views/partials/navbar.php';
require_once __DIR__ . '/../../views/partials/sidebar.php';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Book Appointment</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?page=dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?page=appointments">Appointments</a></li>
                        <li class="breadcrumb-item active">Book</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../../views/partials/alerts.php'; ?>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">New Appointment</h3>
                </div>

                <form method="POST" action="<?= BASE_URL ?>?page=appointments&action=store">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                    <div class="card-body">
                        <!-- Doctor -->
                        <div class="form-group">
                            <label>Select Doctor *</label>
                            <select name="doctor_id" id="doctorSelect" class="form-control" required>
                                <option value="">-- Choose a Doctor --</option>
                                <?php foreach ($doctors as $d): ?>
                                    <option value="<?= $d['id'] ?>"
                                            data-days="<?= e($d['available_days']) ?>"
                                            data-fee="<?= e($d['consultation_fee']) ?>"
                                            data-spec="<?= e($d['specialization_name']) ?>">
                                        Dr. <?= e($d['name']) ?> — <?= e($d['specialization_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Doctor info box -->
                        <div id="doctorInfo" class="alert alert-info" style="display:none;">
                            <strong>Specialization:</strong> <span id="infoSpec"></span> &nbsp;|&nbsp;
                            <strong>Fee:</strong> $<span id="infoFee"></span> &nbsp;|&nbsp;
                            <strong>Available Days:</strong> <span id="infoDays"></span>
                        </div>

                        <div class="row">
                            <!-- Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Appointment Date *</label>
                                    <input type="date" name="appt_date" id="apptDate"
                                           class="form-control" required
                                           min="<?= date('Y-m-d') ?>">
                                </div>
                            </div>

                            <!-- Time Slot -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Time Slot *</label>
                                    <select name="appt_time" class="form-control" required>
                                        <option value="">-- Choose a Time --</option>
                                        <?php foreach ($timeSlots as $slot): ?>
                                            <option value="<?= $slot ?>">
                                                <?= date('h:i A', strtotime($slot)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="form-group">
                            <label>Reason for Visit</label>
                            <input type="text" name="reason" class="form-control"
                                   placeholder="Brief description of your issue"
                                   maxlength="255">
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-plus"></i> Book Appointment
                        </button>
                        <a href="<?= BASE_URL ?>?page=appointments" class="btn btn-secondary ml-2">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
document.getElementById('doctorSelect').addEventListener('change', function () {
    var opt  = this.options[this.selectedIndex];
    var info = document.getElementById('doctorInfo');
    if (this.value) {
        document.getElementById('infoSpec').textContent = opt.dataset.spec;
        document.getElementById('infoFee').textContent  = opt.dataset.fee;
        document.getElementById('infoDays').textContent = opt.dataset.days;
        info.style.display = 'block';
    } else {
        info.style.display = 'none';
    }
});
</script>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>