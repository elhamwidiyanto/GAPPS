<?php

namespace App\Http\Controllers;

use App\Models\ActivitiesLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function storeLogActivities($subject, $userId)
    {
        $activity = new ActivitiesLog();
        $activity->user_id = $userId;
        $activity->subject = $subject;
        $activity->url = request()->fullUrl();
        $activity->method = request()->method();
        $activity->ip_address = request()->ip();
        $activity->agent = request()->header('User-Agent');
        $activity->save();
    }
}
