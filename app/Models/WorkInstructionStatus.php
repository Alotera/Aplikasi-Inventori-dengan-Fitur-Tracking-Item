<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkInstructionStatus extends Model
{
    use HasFactory;

    protected $table = 'work_instruction_status';

    protected $fillable = [
        'work_instruction_id',
        'status_progress',
        'status_ke_tuntasan',
        'notes',
        'status_updated_at',
    ];

    protected $casts = [
        'status_updated_at' => 'datetime',
    ];

    // Relationships
    public function workInstruction(): BelongsTo
    {
        return $this->belongsTo(WorkInstruction::class);
    }

    // Helper methods
    public function isOverdue(): bool
    {
        return $this->workInstruction->deadline !== null
            && $this->workInstruction->deadline->isPast();
    }

    public function getProgressPercentage(): int
    {
        return $this->status_ke_tuntasan;
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status_progress) {
            'completed' => 'bg-green-100 text-green-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'pending' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
