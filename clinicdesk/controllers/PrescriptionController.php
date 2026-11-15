<?php

require_once __DIR__ . '/../models/PrescriptionModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

class PrescriptionController
{
    private PrescriptionModel $prescModel;
    private AppointmentModel  $apptModel;
    private DoctorModel       $doctorModel;

    public function __construct()
    {
        $this->prescModel  = new PrescriptionModel();
        $this->apptModel   = new AppointmentModel();
        $this->doctorModel = new DoctorModel();
    }

    /**
     * List prescriptions — patient sees own, doctor sees own
     */
    public function index(): void
    {
        Auth::requireRole('doctor', 'patient');

        $user = Auth::currentUser();
        $role = Auth::role();

        if ($role === 'patient') {
            $prescriptions = $this->prescModel->getByPatient((int)$user['id']);
            $pageTitle     = 'My Prescriptions';
            require_once __DIR__ . '/../views/prescriptions/patient_list.php';
        } else {
            // Doctor: show appointments that have prescriptions
            $doctor        = $this->doctorModel->findByUserId((int)$user['id']);
            $prescriptions = $this->prescModel->getByDoctor((int)$doctor['id']);
            $pageTitle     = 'Prescriptions';
            require_once __DIR__ . '/../views/prescriptions/doctor_list.php';
        }
    }

    /**
     * Show add prescription form — doctor only
     */
    public function add(): void
    {
        Auth::requireRole('doctor');

        $apptId = (int)($_GET['appt_id'] ?? 0);
        $appt   = $this->apptModel->findById($apptId);

        if (!$appt) {
            flashMessage('danger', 'Appointment not found.');
            redirect('?page=appointments');
        }

        // Verify ownership
        $doctor = $this->doctorModel->findByUserId((int)Auth::currentUser()['id']);
        if (!$doctor || (int)$appt['doctor_id'] !== (int)$doctor['id']) {
            require_once __DIR__ . '/../views/errors/403.php';
            exit;
        }

        // Must be completed
        if ($appt['status'] !== 'completed') {
            flashMessage('danger', 'Prescription can only be added to completed appointments.');
            redirect('?page=appointments&action=detail&id=' . $apptId);
        }

        // Must not already have prescription
        if ($this->prescModel->findByAppointmentId($apptId)) {
            flashMessage('warning', 'This appointment already has a prescription.');
            redirect('?page=appointments&action=detail&id=' . $apptId);
        }

        $pageTitle = 'Add Prescription';
        require_once __DIR__ . '/../views/prescriptions/add.php';
    }

    /**
     * Handle add prescription POST — doctor only
     */
    public function store(): void
    {
        Auth::requireRole('doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid request.');
            redirect('?page=appointments');
        }

        $apptId    = (int)($_POST['appointment_id'] ?? 0);
        $appt      = $this->apptModel->findById($apptId);
        $diagnosis = trim($_POST['diagnosis']   ?? '');
        $meds      = trim($_POST['medications'] ?? '');
        $notes     = trim($_POST['notes']       ?? '');

        if (!$appt || !$diagnosis || !$meds) {
            flashMessage('danger', 'Diagnosis and medications are required.');
            redirect('?page=prescriptions&action=add&appt_id=' . $apptId);
        }

        // Handle PDF upload
        $filePath = null;
        if (!empty($_FILES['prescription_file']['name'])) {
            $upload = $this->uploadPDF($_FILES['prescription_file'], $apptId);
            if ($upload['error']) {
                flashMessage('danger', $upload['error']);
                redirect('?page=prescriptions&action=add&appt_id=' . $apptId);
            }
            $filePath = $upload['filename'];
        }

        $this->prescModel->create([
            'appointment_id' => $apptId,
            'diagnosis'      => $diagnosis,
            'medications'    => $meds,
            'notes'          => $notes,
            'file_path'      => $filePath,
        ]);

        flashMessage('success', 'Prescription added successfully.');
        redirect('?page=appointments&action=detail&id=' . $apptId);
    }

    /**
     * Secure file download — ownership verified
     */
    public function download(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');

        $apptId = (int)($_GET['id'] ?? 0);
        $appt   = $this->apptModel->findById($apptId);

        if (!$appt) {
            flashMessage('danger', 'Appointment not found.');
            redirect('?page=appointments');
        }

        $role = Auth::role();
        $user = Auth::currentUser();

        // Ownership check
        if ($role === 'patient') {
            if ((int)$appt['patient_id'] !== (int)$user['id']) {
                require_once __DIR__ . '/../views/errors/403.php';
                exit;
            }
        } elseif ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId((int)$user['id']);
            if (!$doctor || (int)$appt['doctor_id'] !== (int)$doctor['id']) {
                require_once __DIR__ . '/../views/errors/403.php';
                exit;
            }
        }
        // admin: always allowed

        $prescription = $this->prescModel->findByAppointmentId($apptId);

        if (!$prescription || empty($prescription['file_path'])) {
            flashMessage('danger', 'No file attached to this prescription.');
            redirect('?page=appointments&action=detail&id=' . $apptId);
        }

        $filePath = __DIR__ . '/../' . UPLOAD_PRESCRIPTIONS . $prescription['file_path'];

        if (!file_exists($filePath)) {
            flashMessage('danger', 'File not found on server.');
            redirect('?page=appointments&action=detail&id=' . $apptId);
        }

        // Stream file securely
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="prescription_' . $apptId . '.pdf"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    // ── Helpers ──────────────────────────────────────────

    private function uploadPDF(array $file, int $apptId): array
    {
        if ($file['size'] > MAX_PDF_SIZE) {
            return ['error' => 'PDF must be under 3MB.', 'filename' => null];
        }

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($mimeType !== 'application/pdf') {
            return ['error' => 'Only PDF files are allowed.', 'filename' => null];
        }

        $filename = 'prescription_' . $apptId . '_' . time() . '.pdf';
        $dest     = __DIR__ . '/../' . UPLOAD_PRESCRIPTIONS . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['error' => 'Failed to save file.', 'filename' => null];
        }

        return ['error' => null, 'filename' => $filename];
    }
}