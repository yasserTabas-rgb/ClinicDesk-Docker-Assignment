<?php

class CSRF
{
    /**
     * Generate a CSRF token, store in session, and return it
     * Call this inside every GET form view
     */
    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    /**
     * Validate the submitted token against the session token
     * Always use hash_equals() to prevent timing attacks
     */
    public static function validateToken(string $submittedToken): bool
    {
        if (empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $submittedToken);
    }
}