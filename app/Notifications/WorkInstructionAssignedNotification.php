<?php

namespace App\Notifications;

use App\Models\WorkInstruction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkInstructionAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkInstruction $workInstruction,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $wi = $this->workInstruction;

        return [
            'title_key' => 'notifications.wi.assigned_title',
            'body_key' => 'notifications.wi.assigned_body',
            'replace' => [
                'number' => $wi->wi_number,
                'title' => $wi->title,
            ],
            'action_url' => route('user.work-instructions.show', $wi),
        ];
    }
}
