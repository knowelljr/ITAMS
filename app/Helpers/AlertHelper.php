<?php

namespace App\Helpers;

/**
 * AlertHelper - Centralized alert management for the application
 * 
 * Usage:
 *   AlertHelper::success('Operation completed successfully');
 *   AlertHelper::error('An error occurred');
 *   AlertHelper::warning('Please confirm this action');
 *   AlertHelper::info('Processing your request');
 *   AlertHelper::restriction('You do not have permission');
 *   AlertHelper::systemError('System encountered an error');
 */
class AlertHelper
{
    /**
     * Set success alert
     */
    public static function success($message)
    {
        $_SESSION['success'] = $message;
    }

    /**
     * Set error alert
     */
    public static function error($message)
    {
        $_SESSION['error'] = $message;
    }

    /**
     * Set warning alert
     */
    public static function warning($message)
    {
        $_SESSION['warning'] = $message;
    }

    /**
     * Set info alert
     */
    public static function info($message)
    {
        $_SESSION['info'] = $message;
    }

    /**
     * Set restriction/permission denied alert
     */
    public static function restriction($message = 'You do not have permission to access this resource')
    {
        $_SESSION['restriction'] = $message;
    }

    /**
     * Set system error alert
     */
    public static function systemError($message = 'System error occurred. Please contact administrator')
    {
        $_SESSION['system_error'] = $message;
    }

    /**
     * Validate operation with alert on failure
     * 
     * Usage:
     *   if (AlertHelper::validate($condition, 'Error message')) {
     *       // proceed
     *   }
     */
    public static function validate($condition, $errorMessage)
    {
        if (!$condition) {
            self::error($errorMessage);
            return false;
        }
        return true;
    }

    /**
     * Check authorization with alert on failure
     * 
     * Usage:
     *   if (AlertHelper::authorize($userRole === 'ADMIN', 'Admin access required')) {
     *       // proceed
     *   }
     */
    public static function authorize($authorized, $restrictionMessage = 'You do not have permission to access this resource')
    {
        if (!$authorized) {
            self::restriction($restrictionMessage);
            return false;
        }
        return true;
    }

    /**
     * Try-catch wrapper with automatic error alert
     * 
     * Usage:
     *   AlertHelper::tryExecute(function() {
     *       // your code here
     *   }, 'Operation completed');
     */
    public static function tryExecute(callable $callback, $successMessage = null)
    {
        try {
            $result = call_user_func($callback);
            if ($successMessage) {
                self::success($successMessage);
            }
            return $result;
        } catch (\Exception $e) {
            self::error($e->getMessage());
            return false;
        }
    }

    /**
     * Clear all alerts
     */
    public static function clearAll()
    {
        unset($_SESSION['success']);
        unset($_SESSION['error']);
        unset($_SESSION['warning']);
        unset($_SESSION['info']);
        unset($_SESSION['restriction']);
        unset($_SESSION['system_error']);
    }
}
?>
