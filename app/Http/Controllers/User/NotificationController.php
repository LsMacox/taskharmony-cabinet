<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\NotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $notifications = Notification::where('user_id', auth()->id())->filter()->get();

        return NotificationResource::collection($notifications);
    }

    public function mark(NotificationRequest $request): Response
    {
        Notification::whereIn('id', $request->input('ids'))->update(['mark' => true]);

        return response()->noContent();
    }
}
