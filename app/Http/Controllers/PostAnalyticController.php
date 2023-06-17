<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;
use Auth;

class PostAnalyticController extends Controller
{
    const STATUS = 2;
    public function index()
    {
        $data['title'] = 'Post Analytics';
        return view('dashboard.post_analytics.index', $data);
    }

    public function datatable($startDate = null, $endDate = null, $group_id = null)
    {
        $area_id = session('area_id');
        $is_igd = 0;
        if ($area_id == 'igd') {
            $is_igd = 1;
        }
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            // if the startDate and endDate is set, the query will be depend on the given date.
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $model = \App\FinishTransaction::selectRaw('finish_transactions.*, finish_transactions.id as t_id')->where('created_time', '>=', $from);

        if ($group_id != null && $group_id != "null" && $group_id != 0) {
            $model->leftJoin('finish_transaction_tests', 'finish_transaction_tests.finish_transaction_id', 'finish_transactions.id');
            $model->where('finish_transaction_tests.group_id', '=', $group_id);
        }
        // $model->where('is_igd', $is_igd); 
        $model->where('created_time', '<=', $to);
        $model->where('status', '>=', PostAnalyticController::STATUS);
        if ($group_id != null && $group_id != "null" && $group_id != 0) {
            $model->groupBy('finish_transaction_tests.finish_transaction_id');
        }
        $model->orderBy('created_time', 'desc');
        $model->orderBy('cito', 'desc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function datatableTest($transactionId)
    {
        $model = \App\FinishTransactionTest::selectRaw('finish_transaction_tests.*')
            ->leftJoin('tests', 'tests.id', 'finish_transaction_tests.test_id')
            ->where('finish_transaction_id', $transactionId)
            ->orderBy('group_id', 'asc')
            ->orderBy('tests.sequence', 'asc')
            ->get();

        $transaction = \App\FinishTransaction::findOrFail($transactionId);

        // return Datatables::of($model)
        //     ->addIndexColumn()
        //     ->escapeColumns([])
        //     ->make(true);
        $data['table'] = $model;
        $data['transaction'] = $transaction;
        $html = view('dashboard.post_analytics.transaction-test-table', $data)->render();
        return response()->json(['html' => $html, 'data' => $model]);
    }

    public function datatableProcessTime($transactionId)
    {
        $model = \App\FinishTransactionTest::selectRaw('finish_transaction_tests.*, finish_transactions.checkin_time, finish_transactions.checkin_by_name, finish_transactions.analytic_time')
            ->where('finish_transaction_id', $transactionId)
            ->leftJoin('finish_transactions', 'finish_transaction_tests.transaction_id', 'finish_transactions.transaction_id')
            ->orderBy('sequence', 'asc')
            ->groupByRaw('IFNULL(package_id, finish_transaction_tests.id)');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);

        // $model = \App\TransactionTest::selectRaw('transaction_tests.*, transaction_tests.id as tt_id')->where('transaction_id', $transactionId)->leftJoin('tests', 'tests.id', 'transaction_tests.test_id')->orderBy('tests.sequence', 'asc')->groupByRaw('IFNULL(package_id, transaction_tests.id)');
        // return DataTables::of($model)
        //     ->addIndexColumn()
        //     ->escapeColumns([])
        //     ->make(true);



        // print_r($model);
    }

    public function returnToAnalytics($transactionId)
    {
        try {

            $transaction = \App\FinishTransaction::findOrFail($transactionId);

            if (empty($transaction)) {
                throw new \Exception("FInish Transaction Not Found, ID:" . $transactionId);
            }
            DB::delete('delete from finish_transactions where id = ?', [$transactionId]);
            DB::delete('delete from finish_transaction_tests where id = ?', [$transactionId]);

            DB::table('transactions')
                ->where('id', $transaction->transaction_id)
                ->update(
                    [
                        'status' => 1,
                        'post_time' => NULL
                    ]
                );
            $this->logActivity(
                "Delete Finish Transaction with ID $transactionId",
                json_encode($transaction)
            );

            return response()->json(['message' => 'Finish Transaction with ID ' . $transactionId . ' deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updatePrintTest($testtransactionId)
    {
        try {

            $transactionTests = \App\FinishTransactionTest::findOrFail($testtransactionId);

            if (empty($transactionTests)) {
                throw new \Exception("FInish Transaction Not Found, ID:" . $testtransactionId);
            }

            DB::table('finish_transaction_tests')
                ->where('id', $testtransactionId)
                ->update(
                    [
                        'is_print' => !$transactionTests->is_print,
                    ]
                );

            return response()->json(['message' => 'update status print success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function updatePrintMemo($transactionId)
    {
        try {

            $transactionTests = \App\FinishTransaction::findOrFail($transactionId);

            if (empty($transactionTests)) {
                throw new \Exception("FInish Transaction Not Found, ID:" . $transactionId);
            }

            DB::table('finish_transactions')
                ->where('id', $transactionId)
                ->update(
                    [
                        'is_print_memo' => !$transactionTests->is_print_memo,
                    ]
                );

            return response()->json(['message' => 'update status print success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getFirstPrint($transactionId)
    {
        $first_print = DB::table('log_print')
            ->where('finish_transaction_id', $transactionId)->first();

        if ($first_print) {
            $first_print_date = date('d/m/Y H:i', strtotime($first_print->printed_at));
        } else {
            $first_print_date = null;
        }
        return response()->json(['data' => $first_print_date]);
    }

    public function getVerificatorName($transactionId)
    {
        $finish_transaction = DB::table('finish_transactions')
            ->where('id', $transactionId)->first();

        return response()->json(['data' => $finish_transaction->verficator_name]);
    }

    public function printPackageName(Request $request, $transactionId, $packageId)
    {
        try {
            $value = $request->value;

            $query = DB::table('finish_transaction_tests')
                ->where('transaction_id', $transactionId)
                ->where('package_id', $packageId);
            $transactionTest = $query->get();

            if ($value == 1) {
                $updateData = array('print_package_name' => 1);
            } else {
                $updateData = array('print_package_name' => 0);
            }

            DB::table('finish_transaction_tests')
                ->where('transaction_id', $transactionId)
                ->where('package_id', $packageId)
                ->update($updateData);

            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
