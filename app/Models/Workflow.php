<?php

namespace App\Models;

use App\ModelFilters\WorkflowStatusesFilter;
use App\Models\States\WorkflowStatus\WorkflowStatusState;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Hootlex\Moderation\Moderatable;
use Hootlex\Moderation\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Workflow extends Model
{
    use HasFactory, Notifiable, Filterable, Moderatable, WorkflowStatusesFilter;

    const MAP_STRING_STATUSES = [
        'approved' => Status::APPROVED,
        'pending' => Status::PENDING,
        'rejected' => Status::REJECTED,
        'postponed' => Status::POSTPONED,
    ];

    protected $fillable = [
        'name',
        'status',
        'group_id',
        'approve_sequence',
    ];

    protected $casts = [
        'state' => WorkflowStatusState::class,
        'approve_sequence' => 'array',
    ];

    protected $attributes = [
        'status' => 0,
        'approve_sequence' => '{}',
    ];

    private static array $whiteListFilter = [
        'name',
        'state',
        'group.name',
        'moderated_at',
        'moderated_by',
        'group_id',
        'created_at',
        'updated_at',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function userWorkflowApprovals(): HasMany
    {
        return $this->hasMany(UserWorkflowApproval::class);
    }
}
