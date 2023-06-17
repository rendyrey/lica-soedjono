<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\ActivityLog;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function logActivity($action = '', $description = '')
    {
        if (config('activity_log')) {
            return;
        }
        
        $user = Auth::user();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $user->name . ' ' . $action,
            'description' => $description
        ]);
    }

    public function badgeInfo()
    {
        // $area_id = session('area_id');
        // $is_igd = 0;
        // if ($area_id == 'igd') {
        //   $is_igd = 1;
        // }
        $today = date('Y-m-d H:i:s', mktime(0,0,0));
        $eod = date('Y-m-d H:i:s', mktime(23,59,59));
        $data['pre_analytics'] = \App\Transaction::where('created_time', '>=', $today)->where('created_time', '<=', $eod)
        // ->where('is_igd', $is_igd) 
        ->where(function ($query) {
          $query->where('status', '=', PreAnalyticController::STATUS)
            ->orWhere('status', PreAnalyticController::STATUS_ANALYTIC);
        })->count();

        // $data['analytics'] = \App\Transaction::where('analytic_time', '>=', $today)->where('is_igd',$is_igd)->where('status', AnalyticController::STATUS)->count();
        $data['analytics'] = \App\Transaction::where('analytic_time', '>=', $today)
        ->where('analytic_time', '<=', $eod)
        ->where('status', AnalyticController::STATUS)->count();

        // $data['post_analytics'] = \App\Transaction::where('post_time', '>=', $today)->where('is_igd',$is_igd)->where('status', PostAnalyticController::STATUS)->count();
        $data['post_analytics'] = \App\Transaction::where('post_time', '>=', $today)
        ->where('post_time', '<=', $eod)
        ->where('status', PostAnalyticController::STATUS)->count();

        return response()->json($data);
    }
}
