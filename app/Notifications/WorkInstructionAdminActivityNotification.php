<?php

namespace App\Notifications;

use App\Models\WorkInstruction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkInstructionAdminActivityNotification extends Notification
{
    use Queueable;

    /** @param  'item_done'|'wi_done'|'checklist'  $kind */
    public function __construct(
        public string $kind,
        public string $workerName,
        public WorkInstruction $workInstruction,
        public ?string $itemName = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $wi = $this->workInstruction;
        $actionUrl = route('admin.work-instructions.show', $wi);

        return match ($this->kind) {
            'wi_done' => [
                'title_key' => 'notifications.wi_admin.wi_done_title',
                'body_key' => 'notifications.wi_admin.wi_done_body',
                'replace' => [
                    'worker' => $this->workerName,
                    'number' => $wi->wi_number,
                    'title' => $wi->title,
                ],
                'action_url' => $actionUrl,
            ],
            'checklist' => [
                'title_key' => 'notifications.wi_admin.checklist_title',
                'body_key' => 'notifications.wi_admin.checklist_body',
                'replace' => [
                    'worker' => $this->workerName,
                    'number' => $wi->wi_number,
                    'title' => $wi->title,
                ],
                'action_url' => $actionUrl,
            ],
            default => [
                'title_key' => 'notifications.wi_admin.item_done_title',
                'body_key' => $this->itemName
                    ? 'notifications.wi_admin.item_done_item_body'
                    : 'notifications.wi_admin.item_done_body',
                'replace' => array_filter([
                    'worker' => $this->workerName,
                    'number' => $wi->wi_number,
                    'title' => $wi->title,
                    'item' => $this->itemName,
                ]),
                'action_url' => $actionUrl,
            ],
        };
    }
}
