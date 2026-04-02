<?php

namespace App\Support;

use App\Models\Item;
use App\Models\User;
use App\Models\WorkInstruction;
use App\Notifications\StockMovementNotification;
use App\Notifications\WorkInstructionAdminActivityNotification;
use App\Notifications\WorkInstructionAssignedNotification;
use App\Notifications\WorkInstructionDeletedNotification;
use App\Notifications\WorkInstructionUnassignedNotification;
use Illuminate\Support\Facades\Notification;

final class AppNotifier
{
    public static function stockMovement(string $direction, User $actor, Item $item, int $qty): void
    {
        try {
            $recipients = User::activeAdmins()->get()
                ->merge(User::activeWarehouseStaff()->get())
                ->unique('id')
                ->reject(fn (User $u) => $u->id === $actor->id)
                ->values();

            if ($recipients->isEmpty()) {
                return;
            }

            Notification::send($recipients, new StockMovementNotification(
                $direction,
                $actor->name,
                $item->name,
                $item->unit,
                $qty,
                route('admin.reports.stock'),
                route('warehouse-staff.stock-history'),
            ));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function workInstructionAssigned(WorkInstruction $workInstruction): void
    {
        try {
            $user = User::query()->find($workInstruction->assigned_user_id);
            $user?->notify(new WorkInstructionAssignedNotification($workInstruction));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function workInstructionReassigned(
        WorkInstruction $workInstruction,
        int $previousAssigneeId,
        string $previousWiNumber,
        string $previousTitle,
    ): void {
        try {
            $newId = (int) $workInstruction->assigned_user_id;
            if ($newId !== $previousAssigneeId) {
                User::query()->find($newId)?->notify(new WorkInstructionAssignedNotification($workInstruction));
                User::query()->find($previousAssigneeId)?->notify(
                    new WorkInstructionUnassignedNotification($previousWiNumber, $previousTitle)
                );
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function workInstructionDeleted(int $assigneeId, string $wiNumber, string $title): void
    {
        try {
            User::query()->find($assigneeId)?->notify(new WorkInstructionDeletedNotification($wiNumber, $title));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** @param  'item_done'|'wi_done'|'checklist'  $kind */
    public static function workInstructionAdminActivity(
        string $kind,
        WorkInstruction $workInstruction,
        string $workerName,
        ?string $itemName = null,
    ): void {
        try {
            Notification::send(
                User::activeAdmins()->get(),
                new WorkInstructionAdminActivityNotification($kind, $workerName, $workInstruction, $itemName)
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
