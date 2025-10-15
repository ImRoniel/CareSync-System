<?php
// controllers/auth/SessionManager.php

class SessionManager
{
    public function __construct()
    {
        $this->startSession();
    }

    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            header("Location: /Caresync-System/login/login.php");
            exit();
        }
    }

    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
}
?>