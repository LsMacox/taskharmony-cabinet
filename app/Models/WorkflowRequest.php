<?php

namespace App\Models;

use App\Models\States\WorkflowRequestStatusState;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

class WorkflowRequest extends Model
{
    use HasFactory, Notifiable, Filterable, HasStates;

    protected $casts = [
        'status' => WorkflowRequestStatusState::class,
    ];

    protected $hidden = ['author_id'];

    private static array $whiteListFilter = [
        'status',
        'created_at',
        'updated_at',
    ];

    public function scopeOnlyForAuth(Builder $query): Builder
    {
        return $query->where('author_id', auth()->id());
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }
}
