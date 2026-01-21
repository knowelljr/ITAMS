<?php

namespace App\Models;

class Notification {
    private $notifications = [];
    
    public function create($userId, $message) {
        $notification = [
            'id' => count($this->notifications) + 1,
            'userId' => $userId,
            'message' => $message,
            'read' => false,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->notifications[] = $notification;
        return $notification;
    }

    public function markAsRead($id) {
        foreach ($this->notifications as &$notification) {
            if ($notification['id'] === $id) {
                $notification['read'] = true;
                return $notification;
            }
        }
        return null;
    }

    public function getUserNotifications($userId) {
        return array_filter($this->notifications, function($notification) use ($userId) {
            return $notification['userId'] === $userId;
        });
    }

    public function deleteNotification($id) {
        foreach ($this->notifications as $key => $notification) {
            if ($notification['id'] === $id) {
                unset($this->notifications[$key]);
                return true;
            }
        }
        return false;
    }
}