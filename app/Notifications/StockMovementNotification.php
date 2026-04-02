<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StockMovementNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $direction,
        public string $actorName,
        public string $itemName,
        public string $unit,
        public int $quantity,
        public string $actionUrlAdmin,
        public string $actionUrlWarehouse,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $suffix = $this->direction === 'in' ? 'in' : 'out';
        /** @var User $notifiable */
        $actionUrl = $notifiable->isAdmin() ? $this->actionUrlAdmin : $this->actionUrlWarehouse;

        return [
            'title_key' => "notifications.stock.{$suffix}_title",
            'body_key' => "notifications.stock.{$suffix}_body",
            'replace' => [
                'actor' => $this->actorName,
                'item' => $this->itemName,
                'qty' => (string) $this->quantity,
                'unit' => $this->unit,
            ],
            'action_url' => $actionUrl,
        ];
    }
}
