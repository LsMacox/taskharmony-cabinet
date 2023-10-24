<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Group extends Model
{
    use HasFactory, Notifiable, Filterable;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'is_department',
    ];

    protected $hidden = ['parent_id'];

    private static array $whiteListFilter = [
        'name',
        'is_department',
        'created_at',
        'updated_at',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class);
    }

    public function userGroupPermission(): BelongsTo
    {
        return $this->belongsTo(UserGroupPermission::class);
    }
}
