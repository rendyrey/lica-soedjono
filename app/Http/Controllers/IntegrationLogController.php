<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;
use Auth;

class IntegrationLogController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pre Analytics';
        return view('dashboard.log_integration.index', $data);
    }

    public function datatablePostData($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
            $model = \App\LogIntegration::selectRaw('log_integrations.*')
                ->where('timestamp', '>=', $from)
                ->where('timestamp', '<=', $to)
                ->where('type', '=', 'POST DATA')
                // ->orderBy('timestamp', 'desc')
                // ->orderBy('id', 'desc')
                ->orderBy('status_sequence', 'asc');

            return DataTables::of($model)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
        }

        // if the startDate and endDate is set, the query will be depend on the given date.
        $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
        $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        $model = \App\LogIntegration::selectRaw('log_integrations.*')
            ->where('timestamp', '>=', $from)
            ->where('timestamp', '<=', $to)
            ->where('type', '=', 'POST DATA')
            // ->orderBy('timestamp', 'desc')
            // ->orderBy('id', 'desc')
            ->orderBy('status_sequence', 'asc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function datatableSendData($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
            $model = \App\LogIntegration::selectRaw('log_integrations.*')
                ->where('timestamp', '>=', $from)
                ->where('timestamp', '<=', $to)
                ->where('type', '=', 'SEND DATA')
                ->orderBy('timestamp', 'desc');
                // ->orderBy('id', 'desc')
                // ->orderBy('status_sequence', 'asc');

            return DataTables::of($model)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
        }

        // if the startDate and endDate is set, the query will be depend on the given date.
        $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
        $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        $model = \App\LogIntegration::selectRaw('log_integrations.*')
            ->where('timestamp', '>=', $from)
            ->where('timestamp', '<=', $to)
            ->where('type', '=', 'SEND DATA')
            ->orderBy('timestamp', 'desc');
            // ->orderBy('id', 'desc')
            // ->orderBy('status_sequence', 'asc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function showLog($logId)
    {
        $log_data = DB::table('log_integrations')->where('id', $logId)->first();

        return response()->json($log_data);
    }
}
