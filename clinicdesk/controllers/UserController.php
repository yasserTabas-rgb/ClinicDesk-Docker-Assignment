<?php

require_once __DIR__ . '/../models/UserModel.php';

class UserController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * List all users with pagination + role filter
     */
    public function index(): void
    {
        Auth::requireRole('admin');

        $page    = max(1, (int)($_GET['p'] ?? 1));
        $role    = $_GET['role'] ?? '';
        $total   = $this->userModel->countAll($role);
        $users   = $this->userModel->getAllPaginated($page, $role);
        $pager   = new Paginator($total, ITEMS_PER_PAGE, $page);
        $pageTitle = 'Users';

        require_once __DIR__ . '/../views/users/index.php';
    }

    /**
     * Show create user form
     */
    public function create(): void
    {
        Auth::requireRole('admin');

        require_once __DIR__ . '/../models/SpecializationModel.php';
        $specModel = new SpecializationModel();
        $specs     = $specModel->getAll();
        $pageTitle = 'Create User';

        require_once __DIR__ . '/../views/users/create.php';
    }

    /**
     * Handle create user POST
     */
    public function store(): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid request.');
            redirect('?page=users&action=create');
        }

        $name     = trim($_POST['name']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');
        $role     = $_POST['role']          ?? 'patient';
        $phone    = trim($_POST['phone']    ?? '');

        // Basic validation
        if (!$name || !$email || !$password) {
            flashMessage('danger', 'Name, email and password are required.');
            redirect('?page=users&action=create');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flashMessage('danger', 'Invalid email address.');
            redirect('?page=users&action=create');
        }

        // Check email not already taken
        if ($this->userModel->findByEmail($email)) {
            flashMessage('danger', 'Email already exists.');
            redirect('?page=users&action=create');
        }

        // Create user
        $userId = $this->userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role'     => $role,
            'phone'    => $phone,
        ]);

        // If doctor — also create doctors record
        if ($role === 'doctor') {
            require_once __DIR__ . '/../models/DoctorModel.php';
            $doctorModel = new DoctorModel();

            $days = isset($_POST['available_days']) && is_array($_POST['available_days'])
                ? implode(',', $_POST['available_days'])
                : 'Sun,Mon,Tue,Wed,Thu';

            $doctorModel->create([
                'user_id'           => $userId,
                'specialization_id' => (int)($_POST['specialization_id'] ?? 1),
                'bio'               => trim($_POST['bio'] ?? ''),
                'consultation_fee'  => (float)($_POST['consultation_fee'] ?? 0),
                'available_days'    => $days,
            ]);
        }

        flashMessage('success', 'User created successfully.');
        redirect('?page=users');
    }

    /**
     * Show edit user form
     */
    public function edit(): void
    {
        Auth::requireRole('admin');

        $id   = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->findById($id);

        if (!$user) {
            flashMessage('danger', 'User not found.');
            redirect('?page=users');
        }

        $pageTitle = 'Edit User';
        require_once __DIR__ . '/../views/users/edit.php';
    }

    /**
     * Handle edit user POST
     */
    public function update(): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid request.');
            redirect('?page=users');
        }

        $id   = (int)($_POST['id'] ?? 0);
        $user = $this->userModel->findById($id);

        if (!$user) {
            flashMessage('danger', 'User not found.');
            redirect('?page=users');
        }

        $this->userModel->update($id, [
            'name'   => trim($_POST['name']  ?? ''),
            'phone'  => trim($_POST['phone'] ?? ''),
            'avatar' => $user['avatar'],
        ]);

        // Change password if provided
        if (!empty($_POST['new_password'])) {
            $this->userModel->updatePassword(
                $id,
                password_hash($_POST['new_password'], PASSWORD_BCRYPT)
            );
        }

        flashMessage('success', 'User updated successfully.');
        redirect('?page=users');
    }

    /**
     * Toggle user active/inactive
     */
    public function toggle(): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid request.');
            redirect('?page=users');
        }

        $id = (int)($_POST['id'] ?? 0);

        // Cannot deactivate yourself
        if ($id === (int)Auth::currentUser()['id']) {
            flashMessage('danger', 'You cannot deactivate your own account.');
            redirect('?page=users');
        }

        $this->userModel->toggleActive($id);
        flashMessage('success', 'User status updated.');
        redirect('?page=users');
    }

    /**
     * My Profile — all roles
     */
    public function profile(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');

        $id   = (int)Auth::currentUser()['id'];
        $user = $this->userModel->findById($id);
        $pageTitle = 'My Profile';

        require_once __DIR__ . '/../views/users/profile.php';
    }
}