<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;
use Auth;

class QueueManagementController extends Controller
{
    public function index()
    {
        $data['title'] = 'Patient Queue Laboratory';
        return view('dashboard.qms.index', $data);
    }

    public function datatablePreAnalytics($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
            $model = \App\Transaction::where('created_time', '>=', $from)
                ->where('created_time', '<=', $to)
                ->where('status', 0)
                ->where(function ($query) {
                    $query->where('status', '=', PreAnalyticController::STATUS)
                        ->orWhere('status', PreAnalyticController::STATUS_ANALYTIC);
                })
                ->orderBy('cito', 'desc');

            return DataTables::of($model)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
        }

        // if the startDate and endDate is set, the query will be depend on the given date.
        $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
        $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        $model = \App\Transaction::where('created_time', '>=', $from)
            ->where('created_time', '<=', $to)
            ->where('status', 0)
            ->where(function ($query) {
                $query->where('status', '=', PreAnalyticController::STATUS)
                    ->orWhere('status', PreAnalyticController::STATUS_ANALYTIC);
            })
            ->orderBy('cito', 'desc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function datatableAnalytics($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
            $model = \App\Transaction::where('created_time', '>=', $from)
                ->where('created_time', '<=', $to)
                ->where('status', 1)
                ->where(function ($query) {
                    $query->where('status', '=', PreAnalyticController::STATUS)
                        ->orWhere('status', PreAnalyticController::STATUS_ANALYTIC);
                })
                ->orderBy('cito', 'desc');

            return DataTables::of($model)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
        }

        // if the startDate and endDate is set, the query will be depend on the given date.
        $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
        $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        $model = \App\Transaction::where('created_time', '>=', $from)
            ->where('created_time', '<=', $to)
            ->where('status', 1)
            ->where(function ($query) {
                $query->where('status', '=', PreAnalyticController::STATUS)
                    ->orWhere('status', PreAnalyticController::STATUS_ANALYTIC);
            })
            ->orderBy('cito', 'desc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function datatablePostAnalytics($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
            $model = \App\FinishTransaction::where('post_time', '>=', $from)
                ->where('post_time', '<=', $to)
                ->orderBy('cito', 'desc')
                ->orderBy('status', 'desc')
                ->orderBy('completed', 'desc');

            return DataTables::of($model)
                ->addIndexColumn()
                ->escapeColumns([])
                ->make(true);
        }

        // if the startDate and endDate is set, the query will be depend on the given date.
        $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
        $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        $model = \App\FinishTransaction::where('post_time', '>=', $from)
            ->where('post_time', '<=', $to)
            ->orderBy('cito', 'desc')
            ->orderBy('status', 'desc')
            ->orderBy('completed', 'desc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function updateCompletedPatient(Request $request)
    {
        // echo $request->finishTransactionId;
        // die;

        $finishTransaction = \App\FinishTransaction::findOrFail($request->finishTransactionId);

        $finishTransaction->completed = 1;
        $finishTransaction->completed_time = Carbon::now();

        $finishTransaction->save();

        $this->logActivity(
            "Completed Finish Transaction with ID $finishTransaction->id",
            json_encode($finishTransaction)
        );
    }

    public function displayQueue()
    {
        $data['title'] = 'Patient Queue Display';
        return view('dashboard.qms.display.qms_display', $data);
    }

    public function datatablePreDisplay()
    {
        $qms = DB::table('transactions')->select('transactions.*', 'patients.name as patient_name')
            ->where('status', 0)
            ->leftJoin('patients', 'transactions.patient_id', '=', 'patients.id')
            ->orderBy('created_time', 'desc');
        $qms->whereRaw("(DATE(created_time) = CURDATE())");

        $data_qms = $qms->get();
        $data['qms'] = $data_qms;

        return response()->json($data_qms);
    }

    public function datatableProsesDisplay()
    {
        $qms = DB::table('transactions')->select('transactions.*', 'patients.name as patient_name')
            ->where('status', "=", 1)
            ->leftJoin('patients', 'transactions.patient_id', '=', 'patients.id')
            ->orderBy('no_lab', 'asc');
        $qms->whereRaw("(DATE(created_time) = CURDATE())");

        $data_qms = $qms->get();
        $data['qms'] = $data_qms;

        return response()->json($data_qms);
    }

    public function datatableSelesaiDisplay()
    {
        $qms = DB::table('finish_transactions')->select('finish_transactions.*')
            ->where('status', 4)
            ->where('completed', "!=", 1)
            ->orderBy('no_lab', 'asc');
        $qms->whereRaw("(DATE(created_time) = CURDATE())");

        $data_qms = $qms->get();
        $data['qms'] = $data_qms;

        return response()->json($data_qms);
    }
}
