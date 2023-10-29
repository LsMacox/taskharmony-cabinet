<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->input('perpage', 15);
        $notifications = Notification::where('user_id', auth()->id())
            ->filter()
            ->paginate($perPage);

        return NotificationResource::collection($notifications);
    }
}
