<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function send(User $recipient, string $title, string $message): Notification
    {
        return Notification::create([
            'user_id' => $recipient->id,
            'title' => $title,
            'message' => $message,
        ]);
    }
}