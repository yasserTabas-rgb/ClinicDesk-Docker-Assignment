<?php

require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * GET — Show login form
     */
    public function loginForm(): void
    {
        // Already logged in? Go to dashboard
        if (Auth::check()) {
            redirect('?page=dashboard');
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * POST — Handle login form submission
     */
    public function login(): void
    {
        // 1. Validate CSRF
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid request. Please try again.');
            redirect('?page=auth&action=loginForm');
        }

        // 2. Sanitize email
        $email    = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        // 3. Find user by email
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            flashMessage('danger', 'Invalid credentials.');
            redirect('?page=auth&action=loginForm');
        }

        // 4. Check account is active
        if ((int) $user['is_active'] !== 1) {
            flashMessage('warning', 'Account suspended. Contact admin.');
            redirect('?page=auth&action=loginForm');
        }

        // 5. Verify password
        if (!password_verify($password, $user['password'])) {
            flashMessage('danger', 'Invalid credentials.');
            redirect('?page=auth&action=loginForm');
        }

        // 6. Login — store session
        Auth::login($user);

        // 7. Redirect to dashboard
        redirect('?page=dashboard');
    }

    /**
     * POST — Handle logout
     */
    public function logout(): void
    {
        // Validate CSRF even for logout
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid request.');
            redirect('?page=auth&action=loginForm');
        }

        Auth::logout();
    }
}