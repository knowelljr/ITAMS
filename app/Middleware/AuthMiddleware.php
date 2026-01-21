<?php

namespace App\Middleware;

class AuthMiddleware
{
    public static function check()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function checkRole($allowedRoles = [])
    {
        self::check();

        if (!isset($_SESSION['user_role'])) {
            header('Location: /login');
            exit;
        }

        if (!in_array($_SESSION['user_role'], $allowedRoles)) {
            http_response_code(403);
            die('Access Denied - Insufficient Permissions');
        }
    }

    public static function guest()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
    }

    public static function isAdmin()
    {
        self::check();
        if ($_SESSION['user_role'] !== 'ADMIN') {
            http_response_code(403);
            die('Admin access only');
        }
    }
}