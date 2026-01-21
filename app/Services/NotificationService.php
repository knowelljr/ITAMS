<?php

namespace App\Services;

class NotificationService
{
    protected $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    public function notifyNewRequest($request)
    {
        // Logic to send email notifications via Office 365 SMTP
        // You can use the mailer instance to send an email
        $this->mailer->send('new_request', $request);
        // Logic for database notifications
        $this->saveNotification('New Request', $request);
    }

    public function notifyApproval($request)
    {
        $this->mailer->send('approval', $request);
        $this->saveNotification('Approval', $request);
    }

    public function notifyIssuance($request)
    {
        $this->mailer->send('issuance', $request);
        $this->saveNotification('Issuance', $request);
    }

    public function notifyRejection($request)
    {
        $this->mailer->send('rejection', $request);
        $this->saveNotification('Rejection', $request);
    }

    protected function saveNotification($type, $data)
    {
        // Logic to save the notification to the database
        // Example: Notification::create(['type' => $type, 'data' => $data]);
    }
}
