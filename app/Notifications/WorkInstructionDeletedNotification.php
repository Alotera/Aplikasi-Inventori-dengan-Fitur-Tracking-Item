<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkInstructionDeletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $wiNumber,
        public string $title,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title_key' => 'notifications.wi.deleted_title',
            'body_key' => 'notifications.wi.deleted_body',
            'replace' => [
                'number' => $this->wiNumber,
                'title' => $this->title,
            ],
            'action_url' => route('user.work-instructions.index'),
        ];
    }
}
