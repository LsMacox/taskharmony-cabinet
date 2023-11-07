<?php

namespace App\Models;

use App\ModelFilters\UsersExcludeFromGroupsFilter;
use App\Models\FilterDetections\OrCondition;
use App\Models\FilterDetections\WhereInCondition;
use App\Models\FilterDetections\WhereLikeCondition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPermissions, Filterable, UsersExcludeFromGroupsFilter;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    private static array $whiteListFilter = [
        '*',
        'roles.name',
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'groups_users')->using(GroupUser::class);
    }

    public function userWorkflowApprovals(): HasMany
    {
        return $this->hasMany(UserWorkflowApproval::class);
    }

    public function EloquentFilterCustomDetection(): array
    {
        return [
            WhereLikeCondition::class,
            WhereInCondition::class,
            OrCondition::class,
        ];
    }
}
