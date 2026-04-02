<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // Role checking methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isWarehouseStaff(): bool
    {
        return $this->role === 'warehouse_staff';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    // Relationships
    public function workInstructions(): HasMany
    {
        return $this->hasMany(WorkInstruction::class, 'assigned_user_id');
    }

    /** @return Builder<User> */
    public static function activeAdmins(): Builder
    {
        return static::query()->where('role', 'admin')->where('is_active', true);
    }

    /** @return Builder<User> */
    public static function activeWarehouseStaff(): Builder
    {
        return static::query()->where('role', 'warehouse_staff')->where('is_active', true);
    }
}
