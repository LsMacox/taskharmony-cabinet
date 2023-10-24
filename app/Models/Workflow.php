<?php

namespace App\Models;

use App\Models\States\WorkflowStatusState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

class Workflow extends Model
{
    use HasFactory, Notifiable, HasStates;

    protected $fillable = [
        'name',
        'group_id',
        'approve_sequence',
    ];

    protected $casts = [
        'status' => WorkflowStatusState::class,
    ];


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
