<?php

/**
 * Redirect to a URL relative to BASE_URL
 */
function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

/**
 * Sanitize output to prevent XSS
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Format a date string for display
 */
function formatDate(string $date): string
{
    return date('d M Y', strtotime($date));
}

/**
 * Format a time string for display
 */
function formatTime(string $time): string
{
    return date('h:i A', strtotime($time));
}

/**
 * Set a flash message in session
 */
function flashMessage(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}