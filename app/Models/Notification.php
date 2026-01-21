<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'data', 'read_at'];

    // Create a new notification
    public static function createNotification($userId, $type, $data)
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'data' => $data,
            'read_at' => null,
        ]);
    }

    // Mark notification as read
    public function markAsRead()
    {
        $this->read_at = now();
        $this->save();
    }

    // Retrieve notifications for a user
    public static function getUserNotifications($userId)
    {
        return self::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
    }

    // Store notification history with types
    public static function types()
    {
        return [
            'request_created',
            'request_approved',
            'request_issued',
            'request_accepted',
        ];
    }
}