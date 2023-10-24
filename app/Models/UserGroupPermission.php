<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class UserGroupPermission extends Model
{
    use HasFactory, Notifiable;

    public function group(): HasOne
    {
        return $this->hasOne(Group::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
