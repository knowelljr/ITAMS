Middleware;

use App\Helpers\JWT;

class AuthMiddleware
{
    public static function check()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            header('Location: /login');
            exit;
        }
    }

    public static function checkRole($roles = [])
    {
        self::check();

        if (!in_array($_SESSION['user_role'], $roles)) {
            http_response_code(403);
            die('Access Denied: You do not have permission to access this resource.');
        }
    }

    public static function guest()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
    }
}