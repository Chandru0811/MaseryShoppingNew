<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\TempUsers;
use Auth;
use App\Traits\ApiResponses;

class NotificationController extends Controller
{
    use ApiResponses;

    public function sendnotification(Request $request)
    {
        //$notification = Notification::create($request->all());

        $tempUsers = TempUsers::get();
        foreach ($tempUsers as $tempUser) {
            Notification::create([
                'ip_address' => $tempUser->ip_address,
                'title' => $request->title,
                'message' => $request->message,
                'priority' => $request->priority
            ]);
        }
        return response()->json(['message' => 'Notification send successfully!']);
    }

    public function getnotification(Request $request)
    {
        //$userId = Auth::guard('api')->user();
        //$ip_address = $request->ip();
        $notification = Notification::where('is_sent', 0)->orderBy('created_at', 'desc')->first();
        if ($notification) {
            return response()->json([
                'success' => true,
                'data' => $notification
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No notifications found'
            ]);
        }
    }

    public function getallnotifications()
    {
        $notifications = Notification::orderBy('created_at', 'desc')->distinct()->get();
        return $this->success('Notification Messages Retrieved Successfully!', $notifications);
    }

    public function updatenotification(Request $request, $ip_address)
    {
        $user_id = $request->user_id;
        $notification_id = $request->id;

        $notification = Notification::where('ip_address', $ip_address)->update(['is_sent' => 1]);

        if ($notification) {
            return response()->json([
                'success' => true,
                'data' => $notification
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => ' notifications not updated'
            ]);
        }
    }

    public function storeTempUsers(Request $request)
    {
        $brand = TempUsers::create($request->all());

        return response()->json(['message' => 'TempUsers Created successfully!']);
    }
}
