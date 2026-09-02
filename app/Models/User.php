<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
        'staff_permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'staff_permissions' => 'array',
        ];
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a staff member.
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Check if the account can currently be used.
     */
    public function isActive(): bool
    {
        return $this->is_active ?? true;
    }

    /**
     * Check a staff permission key like "products.edit".
     */
    public function hasStaffPermission(string $feature, string $action): bool
    {
        if (! $this->isStaff() || ! $this->isActive()) {
            return false;
        }

        return in_array("{$feature}.{$action}", $this->staff_permissions ?? [], true);
    }

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isActive() && ($this->isAdmin() || $this->isStaff());
    }
}
