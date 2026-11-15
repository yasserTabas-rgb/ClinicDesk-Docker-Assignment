<?php

class Auth
{
    /**
     * Store user data in session after successful login
     */
    public static function login(array $user): void
    {
        // Prevent session fixation attack
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'   => $user['id'],
            'name' => $user['name'],
            'role' => $user['role'],
        ];
    }

    /**
     * Destroy session and redirect to login
     */
    public static function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '?page=auth&action=loginForm');
        exit;
    }

    /**
     * Check if a user is logged in
     */
    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Get the current logged-in user array, or null
     */
    public static function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Get the current user's role, or empty string
     */
    public static function role(): string
    {
        return $_SESSION['user']['role'] ?? '';
    }

    /**
     * Require one of the given roles — redirect if not authorized
     * Usage: Auth::requireRole('admin') or Auth::requireRole('admin','doctor')
     */
    public static function requireRole(string ...$roles): void
    {
        if (!self::check()) {
            header('Location: ' . BASE_URL . '?page=auth&action=loginForm');
            exit;
        }

        if (!in_array(self::role(), $roles, true)) {
            require_once __DIR__ . '/../views/errors/403.php';
            exit;
        }
    }
}