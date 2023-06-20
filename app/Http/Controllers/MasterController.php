<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DataTables;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class MasterController extends Controller
{
    protected const COUNT_LIMIT_FOR_DATATABLE = 20000; // limit for re-query the count datatable more than this limit
    protected $masters = [
        'test' => 'App\Test',
        'grand-package' => 'App\GrandPackage',
        'package' => 'App\Package',
        'patient' => 'App\Patient',
        'group' => 'App\Group',
        'analyzer' => 'App\Analyzer',
        'specimen' => 'App\Specimen',
        'doctor' => 'App\Doctor',
        'insurance' => 'App\Insurance',
        'price' => 'App\Price',
        'room' => 'App\Room',
        'range' => 'App\Range',
        'interfacing' => 'App\Interfacing',
        'general_code_test' => 'App\GeneralCodeTest',
        'result' => 'App\Result',
        'formula' => 'App\Formula',
    ];

    protected $masterId = null;

    /**
     * The index function for all master pages, route: '/master/*'
     *
     * @param string $masterData The model of the master
     * @return view
     */
    public function index($masterData)
    {
        try {
            // if the param of masterData is not listed in $masters, thrown 404 exception
            if (!isset($this->masters[$masterData])) {
                throw new \Exception("Not Found");
            }

            $data['masterData'] = $masterData; // the master model in string
            $data['title'] = "Master " . ucwords($masterData);

            return view('dashboard.masters.' . $masterData, $data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * The create function for all master data
     *
     * @param string $masterData The model of the master
     * @param array $request The array of input method
     *
     * @return json response JSON from created master data.
     */
    public function create($masterData, Request $request)
    {
        DB::beginTransaction(); // begin of transaction
        try {
            $validator = $this->masters[$masterData]::validate($request); // run validation of form of every master data
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            switch ($masterData) {
                case 'formula':
                    $test_reference = $request->test_reference;
                    $test_a = $request->test_a;
                    $operation_a = $request->operation_a;
                    $value_a = $request->value_a;
                    $test_b = $request->test_b;
                    $operation_b = $request->operation_b;
                    $value_b = $request->value_b;
                    $test_c = $request->test_c;
                    $operation_c = $request->operation_c;
                    $value_c = $request->value_c;
                    $formulas = $request->formulas;

                    $query_reference = DB::table('tests')->select('name as test_name')->where('id', $test_reference)->first();
                    $reference_name = $query_reference->test_name;

                    $query_a = DB::table('tests')->select('name as test_name')->where('id', $test_a)->first();
                    $a_name = $query_a->test_name;

                    $query_b = DB::table('tests')->select('name as test_name')->where('id', $test_b)->first();
                    $b_name = $query_b->test_name;

                    if ($operation_a == '' || $value_a == null) {
                        $operation_a = null;
                        $value_a = null;
                    }

                    if ($operation_b == '' || $value_b == null) {
                        $operation_b = null;
                        $value_b = null;
                    }

                    if ($test_c != '' || $test_c != null) {
                        $query_c = DB::table('tests')->select('name as test_name')->where('id', $test_c)->first();
                        $c_name = $query_c->test_name;
                    } else {
                        $test_c = null;
                        $c_name = null;
                        $operation_c = null;
                        $value_c = null;
                    }

                    $arrayData = [
                        'test_reference_id' => $test_reference,
                        'test_reference_name' => $reference_name,
                        'a_id' => $test_a,
                        'a_name' => $a_name,
                        'a_operation' => $operation_a,
                        'a_value' => $value_a,
                        'b_id' => $test_b,
                        'b_name' => $b_name,
                        'b_operation' => $operation_b,
                        'b_value' => $value_b,
                        'c_id' => $test_c,
                        'c_name' => $c_name,
                        'c_operation' => $operation_c,
                        'c_value' => $value_c,
                        'formulas' => $formulas,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];

                    \App\Formula::insert($arrayData);

                    break;
                case 'price':
                    $creates = $this->createPrices($request);

                    // $this->logActivity(
                    //     "Create Price data(s)",
                    //     json_encode($request->except(['_method','_token']))
                    // );
                    break;
                case 'test':
                    $createdData = $this->masters[$masterData]::create($this->mapInputs($masterData, $request));

                    $this->logActivity(
                        "Create $masterData with ID $createdData->id",
                        json_encode($createdData)
                    );
                    break;
                case 'package':
                    $createdData = $this->masters[$masterData]::create($this->mapInputs($masterData, $request));
                    // this is for create package in particular, it will be executed if the masterData is package
                    $this->createPackageTest($createdData, $masterData, $request);
                    \App\Price::create([
                        'package_id' => $createdData->id,
                        'type' => 'package',
                        'price' => 0, // set default for price that has no class
                        'class' => 0 // set default for price class that
                    ]);

                    $this->logActivity(
                        "Create $masterData with ID $createdData->id",
                        json_encode($createdData)
                    );
                    break;
                case 'grand-package':
                        $createdData = $this->masters[$masterData]::create($this->mapInputs($masterData, $request));
                        // this is for create package in particular, it will be executed if the masterData is 
                        $this->createGrandPackageTest($createdData, $masterData, $request);
                        // \App\Price::create([
                        //     'package_id' => $createdData->id,
                        //     'type' => 'package',
                        //     'price' => 0, // set default for price that has no class
                        //     'class' => 0 // set default for price class that
                        // ]);
    
                        // $this->logActivity(
                        //     "Create $masterData with ID $createdData->id",
                        //     json_encode($createdData)
                        // );
                        break;
                case 'interfacing':
                    $creates = $this->createInterfacing($request);

                    $this->logActivity(
                        "Create Interfacing data(s)",
                        json_encode($request->except(['_method', '_token']))
                    );
                    break;
                case 'patient':
                    $checkMedRec = \App\Patient::where('medrec', $request->medrec)->exists();
                    if ($checkMedRec) {
                        throw new \Exception("Medrec has been used");
                    }
                    $createdData = $this->masters[$masterData]::create($this->mapInputs($masterData, $request));
                    $this->logActivity(
                        "Create $masterData with ID $createdData->id",
                        json_encode($createdData)
                    );
                    // no break
                default:
                    $createdData = $this->masters[$masterData]::create($this->mapInputs($masterData, $request));
                    $this->logActivity(
                        "Create $masterData with ID $createdData->id",
                        json_encode($createdData)
                    );
            }

            $this->resetDefault('create', $masterData, $request);
            DB::commit(); // commit into DB if successfully created the data into masters.
            return response()->json(['message' => ucwords(str_replace("_", " ", $masterData)) . ' added successfully']);
        } catch (\Exception $e) {
            DB::rollback(); // rollback the database if in the middle way of creation there is any error.
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Function for package master
     * it will create a lot of test based test input form.
     *
     * @param object $createdData The created package data
     * @param string $masterData Data that want to be created
     * @param object $request The request data or form data
     *
     * @return void
     */
    private function createPackageTest($createdData, $masterData, $request)
    {
        if ($masterData != 'package') {
            return; // cancel the operation if the masterdata is not package.
        }
        $data = [];
        if ($request->test_ids != null) {
            $currentTime = date('Y-m-d H:i:s');
            foreach ($request->test_ids as $test_id) {
                // collect the test data before insert into package_tests table
                $data[] = [
                    'test_id' => $test_id,
                    'package_id' => $createdData->id,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ];
            }
        }

        \App\PackageTest::insert($data); // insert all test data in one query
    }

    private function createGrandPackageTest($createdData, $masterData, $request)
    {

        if ($masterData != 'grand-package') {
            return; // cancel the operation if the masterdata is not package.
        }

        $package_data = $request->package_ids;

        $data = [];
        if ($request->test_ids != null) {
            $currentTime = date('Y-m-d H:i:s');
            foreach ($request->test_ids as $test_id) {

                $checkPackageTest = DB::table('package_tests')->where('test_id', $test_id)->first();
                
                if ($checkPackageTest) {
                    $data[] = [
                        'test_id' => $test_id,
                        'grand_package_id' => $createdData->id,
                        'package_id' => $checkPackageTest->package_id,
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime
                    ];
                } else {
                    $data[] = [
                        'test_id' => $test_id,
                        'grand_package_id' => $createdData->id,
                        'package_id' => null,
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime
                    ];
                }
            }
        }

        // die;

        // echo '<pre>';
        // print_r($data);
        // die;

        \App\GrandPackageTest::insert($data); // insert all test data in one query
    }

    /**
     * Function for master data Price
     * it will be called if the master data that want to be created is price
     *
     * @param object $request Form data or request data
     * @return void
     */
    private function createPrices($request)
    {
        $data = [];
        $currentTime = date('Y-m-d H:i:s'); // reserved the exact time for all database insert operation later (it was needed put here to prevent different times among rows)

        if ($request->test_id == null && $request->package_id == null) {
            throw new \Exception("Test or package has not been set");
        }
        foreach ($request->class_price as $class_price) {
            // check if the class of price exist or not
            $checkExistsClass = \App\Price::where('class', $class_price['class'])->where('type', $request->type);
            if ($request->type == 'test') {
                $checkExistsClass = $checkExistsClass->where('test_id', $request->test_id)->exists();
            } else {
                $checkExistsClass = $checkExistsClass->where('package_id', $request->package_id)->exists();
            }

            // if there's class of price exists with the package or test id given above, the operation will be cancel and exception will be thrown.
            if ($checkExistsClass) {
                throw new \Exception("The price for that class already set!");
            }

            // collect all data on bulk input for test/package
            $data[] = [
                'class' => $class_price['class'],
                'price' => str_replace(',', '', $class_price['price']),
                'test_id' => $request->type == 'test' ? $request->test_id : null,
                'type' => $request->type,
                'package_id' => $request->type == 'package' ? $request->package_id : null,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ];
        }

        \App\Price::insert($data); // insert all collected data above into one query call.
    }

    /**
     * Function for master data Interfacing
     * it will be called if the master data that want to be created is interfacing
     *
     * @param object $request The form data or request data which user given.
     * @return void
     */
    private function createInterfacing($request)
    {
        $data = [];
        $currentTime = date('Y-m-d H:i:s'); // reserved current time for all operation later.
        foreach ($request->test_code as $test_code) {
            // check if the code, test, and analyzer given is exists or no
            $checkExistsClass = \App\Interfacing::where('code', $test_code['code'])->where('test_id', $test_code['test_id'])->where('analyzer_id', $request->analyzer_id)->exists();

            // throw exception if there's existing data
            if ($checkExistsClass) {
                throw new \Exception("The data interfacing for that code already set!");
            }

            // collect all data from bulk input
            $data[] = [
                'code' => $test_code['code'],
                'test_id' => $test_code['test_id'],
                'analyzer_id' => $request->analyzer_id,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ];
        }

        \App\Interfacing::insert($data); // insert collected data with one query call.
    }

    /**
     * Function for all master data edit function
     *
     * @param string $masterData The master data
     * @param integer/string $id The master data id
     *
     * @return json The master data based on id given or error message if the operation failed
     */
    public function edit($masterData, $id)
    {
        try {
            $data = $this->masters[$masterData]::findOrFail($id);

            return $data;
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Function for all master data update function
     *
     * @param string $masterData The master data
     * @param object $request The form data or request data given.
     *
     * @return json message of success or failed
     */
    public function update($masterData, Request $request)
    {
        // echo $request->format_diff_count;
        // die;
        DB::beginTransaction(); // begin of transaction database
        try {
            $validator = $this->masters[$masterData]::validate($request); // validate the input form
            if ($validator->fails()) { // throw exception if validation fails
                throw new \Exception($validator->errors());
            }

            $this->resetDefault('update', $masterData, $request);
            // update the data in database
            $this->masters[$masterData]::findOrFail($request->id)
                ->update($this->mapInputs($masterData, $request));

            // this function is only for package masterdata
            $this->updatePackageTest($masterData, $request);

            $this->logActivity(
                "Update $masterData with ID $request->id",
                json_encode($request->except(['_method', '_token']))
            );

            DB::commit();
            return response()->json(['message' => ucwords(str_replace("_", " ", $masterData)) . ' updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    private function resetDefault($action = 'create', $masterData, $request)
    {
        if ($masterData == 'room' || $masterData == 'insurance' || $masterData == 'doctor') {
            if ($request->is_default) {
                $master = $this->masters[$masterData]::where('is_default', true)->update(['is_default' => false]);

                if ($action == 'create') {
                    $this->masters[$masterData]::orderBy('id','desc')->first()->update(['is_default' => true]);
                } else {
                    $this->masters[$masterData]::findOrFail($request->id)->update(['is_default' => true]);
                }
            }
        }   
    }

    /**
     * Function for update package test only
     *
     * @param string $masterData the masterdata model name
     * @param object $request Form data or requet data given.
     *
     * @return void
     */
    private function updatePackageTest($masterData, $request)
    {
        if ($masterData != 'package') {
            return; // operation will be canceled if the masterdata is not package.
        }

        $data = [];
        if ($request->test_ids != null) {
            \App\PackageTest::where('package_id', $request->id)->delete(); // delete previous data because we will be doing update for all data

            $currentTime = date('Y-m-d H:i:s');
            foreach ($request->test_ids as $test_id) {
                // collect all master data before insertion
                $data[] = [
                    'test_id' => $test_id,
                    'package_id' => $request->id,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ];
            }
        }

        \App\PackageTest::insert($data); // insert package test in one query call.
    }

    /**
     * Function for deletion for all master data
     *
     * @param string $masterData The master data model name
     * @param integer/string $id The id of master data
     *
     * @return json The message of success or failed
     */
    public function delete($masterData, $id)
    {
        try {
            $this->validateDelete($masterData, $id); // call validate delete first to check it the data that want to be deleted has been used by others or no.
            $data = $this->masters[$masterData]::findOrFail($id);
            $data->delete();

            $this->logActivity(
                "Delete $masterData with ID $id",
                json_encode($data)
            );

            return response()->json(['message' => ucwords($masterData) . ' deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Function to check data has been used or no before deletion
     *
     * @param string $masterData the master data model name
     * @param integer/string $id the master data model id
     *
     * @return void
     */
    private function validateDelete($masterData, $id)
    {
        $exists = [];
        switch ($masterData) {
                /**
             * using $exists array to collect the utilization of the master model.
             */
            case 'test':
                $exists[] = \App\PackageTest::where('test_id', $id)->exists();
                $exists[] = \App\TransactionTest::where('test_id', $id)->exists();
                break;
            case 'group':
                $exists[] = \App\Analyzer::where('group_id', $id)->exists();
                break;
            case 'package':
                $exists[] = \App\Price::where('package_id', $id)->exists();
                break;
            case 'grand-package':
                $exists[] = \App\Price::where('grand_package_id', $id)->exists();
                // $exists[] = \App\GrandPackageTest::where('grand_package_id', $id)->exists();
                DB::table('grand_package_tests')->where('grand_package_id', $id)->delete();

                break;
            case 'interfacing':
                $interfacing = \App\Interfacing::findOrFail($id);
                $exists[] = \App\TransactionTest::where('test_id', $interfacing->test_id)->exists();
                break;
            case 'room':
                $exists[] = \App\Transaction::where('room_id', $id)->exists();
                // no break
                break;
            case 'result':
                $exists[] = \App\TransactionTest::where('result_label', $id)->exists();
                break;
            case 'price':
                $exists[] = \App\TransactionTest::where('price_id', $id)->exists();
            default:
                $exists[] = false;
        }
        if (in_array(true, $exists)) { // if in $exists array there's true value, throw an exception.
            throw new \Exception("You can't delete this data, because this data has been used");
        }
    }

    /**
     * Preparing the data for the DataTables for all master data
     *
     * @param string $masterData The model of the master
     * @param string $with The relation model of the masterData, e.g. "group,specimen" or just "group"
     * @return json of DataTables
     */
    public function datatable($masterData, $with = null)
    {
        /**
         * STORING COUNT CACHE IF THE ROWS COUNT MORE THAN THE LIMIT SETTINGS (20.000 rows) for all master data
         * it super necessary because it improves performance significantly
         *
         * it will execute query for only once in 10 minutes interval.
         * So, if user open the page or going to next/prev page for the second and more times below 10 minutes interval, it will just use the cache instead of execute the same query again
         * COUNT query is expensive command and too resource demanding, this is super necessary to implement.
         */
        if (Cache::has($masterData . '_count')) {
            $count = Cache::get($masterData . '_count'); // if in the cache memory there is a data that datatable needed, it will get it instead of execute count query.
        } else {
            $count = $this->masters[$masterData]::count(); // execute count query first for datatable count
            if ($count > MasterController::COUNT_LIMIT_FOR_DATATABLE) {
                Cache::put($masterData . '_count', $count, 600); // store count value in cache
            }
        }

        $model = $this->masters[$masterData]::query(); // reserved the query model (it's not executing query, tho)
        if ($with) { // if $with params not null, the model will bring related model to the reserved model above
            // with() method is super handy to improve performance, because every row doesn't need to execute to query to related model. it will be treated as lazy load.
            $withModel = explode(',', $with);
            $model = $model->with($withModel);
        }

        return DataTables::of($model)
            ->setTotalRecords($count)
            // ->skipTotalRecords()
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * The datatable for range master data
     *
     * @param string/integer $testId the test_id of range
     */
    public function rangeDatatable($testId)
    {
        if (Cache::has('range_count')) {
            $count = Cache::get('range_count');
        } else {
            $count = \App\Range::where('test_id', $testId)->count();
            if ($count > MasterController::COUNT_LIMIT_FOR_DATATABLE) {
                Cache::put('range_count', $count, 600);
            }
        }

        $model = \App\Range::where('test_id', $testId);
        return DataTables::of($model)
            ->setTotalRecords($count)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function testRangeDatatable()
    {
        $model = \App\Test::where('range_type', 'number');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * 
     */
    public function resultRangeDatatable($testId)
    {
        if (Cache::has('result_range_count')) {
            $count = Cache::get('result_range_count');
        } else {
            $count = \App\Result::where('test_id', $testId)->count();
            if ($count > MasterController::COUNT_LIMIT_FOR_DATATABLE) {
                Cache::put('result_range_count', $count, 600);
            }
        }

        $model = \App\Result::where('test_id', $testId)->orderBy('created_at', 'asc');
        return DataTables::of($model)
            ->setTotalRecords($count)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    /**
     *
     */
    public function testLabelDatatable()
    {
        if (Cache::has('test_label_count')) {
            $count = Cache::get('test_label_count');
        } else {
            $count = \App\Test::where('range_type', 'label')->count();
            if ($count > MasterController::COUNT_LIMIT_FOR_DATATABLE) {
                Cache::put('test_label_count', $count, 600);
            }
        }

        $model = \App\Test::where('range_type', 'label');
        return Datatables::of($model)
            ->setTotalRecords($count)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Function for select option form on the server side rendering.
     *
     * @param string $masterData the master data modmel name
     * @param string $searchKey the searchKey that will be used on search query param.
     * @param string @params the extended params that perhaps needed on the special occasion.
     * @param object $request the request data ['query'] param
     *
     * @return json the master data that was collected based on query param search
     */
    public function selectOptions($masterData, $searchKey, $params = null, Request $request)
    {
        try {
            switch ($masterData) {
                case 'room':
                    $data = $this->masters[$masterData]::selectRaw("id, " . $searchKey . " as name, class")
                        ->where('room', 'LIKE', '%' . $request->input('query') . '%')
                        ->where('type', $params)
                        ->take(150)->get();
                    break;
                case 'test':
                    $data = $this->masters[$masterData]::selectRaw("tests.id as id, tests.name as name, GROUP_CONCAT(class SEPARATOR ', ') as classes, tests.initial")
                        ->leftJoin('prices', 'tests.id', '=', 'prices.test_id')
                        ->where('tests.is_active', 1)
                        ->where($searchKey, 'LIKE', '%' . $request->input('query') . '%')
                        // ->where(function ($q) {
                        //     $q->where('prices.class', '=', 0)->orWhere('prices.class', '=' , 0);
                        // })
                        ->groupBy(['tests.id', 'tests.name', 'tests.initial'])
                        ->take(150)->get();
                    break;
                case 'package':
                    $data = $this->masters[$masterData]::selectRaw("packages.id as id, packages.name as name, GROUP_CONCAT(class SEPARATOR ', ') as classes")
                        ->leftJoin('prices', 'packages.id', '=', 'prices.test_id')
                        ->where($searchKey, 'LIKE', '%' . $request->input('query') . '%')
                        // ->where(function ($q) {
                        //     $q->where('prices.class', '=', 0)->orWhereNull('prices.class');
                        // })
                        ->groupBy(['packages.id', 'packages.name'])
                        ->take(150)->get();
                    break;
                case 'patient':
                    $req = $request->input('query');
                    $data = $this->masters[$masterData]::selectRaw("id, " . $searchKey . " as name, medrec")
                        ->where(function ($q) use ($req) {
                            $q->where('name', 'LIKE', '%' . $req . '%')
                                ->orwhere('medrec', 'LIKE', '%' . $req . '%');
                        })
                        ->take(150)->get();
                    break;
                default:
                    $data = $this->masters[$masterData]::selectRaw('id, ' . $searchKey . ' as name')
                        ->where($searchKey, 'LIKE', '%' . $request->input('query') . '%')
                        ->take(150)->get();
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Function for input mapping for all master data inputs before insert/update method.
     *
     * the particular mapping method for now is only for master data: test, room, price, & package. for others will use request as it is.
     * @param string $masterData the master data model name
     * @param object $request the form/request data given.
     *
     * @return array the mapped data
     */
    private function mapInputs($masterData, $request)
    {
        $data = array();
        switch ($masterData) {
            case 'test':
                if (!$request->normal_notes || (!isset($request->normal_notes)) || $request->normal_notes == null) {
                    return $request->except(['normal_notes']);
                }
                return $request->all();
            case 'room': // room need custom mapping for checkbox
                $data = $request->all();
                $data['auto_checkin'] = $request->auto_checkin == 1;
                $data['auto_draw'] = $request->auto_draw == 1;
                $data['auto_undraw'] = $request->auto_undraw == 1;
                $data['auto_nolab'] = $request->auto_nolab == 1;
                break;
            case 'price':
                $data = $request->all();
                $data['price'] = str_replace(',', '', $request->price);
                $data['test_id'] = $request->type == 'test' ? $request->test_id : null;
                $data['package_id'] = $request->type == 'package' ? $request->package_id : null;
                break;
            case 'package':
                $data = $request->all();
                $data['group_id'] = $request->group_id == '' ? null : $request->group_id;
                break;
            default:
                return $request->all();
        }

        return $data;
    }

    /**
     * Function for get test package on master package page
     *
     * @param string $packageIds
     *
     * @return json the data of package test
     */
    public function getTestPackage($packageIds)
    {
        $pIds = explode(',', $packageIds);
        $data = \App\PackageTest::whereIn('package_id', $pIds)->get();
        return response()->json($data);
    }

    public function active($masterData, $id, $isActive)
    {
        try {
            DB::table('tests')
                ->where('id', $id)
                ->update(
                    [
                        'is_active' => $isActive,
                    ]
                );
            return response()->json(['message' => 'change successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function default($masterData, $id, $default)
    {
        try {
            //set selected


            //set not default to rest of same group
            switch ($masterData) {
                case 'analyzer':
                    $master = DB::table('analyzers')->where('id', $id)->first();

                    DB::table('analyzers')
                        ->where('group_id', $master->group_id)
                        ->update(
                            [
                                'is_default' => 0,
                            ]
                        );

                    DB::table('analyzers')
                        ->where('id', $id)
                        ->update(
                            [
                                'is_default' => !$default,
                            ]
                        );
                    break;
                case 'result':
                    $master = DB::table('results')->where('id', $id)->first();

                    DB::table('results')
                        ->where('test_id', $master->test_id)
                        ->update(
                            [
                                'is_default' => 0,
                            ]
                        );

                    DB::table('results')
                        ->where('id', $id)
                        ->update(
                            [
                                'is_default' => !$default,
                            ]
                        );
                    break;
                default:
                    break;
            }
            return response()->json(['message' => 'change successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}