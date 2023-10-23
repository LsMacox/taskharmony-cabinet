<?php

namespace App\Models;

use App\Models\States\WorkflowRequestStatusState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class WorkflowRequest extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => WorkflowRequestStatusState::class,
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
