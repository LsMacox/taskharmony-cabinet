<?php

namespace App\Models;

use App\ModelFilters\GroupChildrenOfFilter;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Group extends Model
{
    use HasFactory, Notifiable, Filterable, GroupChildrenOfFilter;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'is_department',
    ];

    protected $attributes = [
        'is_department' => false,
    ];

    protected $hidden = ['parent_id'];

    private static array $whiteListFilter = [
        'id',
        'name',
        'description',
        'is_department',
        'created_at',
        'updated_at',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->with('children')
            ->orderBy('is_department', 'desc');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'groups_users')->using(GroupUser::class);
    }

    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class);
    }

    public function userWorkflowApprovals(): HasMany
    {
        return $this->hasMany(UserWorkflowApproval::class);
    }
}
