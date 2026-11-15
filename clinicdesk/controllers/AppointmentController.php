<?php

require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

class AppointmentController
{
    private AppointmentModel $apptModel;
    private DoctorModel $doctorModel;

    // Fixed time slots
    private array $timeSlots = [
        '09:00','09:30','10:00','10:30','11:00','11:30',
        '12:00','12:30','13:00','13:30','14:00','14:30',
        '15:00','15:30','16:00'
    ];

    public function __construct()
    {
        $this->apptModel   = new AppointmentModel();
        $this->doctorModel = new DoctorModel();
    }

    /**
     * List appointments — role-aware
     */
    public function index(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');

        $role    = Auth::role();
        $user    = Auth::currentUser();
        $page    = max(1, (int)($_GET['p'] ?? 1));
        $filters = $this->getFilters();

        if ($role === 'patient') {
            $total = $this->apptModel->countFiltered('patient', (int)$user['id'], $filters);
            $appointments = $this->apptModel->getByPatient((int)$user['id'], $page, $filters);
        } elseif ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId((int)$user['id']);
            if (!$doctor) {
                flashMessage('danger', 'Doctor profile not found.');
                redirect('?page=dashboard');
            }
            $total = $this->apptModel->countFiltered('doctor', (int)$doctor['id'], $filters);
            $appointments = $this->apptModel->getByDoctor((int)$doctor['id'], $page, $filters);
        } else {
            // admin
            $total = $this->apptModel->countFiltered('admin', 0, $filters);
            $appointments = $this->apptModel->getAll($page, $filters);
        }

        $pager     = new Paginator($total, ITEMS_PER_PAGE, $page);
        $doctors   = $this->doctorModel->getAll(); // for filter dropdown
        $pageTitle = 'Appointments';

        require_once __DIR__ . '/../views/appointments/index.php';
    }

    /**
     * Show book appointment form — patient only
     */
    public function book(): void
    {
        Auth::requireRole('patient');

        $doctors   = $this->doctorModel->getAll();
        $timeSlots = $this->timeSlots;
        $pageTitle = 'Book Appointment';

        require_once __DIR__ . '/../views/appointments/book.php';
    }

    /**
     * Handle book appointment POST — patient only
     */
    public function store(): void
    {
        Auth::requireRole('patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid request.');
            redirect('?page=appointments&action=book');
        }

        $patientId = (int)Auth::currentUser()['id'];
        $doctorId  = (int)($_POST['doctor_id']  ?? 0);
        $date      = trim($_POST['appt_date']    ?? '');
        $time      = trim($_POST['appt_time']    ?? '');
        $reason    = trim($_POST['reason']       ?? '');

        // Validate required fields
        if (!$doctorId || !$date || !$time) {
            flashMessage('danger', 'All fields are required.');
            redirect('?page=appointments&action=book');
        }

        // Date must not be in the past
        if ($date < date('Y-m-d')) {
            flashMessage('danger', 'Appointment date cannot be in the past.');
            redirect('?page=appointments&action=book');
        }

        // Validate day of week against doctor's available days
        $doctor        = $this->doctorModel->findById($doctorId);
        $availableDays = explode(',', $doctor['available_days']);
        $dayOfWeek     = date('D', strtotime($date)); // e.g. Mon, Tue

        if (!in_array($dayOfWeek, $availableDays)) {
            flashMessage('danger', 'Doctor is not available on ' . $dayOfWeek . '. Available days: ' . $doctor['available_days']);
            redirect('?page=appointments&action=book');
        }

        // Check for conflict
        if ($this->apptModel->hasConflict($doctorId, $date, $time)) {
            flashMessage('danger', 'This slot is already booked. Please choose another time.');
            redirect('?page=appointments&action=book');
        }

        // Book it
        $booked = $this->apptModel->book([
            'patient_id' => $patientId,
            'doctor_id'  => $doctorId,
            'appt_date'  => $date,
            'appt_time'  => $time,
            'reason'     => $reason,
        ]);

        if ($booked) {
            flashMessage('success', 'Appointment booked successfully!');
            redirect('?page=appointments');
        } else {
            flashMessage('danger', 'Booking failed. Slot may already be taken.');
            redirect('?page=appointments&action=book');
        }
    }

    /**
     * View single appointment detail
     */
    public function detail(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');

        $id   = (int)($_GET['id'] ?? 0);
        $appt = $this->apptModel->findById($id);

        if (!$appt) {
            flashMessage('danger', 'Appointment not found.');
            redirect('?page=appointments');
        }

        // Ownership check
        $this->checkOwnership($appt);

        $pageTitle = 'Appointment Detail';
        require_once __DIR__ . '/../views/appointments/detail.php';
    }

    /**
     * Update appointment status — doctor/admin
     */
    public function updateStatus(): void
    {
        Auth::requireRole('admin', 'doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid request.');
            redirect('?page=appointments');
        }

        $id     = (int)($_POST['appt_id'] ?? 0);
        $status = $_POST['status']        ?? '';
        $notes  = trim($_POST['doctor_notes'] ?? '');

        $allowed = ['confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $allowed)) {
            flashMessage('danger', 'Invalid status.');
            redirect('?page=appointments');
        }

        $appt = $this->apptModel->findById($id);
        if (!$appt) {
            flashMessage('danger', 'Appointment not found.');
            redirect('?page=appointments');
        }

        // Doctor can only update their own appointments
        if (Auth::role() === 'doctor') {
            $doctor = $this->doctorModel->findByUserId((int)Auth::currentUser()['id']);
            if (!$doctor || (int)$appt['doctor_id'] !== (int)$doctor['id']) {
                require_once __DIR__ . '/../views/errors/403.php';
                exit;
            }
        }

        $this->apptModel->updateStatus($id, $status, $notes);
        flashMessage('success', 'Appointment status updated to ' . $status . '.');
        redirect('?page=appointments&action=detail&id=' . $id);
    }

    /**
     * Cancel appointment — patient only
     */
    public function cancel(): void
    {
        Auth::requireRole('patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid request.');
            redirect('?page=appointments');
        }

        $id   = (int)($_POST['appt_id'] ?? 0);
        $appt = $this->apptModel->findById($id);

        if (!$appt) {
            flashMessage('danger', 'Appointment not found.');
            redirect('?page=appointments');
        }

        // Must own this appointment
        if ((int)$appt['patient_id'] !== (int)Auth::currentUser()['id']) {
            require_once __DIR__ . '/../views/errors/403.php';
            exit;
        }

        // Can only cancel pending appointments
        if ($appt['status'] !== 'pending') {
            flashMessage('danger', 'Only pending appointments can be cancelled.');
            redirect('?page=appointments');
        }

        $this->apptModel->updateStatus($id, 'cancelled');
        flashMessage('success', 'Appointment cancelled.');
        redirect('?page=appointments');
    }

    // ── Helpers ──────────────────────────────────────────

    private function getFilters(): array
    {
        return [
            'status'       => $_GET['status']       ?? '',
            'start_date'   => $_GET['start_date']   ?? '',
            'end_date'     => $_GET['end_date']      ?? '',
            'doctor_id'    => $_GET['doctor_id']    ?? '',
            'patient_name' => $_GET['patient_name'] ?? '',
        ];
    }

    private function checkOwnership(array $appt): void
    {
        $role = Auth::role();
        $user = Auth::currentUser();

        if ($role === 'patient' && (int)$appt['patient_id'] !== (int)$user['id']) {
            require_once __DIR__ . '/../views/errors/403.php';
            exit;
        }

        if ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId((int)$user['id']);
            if (!$doctor || (int)$appt['doctor_id'] !== (int)$doctor['id']) {
                require_once __DIR__ . '/../views/errors/403.php';
                exit;
            }
        }
    }
}