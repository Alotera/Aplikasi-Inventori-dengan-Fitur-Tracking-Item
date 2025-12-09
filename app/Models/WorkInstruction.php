<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class WorkInstruction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wi_number',
        'type',
        'title',
        'description',
        'destination_line',
        'dropoff_notes',
        'assigned_user_id',
        'deadline',
        'completed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'work_instruction_items')
                    ->withPivot(['required_quantity', 'actual_quantity', 'condition', 'status', 'notes'])
                    ->withTimestamps();
    }

    public function statusProgress(): HasOne
    {
        return $this->hasOne(WorkInstructionStatus::class);
    }

    public function hasStarted(): bool
    {
        return $this->items()->wherePivot('status', '!=', 'pending')->exists();
    }

    public function isOverdue(): bool
    {
        return $this->deadline !== null
            && $this->deadline->isPast();
    }

    /**
     * Update status progression (pending, in_progress, completed)
     */
    public function updateStatusProgress(): void
    {
        $statusProgress = $this->statusProgress()->firstOrNew();
        $statusProgress->work_instruction_id = $this->id;
        
        $itemsCount = $this->items()->count();

        if ($itemsCount === 0) {
            $statusProgress->status_progress = 'pending';
        } elseif (!$this->items()->wherePivot('status', 'pending')->exists()) {
            // all items finished
            $statusProgress->status_progress = 'completed';
        } elseif ($this->hasStarted()) {
            $statusProgress->status_progress = 'in_progress';
        } else {
            $statusProgress->status_progress = 'pending';
        }

        $statusProgress->status_ke_tuntasan = $this->calculateProgressPercentage();
        $statusProgress->status_updated_at = now();
        $statusProgress->save();
    }

    /**
     * Update main status (not_started, completed, overdue)
     */
    public function updateMainStatus(): void
    {
        $statusProgress = $this->statusProgress()->first();
        $progressStatus = $statusProgress ? $statusProgress->status_progress : 'pending';
        
        if ($progressStatus === 'completed') {
            // If completed, set completion time and check if completed before deadline
            if (!$this->completed_at) {
                $this->completed_at = now();
            }
            
            // Check if completed before deadline
            if ($this->deadline && $this->completed_at->gt($this->deadline)) {
                $this->status = 'overdue'; // Completed after deadline
            } else {
                $this->status = 'completed'; // Completed before or on deadline
            }
        } else {
            // If not completed, clear completion time and check if overdue
            $this->completed_at = null;
            
            if ($this->isOverdue()) {
                $this->status = 'overdue';
            } else {
                $this->status = 'not_started';
            }
        }

        $this->save();
    }

    /**
     * Update both status progression and main status
     */
    public function updateStatus(): void
    {
        $this->updateStatusProgress();
        $this->updateMainStatus();
    }


    public function calculateProgressPercentage(): int
    {
        $totalItems = $this->items()->count();
        if ($totalItems === 0) return 0;

        $finishedItems = $this->items()->wherePivot('status', '!=', 'pending')->count();
        return (int) round(($finishedItems / $totalItems) * 100);
    }

    /**
     * Get the main status (not_started, completed, overdue)
     */
    public function getMainStatus(): string
    {
        return $this->status;
    }

    /**
     * Get the progression status (pending, in_progress, completed)
     */
    public function getProgressionStatus(): string
    {
        $statusProgress = $this->statusProgress()->first();
        return $statusProgress ? $statusProgress->status_progress : 'pending';
    }

    /**
     * Get status label for display
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'not_started' => 'Belum Dikerjakan',
            'completed' => 'Selesai',
            'overdue' => 'Terlambat',
            default => 'Unknown'
        };
    }

    /**
     * Get progression status label for display
     */
    public function getProgressionLabel(): string
    {
        return match($this->getProgressionStatus()) {
            'pending' => 'Pending',
            'in_progress' => 'Dalam Proses',
            'completed' => 'Selesai',
            default => 'Unknown'
        };
    }
}