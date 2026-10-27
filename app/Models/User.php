<?php

namespace App\Models;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\Concerns\LogsActivity;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, LogsActivity, Notifiable;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => Role::Editor->value,
        'is_active' => true,
    ];

    /**
     * @var list<string>
     */
    protected array $activityIgnored = ['last_login_at', 'email_verified_at'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    public function hasPermission(Permission $permission): bool
    {
        return $this->is_active && $this->role->allows($permission);
    }

    public function hasRole(Role $role): bool
    {
        return $this->role === $role;
    }

    /**
     * @return HasMany<ProposalRequest, $this>
     */
    public function assignedProposalRequests(): HasMany
    {
        return $this->hasMany(ProposalRequest::class, 'assigned_to');
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Users whose role grants the permission.
     */
    #[Scope]
    protected function withPermission(Builder $query, Permission $permission): void
    {
        $roles = array_filter(Role::cases(), fn (Role $role): bool => $role->allows($permission));

        $query->whereIn('role', array_map(fn (Role $role): string => $role->value, $roles));
    }
}
