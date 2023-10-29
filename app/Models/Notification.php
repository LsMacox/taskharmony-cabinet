<?php

namespace App\Models;

use App\ModelFilters\StatusesFilter;
use App\Models\States\WorkflowStatus\WorkflowStatusState;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Hootlex\Moderation\Moderatable;
use Hootlex\Moderation\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Notification extends Model
{
    use HasFactory, Notifiable, Filterable;

    protected $fillable = [
        'name',
        'description',
    ];
}
