<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;
use Auth;


class PreAnalyticController extends Controller
{
    const STATUS = 0;
    const STATUS_ANALYTIC = 1;
    public function index()
    {
        $data['title'] = 'Pre Analytics';
        return view('dashboard.pre_analytics.index', $data);
    }

    /**
     * This datatable is for showing all the data for transaction on pre analytics
     * 
     * @param string $startDate The start date of the transaction was created.
     * @param string $endDate The end date of the transaction was created.
     * 
     * @return collection of transaction that was created between the dates
     */
    public function datatable($startDate = null, $endDate = null)
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
            $model = \App\Transaction::where('created_time', '>=', $from)
                ->where('created_time', '<=', $to)
                // ->where('is_igd', $is_igd)
                ->where(function ($query) {
                    $query->where('status', '=', PreAnalyticController::STATUS)
                        ->orWhere('status', PreAnalyticController::STATUS_ANALYTIC);
                })
                ->orderBy('cito', 'desc')
                ->orderBy('check', 'desc')
                ->orderByRaw("CASE WHEN no_lab IS NOT NULL THEN 0 ELSE 1 END ASC")
                ->orderBy('no_lab', 'asc');

            return DataTables::of($model)
                ->addIndexColumn()
                ->addColumn('able_to_analytics', function($data) {
                    $transactionId = $data->id;
                    if ($data->status == '1') {
                        return false;
                    }
        
                    if ($data->no_lab == '' || $data->no_lab == null) {
                        return false;
                    }
    
                    $transactionTests = \App\TransactionTest::where('transaction_id', $transactionId)->get();
    
                    foreach ($transactionTests as $test) {
                        if ($test->analyzer_id == null || $test->analyzer_id == '') {
                            return false;
                        }
        
                        if ($test->draw == null || $test->draw == '0') {
                            return false;
                        }
                    }
    
                    return true;
                })
                ->escapeColumns([])
                ->make(true);
        }

        // if the startDate and endDate is set, the query will be depend on the given date.
        $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
        $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        $model = \App\Transaction::where('created_time', '>=', $from)
            ->where('created_time', '<=', $to)
            // ->where('is_igd', $is_igd)
            ->where(function ($query) {
                $query->where('status', '=', PreAnalyticController::STATUS)
                    ->orWhere('status', PreAnalyticController::STATUS_ANALYTIC);
            })
            ->orderBy('cito', 'desc')
            ->orderBy('check', 'asc')
            ->orderByRaw("CASE WHEN no_lab IS NOT NULL THEN 0 ELSE 1 END ASC")
            ->orderBy('no_lab', 'asc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->addColumn('able_to_analytics', function($data) {
                $transactionId = $data->id;
                if ($data->status == '1') {
                    return false;
                }
    
                if ($data->no_lab == '' || $data->no_lab == null) {
                    return false;
                }

                $transactionTests = \App\TransactionTest::where('transaction_id', $transactionId)->get();

                foreach ($transactionTests as $test) {
                    if ($test->analyzer_id == null || $test->analyzer_id == '') {
                        return false;
                    }
    
                    if ($test->draw == null || $test->draw == '0') {
                        return false;
                    }
                }

                return true;
                return true;
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * The datatable function for showing the data of the transaction's tests on the selected transaction
     * 
     * @param string $transactionId The transaction id that was selected on the table (on row click)
     * @return collection of transaction's test on the selected transaction
     */
    public function datatableTransactionTest($transactionId)
    {
        $model = \App\TransactionTest::selectRaw('transaction_tests.*, transaction_tests.id as tt_id')->where('transaction_id', $transactionId)->leftJoin('tests', 'tests.id', 'transaction_tests.test_id')->orderBy('tests.sequence', 'asc')->groupByRaw('IFNULL(package_id, transaction_tests.id)');
        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * The datatable function for showing the data of the test specimen on the selected transaction
     * 
     * @param string $transactionId The transaction id that was selected on the table (on row click)
     * @return collection of 
     */
    public function datatableTransactionSpecimen($transactionId)
    {
        // get all the transaction's tests first to get all the test id(s)
        $transactionTests = \App\TransactionTest::selectRaw('test_id')->where('transaction_id', $transactionId)->get()->toArray();
        $testIds = [];
        foreach ($transactionTests as $testId) {
            $testIds[] = $testId['test_id'];
        }

        // get all specimen depend on the test
        $model = \App\Test::selectRaw('transaction_tests.transaction_id as transaction_id, GROUP_CONCAT(tests.id SEPARATOR ",") as test_ids, IFNULL(GROUP_CONCAT(transaction_tests.draw SEPARATOR ","),0) as draw, specimen_id, SUM(volume) as volume, unit, transactions.no_lab')
            ->leftJoin('transaction_tests', 'tests.id', '=', 'transaction_tests.test_id')
            ->leftJoin('transactions', 'transaction_tests.transaction_id', '=', 'transactions.id')
            ->where('transaction_tests.transaction_id', $transactionId)
            ->whereIn('tests.id', $testIds)
            ->groupBy('specimen_id', 'transaction_id', 'no_lab')
            ->orderBy('tests.sequence', 'asc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * The datatable function to show all test when adding the patient depend the the room class selected on previous form
     * 
     * @param string $roomClass The class of the room E.g. 1 or 2
     * @return collection of the test analytics view table
     */
    public function datatableTest($roomClass)
    {
        $model = \App\TestPreAnalyticsView::selectRaw("*")->groupBy('unique_id');
        // $model = DB::table('test_pre_analytics_view')
        // ->select('test_pre_analytics_view.*')
        // ->leftJoin('tests', 'test_pre_analytics_view.id', '=', 'tests.id')
        // ->where('tests.is_active', 1)
        // ->orWhere('test_pre_analytics_view.type', 'package')
        // ->groupBy('unique_id');


        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * The datatable function to retrieve all the test list base on room
     * the function is only for user do the edit test on created transaction
     * 
     * @param string/integer $roomClass The room class, E.g. 1
     * @param string/integer $transactionId The transaction Id
     * 
     * @return json 
     */
    public function datatableEditTest($roomClass, $transactionId)
    {
        // get all the transaction's tests first to get all the test id(s)
        $transactionTests = \App\TransactionTest::selectRaw('*')->where('transaction_id', $transactionId)->get()->toArray();
        $uniqueIds = [];
        $uniqueZeroClassIds = [];
        foreach ($transactionTests as $testId) {
            if ($testId['type'] == 'single') {
                $uniqueIds[] = 's' . $roomClass . '-' . $testId['test_id'];
                $uniqueZeroClassIds[] = 's0-' . $testId['test_id'];
            } else if ($testId['type'] == 'package') {
                $uniqueIds[] = 'p' . $roomClass . '-' . $testId['package_id'];
                $uniqueZeroClassIds[] = 'p0-' . $testId['package_id'];
            }
        }

        $model = \App\TestPreAnalyticsView::where(function ($q) use ($roomClass) {
            // $q->where('class', $roomClass);
            // ->orWhere('class', '0')
            // ->orWhereNull('class');
        })->whereNotIn('unique_id', $uniqueIds)->whereNotIn('unique_id', $uniqueZeroClassIds)->groupBy('unique_id');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Function for table on the selected test list on right side when user do the edit test
     * on the created transaction
     * 
     * @param string/integer $roomClass E.g. 1
     * @param string/integer $transactionId The transaction id
     * 
     * @return json
     */
    public function selectedEditTest($roomClass, $transactionId)
    {
        $transactionTests = \App\TransactionTest::selectRaw('*')->where('transaction_id', $transactionId)->get()->toArray();
        $testIds = [];
        $uniqueIds = [];
        $uniqueZeroClassIds = [];
        foreach ($transactionTests as $testId) {
            $testIds[] = $testId['test_id'];
            if ($testId['type'] == 'single') {
                $uniqueIds[] = 's' . $roomClass . '-' . $testId['test_id'];
                $uniqueZeroClassIds[] = 's0-' . $testId['test_id'];
            } else if ($testId['type'] == 'package') {
                $uniqueIds[] = 'p' . $roomClass . '-' . $testId['package_id'];
                $uniqueZeroClassIds[] = 'p0-' . $testId['package_id'];
            }
        }
        // return response()->json(['a' => $uniqueIds]);

        $data1 = \App\TransactionTest::selectRaw('test_pre_analytics_view.*,transaction_tests.draw as draw, transaction_tests.id as transaction_test_id')
            ->leftJoin('test_pre_analytics_view', 'test_pre_analytics_view.id', '=', 'transaction_tests.test_id')
            ->where(function ($query) use ($uniqueIds, $uniqueZeroClassIds) {
                $query->whereIn('unique_id', $uniqueIds)
                    ->orwhereIn('unique_id', $uniqueZeroClassIds);
            })
            // ->where(function($q) use($roomClass) {
            // $q->where('class', $roomClass)
            //     ->orWhere('class', '0')
            //     ->orWhereNull('class');
            // })
            ->where('transaction_tests.transaction_id', $transactionId)
            ->where('test_pre_analytics_view.type', 'single')->get()->toArray();

        $data2 = \App\TransactionTest::selectRaw('test_pre_analytics_view.*,transaction_tests.draw as draw, transaction_tests.id as transaction_test_id')
            ->leftJoin('test_pre_analytics_view', 'test_pre_analytics_view.id', '=', 'transaction_tests.package_id')
            ->where(function ($query) use ($uniqueIds, $uniqueZeroClassIds) {
                $query->whereIn('unique_id', $uniqueIds)
                    ->orwhereIn('unique_id', $uniqueZeroClassIds);
            })
            // ->where(function($q) use($roomClass) {
            // $q->where('class', $roomClass)
            //     ->orWhere('class', '0')
            //     ->orWhereNull('class');
            // })
            ->where('transaction_tests.transaction_id', $transactionId)
            ->where('test_pre_analytics_view.type', 'package')
            ->groupBy('transaction_tests.package_id')
            ->get()->toArray();

        $data = array_merge($data1, $data2);

        $uniqueIds = [];
        foreach ($data as $theTest) {
            $uniqueIds[] = $theTest['unique_id'];
        }

        return response()->json(['selected_test_ids' => implode(",", $testIds), 'selected_test_unique_ids' => implode(",", $uniqueIds), 'data' => $data]);
    }

    /**
     * Function when user click add test or select test to the selected list
     * 
     * @param object $request request data/form data from front end
     * 
     * @return json
     */
    public function editTestAdd(Request $request)
    {
        $test = \App\TestPreAnalyticsView::where('unique_id', $request->unique_id)->first(); // get the tests views based on unique id
        $roomClass = $request->room_class; // get the room class
        if ($test->type == 'single') {
            $transactionTest = \App\TransactionTest::create([
                'transaction_id' => $request->transaction_id,
                'test_id' => $test->id,
                'price_id' => $test->price_id,
                'group_id' => $test->group_id,
                'type' => 'single'
            ]);

            $this->logActivity(
                "Make an additional test on transaction with ID $request->transaction_id",
                json_encode($transactionTest)
            );
        } else {
            $package = \App\Package::findOrFail($test->id);
            foreach ($package->package_tests as $testItem) {
                $transactionTest = \App\TransactionTest::create([
                    'transaction_id' => $request->transaction_id,
                    'test_id' => $testItem->test_id,
                    'price_id' => $test->price_id,
                    'group_id' => $test->group_id,
                    'type' => 'single'
                ]);

                $this->logActivity(
                    "Make an additional test on transaction with ID $request->transaction_id",
                    json_encode($transactionTest)
                );
            }
        }

        $transactionTests = \App\TransactionTest::selectRaw('*')->where('transaction_id', $request->transaction_id)->get()->toArray();
        $testIds = [];
        foreach ($transactionTests as $testId) {
            $testIds[] = $testId['test_id'];
        }

        $data = \App\TransactionTest::selectRaw('test_pre_analytics_view.*,transaction_tests.draw as draw, transaction_tests.id as transaction_test_id')
            ->leftJoin('test_pre_analytics_view', 'test_pre_analytics_view.id', '=', 'transaction_tests.test_id')->where(function ($q) use ($roomClass) {
                // $q->where('class', $roomClass);
                // ->orWhere('class', '0')
                // ->orWhereNull('class');
            })->whereIn('transaction_tests.test_id', $testIds)
            ->where('transaction_tests.transaction_id', $request->transaction_id)
            ->where('test_pre_analytics_view.type', 'single')->get();

        return response()->json(['message' => 'Test added successfully!', 'selected_test_ids' => implode(",", $testIds), 'data' => $data]);
    }

    /**
     * Function when user do the deletion test on the selected test list on the right side when user do the edit test
     * on the created transction
     * 
     * @param integer $transactionTestId The transaction_tests id
     * 
     * @return json
     */
    public function editTestDelete($transactionTestId)
    {
        try {
            $data = \App\TransactionTest::findOrFail($transactionTestId);
            $data->delete();

            $this->logActivity(
                "Make a deletion test on transaction test with ID $transactionTestId",
                json_encode($data)
            );

            return response()->json(['message' => 'Delete test successfully!']);
        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => 'Test deleted successfully!']);
    }

    public function editTestUpdate(Request $request)
    {
        // delete first
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $transactionId = $request->transaction_id;
            $transactionTest = \App\TransactionTest::where('transaction_id', $transactionId)->where('verify', 1)->exists();
            if ($transactionTest) {
                throw new \Exception("You can't edit because one of the test is verified");
            }
            \App\TransactionTest::where('transaction_id', $transactionId)->where('draw', '<>', 1)->delete();
            $testUniqueIds = explode(',', $request->unique_ids);

            $tests = \App\TestPreAnalyticsView::whereIn('unique_id', $testUniqueIds);
            $transaction = \App\Transaction::findOrFail($transactionId);
            $autoDraw = $transaction->room->auto_draw;
            // $isUndrawExists = \App\TransactionTest::where('transaction_id', $transactionId)->where(function($q) {
            //     $q->where('draw', '0')->orWhere('draw', null);
            // })->exists();

            if ($tests->count() > 0) {
                $inputData = [];
                $now = Carbon::now();
                foreach ($tests->get() as $test) {
                    $inputData['transaction_id'] = $transactionId;
                    $inputData['price_id'] = $test->price_id;
                    $inputData['group_id'] = $test->group_id;
                    $inputData['type'] = $test->type;
                    $inputData['test_id'] = NULL;
                    $inputData['package_id'] = NULL;
                    $inputData['input_time'] = $now;
                    if ($autoDraw) {
                        $inputData['draw'] = true;
                        $inputData['draw_time'] = $now;
                        $inputData['draw_by'] = $user->id;
                    }
                    //check have default or not
                    $checkDefaultAnalyzer = \App\Analyzer::where('group_id', $test->group_id)->where('is_default', 1)->first();
                    if ($checkDefaultAnalyzer) {
                        $inputData['analyzer_id'] = $checkDefaultAnalyzer->id;
                    } else {
                        $inputData['result_label'] = null;
                    }

                    $checkDefaultLabel = \App\Result::selectRaw('results.id ,tests.range_type')->where('test_id', $test->id)->join('tests', 'tests.id', '=', 'results.test_id')->where('is_default', 1)->where('tests.range_type', '=', 'label')->first();
                    if ($checkDefaultLabel) {
                        $inputData['result_label'] = $checkDefaultLabel->id;
                    } else {
                        $inputData['result_label'] = null;
                    }
                    switch ($test->type) {
                        case 'single':
                            $inputData['test_id'] = $test->id;
                            $checkExisting = \App\TransactionTest::where('transaction_id', $transactionId)->where('test_id', $test->id)->exists();
                            if (!$checkExisting) {
                                \App\TransactionTest::create($inputData);
                            }
                            break;
                        case 'package':
                            $inputData['package_id'] = $test->id;
                            $class = $transaction->room->class;
                            $this->createTransactionTestsFromPackage($inputData, $class);
                            break;
                        default:
                    }
                }
            }
            DB::commit();
            // return response()->json(['message' => 'Success update test', 'auto_draw' => ($autoDraw || !$isUndrawExists)]);
            return response()->json(['message' => 'Success update test', 'auto_draw' => ($autoDraw)]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }


    /**
     * Function for refresh the test list when user select test when create transaction
     * 
     * @param object $request Request data or form data
     * @param integer $roomClass The room class. E.g. 1 or 2
     * @param array $withoutIds The selected test id, so the table will not shown the selected test
     * 
     * @return json
     */
    public function datatableSelectTest(Request $request, $roomClass, $withoutIds)
    {
        $model = \App\TestPreAnalyticsView::where(function ($q) use ($roomClass) {
            // $q->where('class', $roomClass)->orWhere('class', '0')->orWhereNull('class');
        })->whereNotIn('unique_id', explode(',', $withoutIds))->groupBy('unique_id');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Function when user create transaction or add patient on pre analytics page
     * 
     * @param object $request Request data or form data
     * 
     * @return json
     */
    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $requestData = $request->except(['_method', '_token']);
            $requestData['created_time'] = $request->created_time.' '.date('H:i:s');

            if (!$request->patient_id) {
                // create new patient if user choose the add new patient
                $medrec = $request->medrec;
                $checkMedrec = \App\Patient::where('medrec', $medrec)->exists();
                if ($checkMedrec) {
                    throw new \Exception("Medrec has been used");
                }
                $new_patient = \App\Patient::create($request->all());
                $requestData['patient_id'] = $new_patient->id;
            }

            $requestData['status'] = 0;
            $requestData['note'] = $request->diagnosis;
            $requestData['transaction_id_label'] = $this->getTransactionIdLabel($request);
            $room = \App\Room::findOrFail($requestData['room_id']);

            if ($room->auto_checkin || $room->auto_draw) {
                $prefixDate = date('ymd');
                $countExistingData = \App\Transaction::where('no_lab', 'like', $prefixDate . '%')->count();
                if ($countExistingData == 0) {
                    $countExistingData = 1;
                }
                $trxId = str_pad($countExistingData, 3, '0', STR_PAD_LEFT);
                $check =  \App\Transaction::where('no_lab', $prefixDate . $trxId)->exists();

                while ($check) {
                    $countExistingData += 1;
                    $trxId = str_pad($countExistingData, 3, '0', STR_PAD_LEFT);
                    $check =  \App\Transaction::where('no_lab', $prefixDate . $trxId)->exists();
                }
                $requestData['no_lab'] = $prefixDate . $trxId;
                $requestData['checkin_time'] = Carbon::now();
                $user = Auth::user();
                $requestData['checkin_by'] = $user->id;
            }

            $transaction = \App\Transaction::create($requestData);
            $transactionId = $transaction->id;

            $this->logActivity(
                "Create a pre analytics data with ID $transactionId",
                json_encode($requestData)
            );

            $this->createTransactionTests($transactionId, $requestData);

            DB::commit();
            return response()->json(['message' => 'Create transaction success!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateNote(Request $request)
    {
        try {
            $transaction = \App\Transaction::where('id', $request->transaction_id)->first();
            $transaction->note = $request->note;
            $transaction->save();

            return response()->json(['message' => 'Update transaction note successful!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Delete transaction function
     * 
     * @param integer $id Transaction ID
     * 
     * @return json
     */
    public function deleteTransaction($id)
    {
        try {
            $data = \App\Transaction::findOrFail($id);
            $data->delete();

            DB::table('transaction_tests')->where('transaction_id', $id)->delete();
            DB::table('transaction_bills')->where('transaction_id', $id)->delete();

            $this->logActivity(
                "Delete Transaction with ID $id",
                json_encode($data)
            );

            return response()->json(['message' => 'Transaction with ID ' . $id . ' deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Check in function
     * 
     * @param boolean/integer $isManual The config of room, whether the room config is auto checkin or no.
     */
    public function checkIn($isManual = false, Request $request)
    {
        try {
            $user = Auth::user();
            $transactionId = $request->transaction_id;
            $no_lab = $request->no_lab;
            $prefixDate = date('ymd');
            $transaction = \App\Transaction::findOrFail($transactionId);
            $transaction->check = 1;
            $transaction->checkin_time = Carbon::now();
            $transaction->checkin_by = $user->id;

            $area_id = session('area_id');
            if ($area_id == 'igd') {
                $transaction->is_igd = 1;
            } else {
                $transaction->is_igd = 0;
            }

            if ($isManual) {
                $check = \App\Transaction::where('no_lab', $prefixDate . $no_lab)->exists();
                if ($check) {
                    return response()->json(['message' => 'No Lab has been used!'], 400);
                }

                $transaction->no_lab = $prefixDate . $no_lab;
            } else {
                $countExistingData = \App\Transaction::where('no_lab', 'like', $prefixDate . '%')->count();
                if ($countExistingData == 0) {
                    $countExistingData = 1;
                }

                $trxId = str_pad($countExistingData, 3, '0', STR_PAD_LEFT);
                $check =  \App\Transaction::where('no_lab', $prefixDate . $trxId)->exists();

                while ($check) {
                    $countExistingData += 1;
                    $trxId = str_pad($countExistingData, 3, '0', STR_PAD_LEFT);
                    $check =  \App\Transaction::where('no_lab', $prefixDate . $trxId)->exists();
                }
                $transaction->no_lab = $prefixDate . $trxId;
            }
            // print_r($transaction);

            $transaction->save();

            $this->logActivity(
                "Check in Transaction with Label ID $transaction->no_lab",
                json_encode([])
            );

            return response()->json(['message' => 'Patient successfully checked in with No. Lab: ' . $transaction->no_lab, 'no_lab' => $transaction->no_lab]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Function for all select option form on the select analyzer 
     * 
     * @param integer $testId The test id 
     * 
     * @return text $options The html text for select options on the analyzer selection
     */
    public function analyzerTest($testId)
    {
        $interfacings = \App\Interfacing::where('test_id', $testId)->get();

        $options = '<option value=""></option>';
        foreach ($interfacings as $interfacing) {
            $options .= '<option value="' . $interfacing->analyzer_id . '">' . $interfacing->analyzer->name . '</option>';
        }

        return $options;
    }

    /**
     * When user change the analyzer, make the update query on transaction test
     * 
     * @param integer @transactoinTestId The transaction test id
     * @param object $request The form data or request data
     * 
     * @return json response
     */
    public function updateAnalyzer($transactionTestId, Request $request)
    {
        try {
            $data = \App\TransactionTest::where('id', $transactionTestId)->first();
            $transactionId = $data->transaction_id;
            $transactionTest = \App\TransactionTest::where('transaction_id', $transactionId)->where('verify', 1)->exists();
            if ($transactionTest) {
                throw new \Exception("You can't edit because one of the test is verified");
            }

            //if package, change all analyzer of test on each tests
            if ($data->package_id) {
                \App\TransactionTest::where('transaction_id', $data->transaction_id)
                    ->where('package_id', $data->package_id)
                    ->update([
                        'analyzer_id' => $request->analyzer_id
                    ]);
            } else {
                $data->update(['analyzer_id' => $request->analyzer_id]);
            }

            $this->logActivity(
                "Change the analyzer with transaction test ID $transactionTestId",
                json_encode($request->except(['_method', '_token']))
            );
            return response()->json(['message' => 'Success update analyzer']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Function when user click the draw checkbox in the pre analytics page
     * 
     * @param object $request The form or request data
     * 
     * @return json response message
     */
    public function updateDraw(Request $request)
    {
        try {
            $user = Auth::user();
            $test_ids = explode(',', $request->test_ids);
            $transactionTest = \App\TransactionTest::where('transaction_id', $request->transaction_id)->where('verify', 1)->exists();
            if ($transactionTest) {
                throw new \Exception("You can't edit because one of the test is verified");
            }
            $data_update = [
                'draw_by' => $user->id,
                'draw' => DB::raw('(CASE WHEN draw = NULL THEN 1 ELSE (1 - draw) END)'),
                'draw_time' => DB::raw('CASE WHEN draw = "1" THEN "' . Carbon::now() . '" ELSE NULL END'),
            ];
            if ($request->mode == "draw") {
                $data_update['draw_memo'] = null;
                $data_update['undraw_by'] = null;
                $data_update['undraw_memo'] = null;

                // update draw in transactions 
                $draw_transactions = [
                    'draw' => 1,
                ];
                \App\Transaction::where('id', $request->transaction_id)->update($draw_transactions);
            } else if ($request->mode == "undraw") {
                $data_update['draw'] = 0;
                $data_update['draw_by'] = null;
                $data_update['draw_memo'] = null;
                $data_update['undraw_by'] = $user->id;
                $data_update['undraw_memo'] = $request->reason;

                // update draw in transactions 
                $draw_transactions = [
                    'draw' => 0,
                ];
                \App\Transaction::where('id', $request->transaction_id)->update($draw_transactions);
            }
            \App\TransactionTest::where('transaction_id', $request->transaction_id)->whereIn('test_id', $test_ids)
                ->update($data_update);

            return response()->json(['message' => 'Success update draw status']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Function for when user click draw all or undraw all button in pre analytics page in the specimens section
     * 
     * @param integer $value 1 or 0 of the draw status
     * @param object $request The form data or request data
     * 
     * @return json response message
     */
    public function drawAll($value, Request $request)
    {

        try {
            $user = Auth::user();
            $transactionTest = \App\TransactionTest::where('transaction_id', $request->transaction_id)->where('verify', 1)->exists();
            if ($transactionTest) {
                throw new \Exception("You can't edit because one of the test is verified");
            }

            // !$value means $value = 0

            \App\TransactionTest::where('transaction_id', $request->transaction_id)
                ->where('draw', !$value)
                ->orWhere('draw', null)
                ->update([
                    'draw' => $value,
                    'draw_by' => $user->id,
                    'draw_time' => ($value) ? Carbon::now() : null,
                    'draw_memo' => null,
                    'undraw_by' => null,
                    'undraw_memo' => (!$value) ? $request->reason : null
                ]);

            // update draw in transactions 
            if ($request->mode == "draw") {
                $draw_transactions = [
                    'draw' => 1,
                ];
            } else if ($request->mode == "undraw") {
                $draw_transactions = [
                    'draw' => 0,
                ];
            }

            \App\Transaction::where('id', $request->transaction_id)->update($draw_transactions);

            $this->logActivity(
                "Change the status of all draw specimen with transaction ID $request->transaction_id to $value",
                json_encode($request->except(['_method', '_token']))
            );

            return response()->json(['message' => 'Success update draw status']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 404]);
        }
    }

    /**
     * Function to check status on specimens whether all the specimens has been drawn or not.
     * 
     * @param integer $transactionId The transaction id
     * 
     * @return json response message
     */
    public function isAllDrawn($transactionId)
    {
        try {
            $exists = \App\TransactionTest::where('transaction_id', $transactionId)->where(function ($q) {
                $q->where('draw', '0')->orWhere('draw', null);
            })->exists();
            return response()->json(['message' => $transactionId, 'all_drawn' => !$exists]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * Function for when user create transaction or add patient, it's private function. it will be used only on create function above
     * it will create bulk test based on the selected test when create the transaction
     * 
     * @param integer $transactionId The transaction id
     * @param object $requestData the form or request data
     * 
     * @return void
     */
    private function createTransactionTests($transactionId, $requestData)
    {
        $user = Auth::user();
        $testUniqueIds = explode(',', $requestData['selected_test_ids']);

        $tests = \App\TestPreAnalyticsView::whereIn('unique_id', $testUniqueIds);
        $room = \App\Room::findOrFail($requestData['room_id']);
        $autoDraw = $room->auto_draw;

        if ($tests->count() > 0) {
            $inputData = $requestData;
            $now = Carbon::now();
            foreach ($tests->get() as $test) {
                $inputData['transaction_id'] = $transactionId;
                $inputData['price_id'] = $test->price_id;
                $inputData['group_id'] = $test->group_id;
                $inputData['type'] = $test->type;
                $inputData['test_id'] = NULL;
                $inputData['package_id'] = NULL;
                $inputData['input_time'] = Carbon::now()->toDateTimeString();
                if ($autoDraw) {
                    $inputData['draw'] = true;
                    $inputData['draw_time'] = $now;
                    $inputData['draw_by'] = $user->id;
                }

                //check have default or not
                $checkDefaultAnalyzer = \App\Analyzer::where('group_id', $test->group_id)->where('is_default', 1)->first();
                $analyzerFromInterfacing = DB::table('interfacings')->where('test_id', $test->id)->first();
                if ($checkDefaultAnalyzer) {
                    if (!empty($analyzerFromInterfacing)) {

                        if ($analyzerFromInterfacing->analyzer_id == $checkDefaultAnalyzer->id) {

                            $inputData['analyzer_id'] = $checkDefaultAnalyzer->id;
                        } else {
                            $inputData['analyzer_id'] = $analyzerFromInterfacing->analyzer_id;
                        }
                    } else {

                        $inputData['analyzer_id'] = $checkDefaultAnalyzer->id;
                    }
                } else {
                    $inputData['result_label'] = null;
                    if ($analyzerFromInterfacing) {
                        $inputData['analyzer_id'] = $analyzerFromInterfacing->analyzer_id;
                    }
                }

                $checkDefaultLabel = \App\Result::selectRaw('results.id, results.status as result_status, tests.range_type')->where('test_id', $test->id)->join('tests', 'tests.id', '=', 'results.test_id')->where('is_default', 1)->where('tests.range_type', '=', 'label')->first();
                if ($checkDefaultLabel) {
                    $inputData['result_label'] = $checkDefaultLabel->id;

                    if ($checkDefaultLabel->status == 'normal') {
                        $inputData['result_status'] = 2;
                    } else if ($checkDefaultLabel->status == 'abnormal') {
                        $inputData['result_status'] = 4;
                    } else if ($checkDefaultLabel->status == 'critical') {
                        $inputData['result_status'] = 5;
                    } else {
                        $inputData['result_status'] = 4;
                    }
                } else {
                    $inputData['result_label'] = null;
                }

                switch ($test->type) {
                    case 'single':
                        $inputData['test_id'] = $test->id;
                        $checkTest = \App\TransactionTest::where('transaction_id', $transactionId)->where('test_id', $test->id)->exists();

                        if (!$checkTest) {
                            \App\TransactionTest::create($inputData);

                            // create into transaction bills table
                            DB::table('transaction_bills')
                                ->insert([
                                    'transaction_id' => $transactionId,
                                    'price_id' => $inputData['price_id'],
                                    'test_id' => $inputData['test_id'],
                                    'package_id' => $inputData['package_id'],
                                    'created_at' => Carbon::now(),
                                    'updated_at' => NULL
                                ]);
                        }
                        break;
                    case 'package':
                        $inputData['package_id'] = $test->id;
                        $class = $room->class;
                        $this->createTransactionTestsFromPackage($inputData, $class);
                        break;
                    case 'grand_package':
                        // echo 'id nya adalah : ' . $test->id;
                        // die;
                        $inputData['grand_package_id'] = $test->id;
                        $class = $room->class;
                        $this->createTransactionTestsFromGrandPackage($inputData, $class);
                        break;
                    default:
                }
            }
        }
    }

    /**
     * Private function when user create transaction, it's only use when the user select the package test
     * 
     * @param array $inputData the request data or form data
     * 
     * @return void
     */
    private function createTransactionTestsFromPackage($inputData, $class)
    {
        $tests = \App\PackageTest::where('package_id', $inputData['package_id'])->get();

        foreach ($tests as $test) {
            $inputData['test_id'] = $test->test_id;

            $checkDefaultLabel = \App\Result::selectRaw('results.id, results.status, tests.range_type')->where('test_id', $test->test_id)->join('tests', 'tests.id', '=', 'results.test_id')->where('is_default', 1)->where('tests.range_type', '=', 'label')->first();
            if ($checkDefaultLabel) {
                $inputData['result_label'] = $checkDefaultLabel->id;

                if ($checkDefaultLabel->status == 'normal') {
                    $inputData['result_status'] = 2;
                } else if ($checkDefaultLabel->status == 'abnormal') {
                    $inputData['result_status'] = 4;
                } else if ($checkDefaultLabel->status == 'critical') {
                    $inputData['result_status'] = 5;
                } else {
                    $inputData['result_status'] = 4;
                }
            } else {
                $inputData['result_label'] = null;
            }
            
            // make sure the test only has one and is in the room
            $checkTest = \App\TransactionTest::where('transaction_id', $inputData['transaction_id'])->where('test_id', $inputData['test_id'])->exists();
            if (!$checkTest) {
                \App\TransactionTest::create($inputData);

                // create into transaction bills table
                DB::table('transaction_bills')
                    ->insert([
                        'transaction_id' => $inputData['transaction_id'],
                        'price_id' => $inputData['price_id'],
                        'test_id' => $inputData['test_id'],
                        'package_id' => $inputData['package_id'],
                        'created_at' => Carbon::now(),
                        'updated_at' => NULL
                    ]);
            }
        }
    }

    /**
     * Private function when user create transaction, it's only use when the user select the package test
     * 
     * @param array $inputData the request data or form data
     * 
     * @return void
     */
    private function createTransactionTestsFromGrandPackage($inputData, $class)
    {
        // $tests = \App\PackageTest::where('package_id', $inputData['package_id'])->get();
        $grandPackageId = $inputData['grand_package_id'];

        $tests = DB::table('grand_package_tests')
            ->select('grand_package_tests.*', 'tests.group_id')
            ->leftJoin('tests', 'grand_package_tests.test_id', '=', 'tests.id')
            ->where('grand_package_id', $grandPackageId)
            ->get();

        foreach ($tests as $test) {
            $inputData['test_id'] = $test->test_id;
            $inputData['package_id'] = $test->package_id;
            $inputData['type'] = $test->package_id ? 'package' : 'single';
            $inputData['group_id'] = $test->group_id;

            //check have default or not
            $checkDefaultAnalyzer = \App\Analyzer::where('group_id', $test->group_id)->where('is_default', 1)->first();
            $analyzerFromInterfacing = DB::table('interfacings')->where('test_id', $test->test_id)->first();
            if ($checkDefaultAnalyzer) {
                if (!empty($analyzerFromInterfacing)) {

                    if ($analyzerFromInterfacing->analyzer_id == $checkDefaultAnalyzer->id) {

                        $inputData['analyzer_id'] = $checkDefaultAnalyzer->id;
                    } else {
                        $inputData['analyzer_id'] = $analyzerFromInterfacing->analyzer_id;
                    }
                } else {

                    $inputData['analyzer_id'] = $checkDefaultAnalyzer->id;
                }
            } else {
                $inputData['result_label'] = null;
                if ($analyzerFromInterfacing) {
                    $inputData['analyzer_id'] = $analyzerFromInterfacing->analyzer_id;
                }
            }

            $checkDefaultLabel = \App\Result::selectRaw('results.id, results.status, tests.range_type')->where('test_id', $test->test_id)->join('tests', 'tests.id', '=', 'results.test_id')->where('is_default', 1)->where('tests.range_type', '=', 'label')->first();
            if ($checkDefaultLabel) {
                $inputData['result_label'] = $checkDefaultLabel->id;

                if ($checkDefaultLabel->status == 'normal') {
                    $inputData['result_status'] = 2;
                } else if ($checkDefaultLabel->status == 'abnormal') {
                    $inputData['result_status'] = 4;
                } else if ($checkDefaultLabel->status == 'critical') {
                    $inputData['result_status'] = 5;
                } else {
                    $inputData['result_status'] = 4;
                }
            } else {
                $inputData['result_label'] = null;
            }

            // make sure the test only has one and is in the room
            $checkTest = \App\TransactionTest::where('transaction_id', $inputData['transaction_id'])->where('test_id', $inputData['test_id'])->exists();
            if (!$checkTest) {
                \App\TransactionTest::create($inputData);

                // create into transaction bills table
                DB::table('transaction_bills')
                    ->insert([
                        'transaction_id' => $inputData['transaction_id'],
                        'price_id' => $inputData['price_id'],
                        'test_id' => $inputData['test_id'],
                        'package_id' => $inputData['package_id'],
                        'created_at' => Carbon::now(),
                        'updated_at' => NULL
                    ]);
            }
        }
    }

    /**
     * Private function for set the human readable transaction id
     * 
     * @param object $request the form or request data. It's only use type param tho.
     * 
     * @return string the generated transaction id label
     */
    private function getTransactionIdLabel($request)
    {
        $prefix = '';
        switch ($request->type) {
            case 'rawat_jalan':
                $prefix = 'RWJ';
                break;
            case 'rawat_inap':
                $prefix = 'RWI';
                break;
            case 'igd':
                $prefix = 'IGD';
                break;
            case 'rujukan':
                $prefix = 'RJK';
                break;
            default:
                $prefix = 'TRX';
        }

        $year = date('Y');
        $countExistingData = \App\Transaction::where('transaction_id_label', 'like', $prefix . $year . '%')->count();
        $countExistingData += 1;

        $trxId = str_pad($countExistingData, 7, '0', STR_PAD_LEFT);
        return $prefix . $year . $trxId;
    }

    /**
     * 
     */
    public function checkMedRec($medrec)
    {
        try {
            $checkMedRec = \App\Patient::where('medrec', $medrec)->exists();
            if ($checkMedRec) {
                throw new \Exception("Medical Record has been used");
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * 
     */
    public function editPatientDetails($transactionId)
    {
        try {
            $transaction = \App\Transaction::findOrFail($transactionId);
            return $transaction;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * 
     */
    public function updatePatientDetails(Request $request)
    {
        try {
            $data = $request->all();
            $data['cito'] = $request->cito == true;
            \App\Transaction::findOrFail($request->id)->update($data);
            $transactionTest = \App\TransactionTest::where('transaction_id', $request->id)->where('verify', 1)->exists();
            if ($transactionTest) {
                throw new \Exception("You can't edit because one of the test is verified");
            }
            $transaction = \App\Transaction::findOrFail($request->id);
            return response()->json(['message' => 'Success update patient details', 'data' => $transaction]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * 
     */
    public function goToAnalyticBtn($transactionId)
    {
        try {
            $transaction = \App\Transaction::findOrFail($transactionId);

            if ($transaction->status == '1') {
                throw new \Exception("Transaction has been moved to analytic");
            }

            if ($transaction->no_lab == '' || $transaction->no_lab == null) {
                throw new \Exception("No Lab has not been set");
            }

            $allAnalzerSet = false;
            $transactionTest = \App\TransactionTest::where('transaction_id', $transactionId)->get();

            foreach ($transactionTest as $test) {
                if ($test->analyzer_id == null || $test->analyzer_id == '') {
                    throw new \Exception('Analyzer hasn\'t been set for all');
                }

                if ($test->draw == null || $test->draw == '0') {
                    throw new \Exception('Draw hasn\'t set for all specimen');
                }
            }

            return response()->json(['message' => 'Valid', 'valid' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'valid' => false]);
        }
    }

    /**
     * 
     */
    public function goToAnalytic(Request $request)
    {
        try {
            $now = Carbon::now();
            \App\Transaction::where('id', $request->transaction_id)->update(['status' => 1, 'analytic_time' => $now]);

            return response()->json(['message' => 'Success move to analytics']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessag()], 400);
        }
    }

    public function goToAnalyticBulk(Request $request)
    {
        try {
            $now = Carbon::now();
            \App\Transaction::whereIn('id', $request->transaction_ids)->update(['status' => 1, 'analytic_time' => $now]);

            return response()->json(['message' => 'Success move to analytics']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessag()], 400);
        }
    }

    public function isVerifiedTestExist($transactionId)
    {
        try {
            $exists = \App\TransactionTest::where('transaction_id', $transactionId)->where('verify', 1)->exists();

            return response()->json(['exists' => $exists]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getUser()
    {
        $query = \App\User::selectRaw('users.id as user_id, users.name')->get();
        $userData = $query;

        return response()->json($userData);
    }

    public function updateCitoCheckin(Request $request)
    {
        if ($request->method == "cito") {
            DB::table('transactions')->where('id', $request->transaction_id)->update([
                'cito' => 1
            ]);
        } else {
            DB::table('transactions')->update([
                'cito' => 0
            ]);
        }
    }
}
