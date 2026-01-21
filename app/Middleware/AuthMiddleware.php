<?php
namespace App\Middleware;

class AuthMiddleware
{
    /**
     * White-listed routes that DON'T need authentication
     */
    private static $whiteList = [
        'AuthController@login',
        'AuthController@authenticate',
        'AuthController@register',
        'AuthController@storeRegistration',
    ];

    /**
     * Check if the user is authenticated and has permission
     */
    public static function handle($controller, $method)
    {
        $currentRoute = $controller . '@' . $method;

        // Skip check for whitelisted routes
        if (in_array($currentRoute, self::$whiteList)) {
            return true;
        }

        // Check if logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('auth/login'));
            exit();
        }

        // Role-based protection
        if ($controller === 'UserController') {
            $adminMethods = ['index', 'updateStatus', 'updateRole'];
            if (in_array($method, $adminMethods) && $_SESSION['role'] !== 'Super Admin') {
                header('Location: ' . url('dashboard'));
                exit();
            }
        }

        return true;
    }
}
