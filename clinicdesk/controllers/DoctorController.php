<?php

require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';

class DoctorController
{
    private DoctorModel $doctorModel;
    private SpecializationModel $specModel;

    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
        $this->specModel   = new SpecializationModel();
    }

    /**
     * List all doctors
     */
    public function index(): void
    {
        Auth::requireRole('admin');

        $page      = max(1, (int)($_GET['p'] ?? 1));
        $total     = $this->doctorModel->countAll();
        $doctors   = $this->doctorModel->getAllPaginated($page);
        $pager     = new Paginator($total, ITEMS_PER_PAGE, $page);
        $pageTitle = 'Doctors';

        require_once __DIR__ . '/../views/doctors/index.php';
    }

    /**
     * Edit doctor record
     */
    public function edit(): void
    {
        Auth::requireRole('admin', 'doctor');

        $id     = (int)($_GET['id'] ?? 0);
        $doctor = $this->doctorModel->findById($id);

        if (!$doctor) {
            flashMessage('danger', 'Doctor not found.');
            redirect('?page=doctors');
        }

        // Doctor can only edit their own record
        if (Auth::role() === 'doctor') {
            $myDoctor = $this->doctorModel->findByUserId((int)Auth::currentUser()['id']);
            if (!$myDoctor || $myDoctor['id'] !== $doctor['id']) {
                require_once __DIR__ . '/../views/errors/403.php';
                exit;
            }
        }

        $specs     = $this->specModel->getAll();
        $pageTitle = 'Edit Doctor';

        require_once __DIR__ . '/../views/doctors/edit.php';
    }

    /**
     * Handle edit doctor POST
     */
    public function update(): void
    {
        Auth::requireRole('admin', 'doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid request.');
            redirect('?page=doctors');
        }

        $doctorId = (int)($_POST['doctor_id'] ?? 0);
        $doctor   = $this->doctorModel->findById($doctorId);

        if (!$doctor) {
            flashMessage('danger', 'Doctor not found.');
            redirect('?page=doctors');
        }

        $days = isset($_POST['available_days']) && is_array($_POST['available_days'])
            ? implode(',', $_POST['available_days'])
            : 'Sun,Mon,Tue,Wed,Thu';

        // Handle photo upload
        $photoFile = $doctor['avatar'] ?? null;
        if (!empty($_FILES['photo']['name'])) {
            $upload = $this->uploadPhoto($_FILES['photo'], $doctorId);
            if ($upload['error']) {
                flashMessage('danger', $upload['error']);
                redirect('?page=doctors&action=edit&id=' . $doctorId);
            }
            $photoFile = $upload['filename'];

            // Update user avatar
            $userModel = new UserModel();
            $userModel->update($doctor['user_id'], [
                'name'   => $doctor['name'],
                'phone'  => $doctor['phone'],
                'avatar' => $photoFile,
            ]);
        }

        $this->doctorModel->update($doctorId, [
            'specialization_id' => (int)($_POST['specialization_id'] ?? 1),
            'bio'               => trim($_POST['bio'] ?? ''),
            'consultation_fee'  => (float)($_POST['consultation_fee'] ?? 0),
            'available_days'    => $days,
        ]);

        flashMessage('success', 'Doctor record updated.');
        redirect(Auth::role() === 'admin' ? '?page=doctors' : '?page=dashboard');
    }

    /**
     * Specializations list
     */
    public function specializations(): void
    {
        Auth::requireRole('admin');

        $specs     = $this->specModel->getAll();
        $pageTitle = 'Specializations';

        require_once __DIR__ . '/../views/doctors/specializations.php';
    }

    /**
     * Add specialization POST
     */
    public function addSpecialization(): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid request.');
            redirect('?page=doctors&action=specializations');
        }

        $name = trim($_POST['name'] ?? '');
        if (!$name) {
            flashMessage('danger', 'Specialization name is required.');
            redirect('?page=doctors&action=specializations');
        }

        $this->specModel->create($name);
        flashMessage('success', 'Specialization added.');
        redirect('?page=doctors&action=specializations');
    }

    /**
     * Delete specialization POST
     */
    public function deleteSpecialization(): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid request.');
            redirect('?page=doctors&action=specializations');
        }

        $id = (int)($_POST['spec_id'] ?? 0);

        if (!$this->specModel->isSafeToDelete($id)) {
            flashMessage('danger', 'Cannot delete — doctors are using this specialization.');
            redirect('?page=doctors&action=specializations');
        }

        $this->specModel->delete($id);
        flashMessage('success', 'Specialization deleted.');
        redirect('?page=doctors&action=specializations');
    }

    /**
     * Upload doctor photo helper
     */
    private function uploadPhoto(array $file, int $doctorId): array
    {
        if ($file['size'] > MAX_PHOTO_SIZE) {
            return ['error' => 'Photo must be under 1MB.', 'filename' => null];
        }

        $imgInfo = getimagesize($file['tmp_name']);
        if (!$imgInfo) {
            return ['error' => 'Invalid image file.', 'filename' => null];
        }

        $allowed = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
        if (!in_array($imgInfo[2], $allowed)) {
            return ['error' => 'Only JPEG and PNG images are allowed.', 'filename' => null];
        }

        $ext      = $imgInfo[2] === IMAGETYPE_JPEG ? 'jpg' : 'png';
        $filename = 'doctor_' . $doctorId . '_' . time() . '.' . $ext;
        $dest     = __DIR__ . '/../' . UPLOAD_DOCTOR_PHOTOS . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['error' => 'Failed to save photo.', 'filename' => null];
        }

        return ['error' => null, 'filename' => $filename];
    }
}