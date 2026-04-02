<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkInstructionUnassignedNotification extends Notification
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
            'title_key' => 'notifications.wi.unassigned_title',
            'body_key' => 'notifications.wi.unassigned_body',
            'replace' => [
                'number' => $this->wiNumber,
                'title' => $this->title,
            ],
            'action_url' => route('user.work-instructions.index'),
        ];
    }
}
